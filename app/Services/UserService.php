<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role as RoleEnum;
use App\Enums\SortDirection;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

        $role = Role::firstOrCreate(['name' => RoleEnum::User->value, 'guard_name' => 'web']);
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

    public function paginateForAdmin(?string $search, int $perPage = 20): LengthAwarePaginator
    {
        return User::with('roles')
            ->when($search, fn ($q) => $q->where('name', 'like', sprintf('%%%s%%', $search))->orWhere('email', 'like', sprintf('%%%s%%', $search)))
            ->orderBy('created_at', SortDirection::Descending->value)
            ->paginate($perPage);
    }

    public function toggleRole(User $user): void
    {
        if ($user->hasRole(RoleEnum::Dev->value)) {
            $user->removeRole(RoleEnum::Dev->value);
            $user->assignRole(RoleEnum::User->value);
        } else {
            $user->removeRole(RoleEnum::User->value);
            $user->assignRole(RoleEnum::Dev->value);
        }
    }

    public function deleteForAdmin(User $user): void
    {
        $user->delete();
    }
}
