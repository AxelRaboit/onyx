<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ApplicationParameter\OnyxApplicationParameterEnum;
use App\Models\ApplicationParameter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationParameterCommandTest extends TestCase
{
    use RefreshDatabase;

    // ── Creates missing ───────────────────────────────────────────────────────

    public function test_command_creates_missing_parameters(): void
    {
        $this->artisan('onyx:application-parameter')->assertSuccessful();

        foreach (OnyxApplicationParameterEnum::cases() as $case) {
            $this->assertDatabaseHas('application_parameters', [
                'key'   => $case->value,
                'value' => $case->getDefaultValue(),
            ]);
        }
    }

    public function test_command_does_not_overwrite_existing_values(): void
    {
        ApplicationParameter::create([
            'key'         => OnyxApplicationParameterEnum::RegistrationEnabled->value,
            'value'       => '0',
            'description' => OnyxApplicationParameterEnum::RegistrationEnabled->getDescription(),
        ]);

        $this->artisan('onyx:application-parameter')->assertSuccessful();

        $this->assertDatabaseHas('application_parameters', [
            'key'   => OnyxApplicationParameterEnum::RegistrationEnabled->value,
            'value' => '0', // not overwritten with default '1'
        ]);
    }

    // ── Updates descriptions ──────────────────────────────────────────────────

    public function test_command_updates_description_when_changed(): void
    {
        ApplicationParameter::create([
            'key'         => OnyxApplicationParameterEnum::RegistrationEnabled->value,
            'value'       => '1',
            'description' => 'Old description',
        ]);

        $this->artisan('onyx:application-parameter')->assertSuccessful();

        $this->assertDatabaseHas('application_parameters', [
            'key'         => OnyxApplicationParameterEnum::RegistrationEnabled->value,
            'description' => OnyxApplicationParameterEnum::RegistrationEnabled->getDescription(),
        ]);
    }

    public function test_command_does_not_update_when_description_unchanged(): void
    {
        $case = OnyxApplicationParameterEnum::RegistrationEnabled;
        ApplicationParameter::create([
            'key'         => $case->value,
            'value'       => '1',
            'description' => $case->getDescription(),
        ]);

        // Run twice — second run should produce no output for this parameter
        $this->artisan('onyx:application-parameter')
            ->doesntExpectOutputToContain($case->value)
            ->assertSuccessful();
    }

    // ── Deletes obsolete ──────────────────────────────────────────────────────

    public function test_command_deletes_obsolete_parameters(): void
    {
        ApplicationParameter::create(['key' => 'obsolete_param', 'value' => 'old']);

        $this->artisan('onyx:application-parameter')->assertSuccessful();

        $this->assertDatabaseMissing('application_parameters', ['key' => 'obsolete_param']);
    }

    // ── Dry-run ───────────────────────────────────────────────────────────────

    public function test_dry_run_does_not_create_parameters(): void
    {
        $this->artisan('onyx:application-parameter', ['--dry-run' => true])->assertSuccessful();

        $this->assertDatabaseEmpty('application_parameters');
    }

    public function test_dry_run_does_not_delete_obsolete_parameters(): void
    {
        ApplicationParameter::create(['key' => 'obsolete_param', 'value' => 'old']);

        $this->artisan('onyx:application-parameter', ['--dry-run' => true])->assertSuccessful();

        $this->assertDatabaseHas('application_parameters', ['key' => 'obsolete_param']);
    }

    // ── Output & exit code ────────────────────────────────────────────────────

    public function test_command_outputs_created_and_deleted_counts(): void
    {
        $expectedCreated = count(OnyxApplicationParameterEnum::cases());

        $this->artisan('onyx:application-parameter')
            ->expectsOutputToContain("{$expectedCreated} créé(s)")
            ->assertSuccessful();
    }

    public function test_command_reports_zero_changes_when_already_synced(): void
    {
        $this->artisan('onyx:application-parameter')->assertSuccessful();

        // Second run: everything already in sync
        $this->artisan('onyx:application-parameter')
            ->expectsOutputToContain('0 créé(s), 0 supprimé(s)')
            ->assertSuccessful();
    }
}
