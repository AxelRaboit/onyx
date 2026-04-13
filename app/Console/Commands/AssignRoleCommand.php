<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('user:assign-role {email : User email} {role : Role name (e.g. ROLE_DEV)}')]
#[Description('Assign a role to a user')]
class AssignRoleCommand extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error(sprintf('User with email %s not found', $email));

            return 1;
        }

        $user->syncRoles($roleName);

        $this->info(sprintf('Assigned role %s to %s', $roleName, $user->email));

        return 0;
    }
}
