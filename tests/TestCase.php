<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function createRoles(): void
    {
        Role::firstOrCreate(['name' => 'ROLE_DEV',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'ROLE_USER', 'guard_name' => 'web']);
    }

    protected function makeUser(): User
    {
        return User::factory()->create();
    }

    protected function makeDevUser(): User
    {
        $this->createRoles();
        $user = $this->makeUser();
        $user->assignRole('ROLE_DEV');

        return $user;
    }
}
