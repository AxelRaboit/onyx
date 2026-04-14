<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Traits\WithImageDirectory;

class NoteImageTest extends TestCase
{
    use RefreshDatabase;
    use WithImageDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpImageDirectory();
    }

    protected function tearDown(): void
    {
        $this->tearDownImageDirectory();
        parent::tearDown();
    }

    // ── Auth guards ───────────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_upload(): void
    {
        $this->postJson('/notes/images')->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_serve(): void
    {
        $this->get('/notes/images/some-file.jpg')->assertRedirect('/login');
    }

    // ── Upload ────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_upload_image(): void
    {
        $user = $this->makeUser();
        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

        $response = $this->actingAs($user)
            ->postJson('/notes/images', ['image' => $file]);

        $response->assertOk()->assertJsonStructure(['url']);

        $url      = $response->json('url');
        $filename = basename(parse_url($url, PHP_URL_PATH));

        $this->assertStringContainsString('/notes/images/', $url);
        $this->assertStringEndsWith('.webp', $filename);
        $this->assertFileExists($this->imageDir . '/' . $filename);

        @unlink($this->imageDir . '/' . $filename);
    }

    public function test_upload_rejects_missing_file(): void
    {
        $this->actingAs($this->makeUser())
            ->postJson('/notes/images')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    }

    public function test_upload_rejects_non_image_file(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($this->makeUser())
            ->postJson('/notes/images', ['image' => $file])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    }

    public function test_upload_rejects_file_exceeding_size_limit(): void
    {
        $file = UploadedFile::fake()->image('big.jpg')->size(11_000);

        $this->actingAs($this->makeUser())
            ->postJson('/notes/images', ['image' => $file])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['image']);
    }

    // ── Serve ─────────────────────────────────────────────────────────────────

    public function test_serve_returns_image_with_correct_content_type(): void
    {
        $filename  = 'test_serve.webp';
        $fakeImage = UploadedFile::fake()->image('source.jpg', 10, 10);

        // Simulate what NoteImageService::store() produces: a webp file
        (new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver))
            ->decode($fakeImage->getRealPath())
            ->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 80))
            ->save($this->imageDir . '/' . $filename);

        $this->actingAs($this->makeUser())
            ->get('/notes/images/' . $filename)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp');
    }

    public function test_serve_returns_404_for_missing_file(): void
    {
        $this->actingAs($this->makeUser())
            ->get('/notes/images/nonexistent.jpg')
            ->assertNotFound();
    }

    public function test_any_authenticated_user_can_serve_an_image(): void
    {
        $filename  = 'test_cross_user.jpg';
        $fakeImage = UploadedFile::fake()->image($filename, 10, 10);
        copy($fakeImage->getRealPath(), $this->imageDir . '/' . $filename);

        // A different user than the one who uploaded can still view it
        $this->actingAs($this->makeUser())
            ->get('/notes/images/' . $filename)
            ->assertOk();
    }
}
