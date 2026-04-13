<?php

declare(strict_types=1);

namespace App\Enums\ApplicationParameter;

enum OnyxApplicationParameterEnum: string
{
    case RegistrationEnabled = 'registration_enabled';

    public function getDescription(): string
    {
        return match ($this) {
            self::RegistrationEnabled => 'Ouvrir ou fermer les inscriptions (0 = fermées, 1 = ouvertes)',
        };
    }

    public function getDefaultValue(): string
    {
        return match ($this) {
            self::RegistrationEnabled => '1',
        };
    }
}
