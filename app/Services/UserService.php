<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function createForAdmin(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'locale' => $data['locale'],
        ]);

        $role = Role::firstOrCreate(['name' => 'ROLE_USER', 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }

    public function updateForAdmin(User $user, array $data): void
    {
        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'locale' => $data['locale'],
        ];

        if (! empty($data['password'])) {
            $attributes['password'] = Hash::make($data['password']);
        }

        $user->update($attributes);
    }
}
