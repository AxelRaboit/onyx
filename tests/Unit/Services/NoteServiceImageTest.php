<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Note;
use App\Services\NoteImageService;
use App\Services\NoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithImageDirectory;

class NoteServiceImageTest extends TestCase
{
    use RefreshDatabase;
    use WithImageDirectory;

    private NoteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NoteService(new NoteImageService);
        $this->setUpImageDirectory();
    }

    protected function tearDown(): void
    {
        $this->tearDownImageDirectory();
        parent::tearDown();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function imageUrl(string $filename): string
    {
        return route('notes.images.serve', ['filename' => $filename]);
    }

    // ── update() — orphan cleanup ─────────────────────────────────────────────

    public function test_update_deletes_image_removed_from_content(): void
    {
        $filename = 'test_removed.jpg';
        $path     = $this->createTestImage($filename);

        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => '![img](' . $this->imageUrl($filename) . ')',
        ]);

        $this->service->update($note, ['content' => 'no images here']);

        $this->assertFileDoesNotExist($path);
    }

    public function test_update_keeps_image_still_present_in_content(): void
    {
        $filename = 'test_kept.jpg';
        $path     = $this->createTestImage($filename);
        $url      = $this->imageUrl($filename);

        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => '![img](' . $url . ')',
        ]);

        $this->service->update($note, ['content' => '![img](' . $url . ') still here']);

        $this->assertFileExists($path);
    }

    public function test_update_deletes_only_removed_images_when_multiple_exist(): void
    {
        $kept    = 'test_multi_kept.jpg';
        $removed = 'test_multi_removed.jpg';

        $keptPath    = $this->createTestImage($kept);
        $removedPath = $this->createTestImage($removed);

        $oldContent = '![a](' . $this->imageUrl($kept) . ') ![b](' . $this->imageUrl($removed) . ')';
        $newContent = '![a](' . $this->imageUrl($kept) . ')';

        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => $oldContent,
        ]);

        $this->service->update($note, ['content' => $newContent]);

        $this->assertFileExists($keptPath);
        $this->assertFileDoesNotExist($removedPath);
    }

    public function test_update_does_not_delete_external_image_urls(): void
    {
        $filename = 'test_external_kept.jpg';
        $path     = $this->createTestImage($filename);

        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => '![ext](https://example.com/photo.jpg)',
        ]);

        // External URL should never match the pattern and the local file must remain untouched
        $this->service->update($note, ['content' => '']);

        $this->assertFileExists($path);
    }

    public function test_update_handles_content_without_images(): void
    {
        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => 'plain text, no images',
        ]);

        $this->service->update($note, ['content' => 'still plain text']);

        $this->assertTrue(true);
    }

    // ── delete() — full cleanup ───────────────────────────────────────────────

    public function test_delete_removes_all_images_referenced_in_note(): void
    {
        $file1 = 'test_del_1.jpg';
        $file2 = 'test_del_2.jpg';

        $path1 = $this->createTestImage($file1);
        $path2 = $this->createTestImage($file2);

        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => '![a](' . $this->imageUrl($file1) . ') ![b](' . $this->imageUrl($file2) . ')',
        ]);

        $this->service->delete($note);

        $this->assertFileDoesNotExist($path1);
        $this->assertFileDoesNotExist($path2);
    }

    public function test_delete_handles_note_without_images(): void
    {
        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => 'just text',
        ]);

        $this->service->delete($note);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_delete_ignores_missing_file_without_throwing(): void
    {
        $note = Note::factory()->create([
            'user_id' => $this->makeUser()->id,
            'content' => '![img](' . $this->imageUrl('test_ghost.jpg') . ')',
        ]);

        $this->service->delete($note);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
