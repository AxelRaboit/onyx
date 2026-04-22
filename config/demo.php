<?php

declare(strict_types=1);

return [
    'email'           => env('DEMO_USER_EMAIL', 'demo@onyx.app'),
    'password'        => env('DEMO_USER_PASSWORD'),
    'enabled'         => env('DEMO_ENABLED', false),
    'access_password' => env('DEMO_ACCESS_PASSWORD'),
];
