<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(['name' => 'ROLE_USER', 'guard_name' => 'web']);

        User::query()
            ->whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['ROLE_USER', 'ROLE_DEV']))
            ->get()
            ->each(fn (User $user) => $user->assignRole('ROLE_USER'));
    }

    public function down(): void
    {
    }
};
