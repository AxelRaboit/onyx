<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Role as RoleEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateRolesCommand extends Command
{
    protected $signature = 'onyx:create-roles';

    protected $description = 'Create application roles (ROLE_DEV, ROLE_USER) if they do not exist.';

    public function handle(): int
    {
        foreach (RoleEnum::cases() as $role) {
            $created = Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web'])->wasRecentlyCreated;
            $status = $created ? '<info>created</info>' : '<comment>already exists</comment>';
            $this->line(sprintf('  %s : %s', $role->value, $status));
        }

        $this->info('Roles OK.');

        return self::SUCCESS;
    }
}
