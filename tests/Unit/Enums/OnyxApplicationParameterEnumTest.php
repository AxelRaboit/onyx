<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ApplicationParameter\OnyxApplicationParameterEnum;
use PHPUnit\Framework\TestCase;

class OnyxApplicationParameterEnumTest extends TestCase
{
    public function test_all_cases_have_a_description(): void
    {
        foreach (OnyxApplicationParameterEnum::cases() as $case) {
            $this->assertNotEmpty($case->getDescription(), "Case {$case->name} has no description.");
        }
    }

    public function test_all_cases_have_a_default_value(): void
    {
        foreach (OnyxApplicationParameterEnum::cases() as $case) {
            $this->assertNotNull($case->getDefaultValue(), "Case {$case->name} has no default value.");
        }
    }

    public function test_registration_enabled_key(): void
    {
        $this->assertSame('registration_enabled', OnyxApplicationParameterEnum::RegistrationEnabled->value);
    }

    public function test_registration_enabled_default_is_one(): void
    {
        $this->assertSame('1', OnyxApplicationParameterEnum::RegistrationEnabled->getDefaultValue());
    }

    public function test_registration_enabled_has_description(): void
    {
        $description = OnyxApplicationParameterEnum::RegistrationEnabled->getDescription();
        $this->assertNotEmpty($description);
    }
}
