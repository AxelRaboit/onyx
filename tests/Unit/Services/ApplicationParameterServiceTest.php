<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ApplicationParameter;
use App\Services\ApplicationParameterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationParameterServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApplicationParameterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApplicationParameterService;
    }

    // ── get() ─────────────────────────────────────────────────────────────────

    public function test_get_returns_null_when_key_does_not_exist(): void
    {
        $this->assertNull($this->service->get('missing_key'));
    }

    public function test_get_returns_default_when_key_does_not_exist(): void
    {
        $this->assertSame('fallback', $this->service->get('missing_key', 'fallback'));
    }

    public function test_get_returns_stored_value(): void
    {
        ApplicationParameter::create(['key' => 'my_key', 'value' => 'hello']);

        $this->assertSame('hello', $this->service->get('my_key'));
    }

    // ── set() ─────────────────────────────────────────────────────────────────

    public function test_set_creates_parameter_when_absent(): void
    {
        $this->service->set('new_key', 'new_value');

        $this->assertDatabaseHas('application_parameters', ['key' => 'new_key', 'value' => 'new_value']);
    }

    public function test_set_updates_existing_parameter(): void
    {
        ApplicationParameter::create(['key' => 'existing', 'value' => 'old']);

        $this->service->set('existing', 'updated');

        $this->assertDatabaseHas('application_parameters', ['key' => 'existing', 'value' => 'updated']);
        $this->assertSame(1, ApplicationParameter::where('key', 'existing')->count());
    }

    public function test_set_can_store_null(): void
    {
        $this->service->set('nullable_key', null);

        $this->assertDatabaseHas('application_parameters', ['key' => 'nullable_key', 'value' => null]);
    }

    // ── getInt() ──────────────────────────────────────────────────────────────

    public function test_get_int_returns_default_when_absent(): void
    {
        $this->assertSame(42, $this->service->getInt('missing', 42));
    }

    public function test_get_int_casts_string_to_int(): void
    {
        ApplicationParameter::create(['key' => 'count', 'value' => '7']);

        $this->assertSame(7, $this->service->getInt('count'));
    }

    // ── getBool() ─────────────────────────────────────────────────────────────

    public function test_get_bool_returns_true_for_one(): void
    {
        ApplicationParameter::create(['key' => 'flag', 'value' => '1']);

        $this->assertTrue($this->service->getBool('flag'));
    }

    public function test_get_bool_returns_false_for_zero(): void
    {
        ApplicationParameter::create(['key' => 'flag', 'value' => '0']);

        $this->assertFalse($this->service->getBool('flag'));
    }

    public function test_get_bool_returns_default_when_absent(): void
    {
        $this->assertTrue($this->service->getBool('missing', true));
        $this->assertFalse($this->service->getBool('missing', false));
    }

    // ── getFloat() ────────────────────────────────────────────────────────────

    public function test_get_float_casts_string_to_float(): void
    {
        ApplicationParameter::create(['key' => 'ratio', 'value' => '3.14']);

        $this->assertEqualsWithDelta(3.14, $this->service->getFloat('ratio'), 0.0001);
    }

    public function test_get_float_returns_default_when_absent(): void
    {
        $this->assertEqualsWithDelta(1.5, $this->service->getFloat('missing', 1.5), 0.0001);
    }

    // ── all() ─────────────────────────────────────────────────────────────────

    public function test_all_returns_empty_array_when_no_parameters(): void
    {
        $this->assertSame([], $this->service->all());
    }

    public function test_all_returns_parameters_ordered_by_key(): void
    {
        ApplicationParameter::create(['key' => 'z_key', 'value' => '1', 'description' => null]);
        ApplicationParameter::create(['key' => 'a_key', 'value' => '2', 'description' => 'desc']);

        $result = $this->service->all();

        $this->assertCount(2, $result);
        $this->assertSame('a_key', $result[0]['key']);
        $this->assertSame('z_key', $result[1]['key']);
    }

    public function test_all_includes_key_value_and_description(): void
    {
        ApplicationParameter::create(['key' => 'my_param', 'value' => 'val', 'description' => 'My desc']);

        $result = $this->service->all();

        $this->assertSame([
            'key'         => 'my_param',
            'value'       => 'val',
            'description' => 'My desc',
        ], $result[0]);
    }
}
