<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use App\Services\DemoFixtureService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedDemoUserCommand extends Command
{
    protected $signature = 'onyx:demo:seed
                            {--email= : Demo user email (defaults to config demo.email)}
                            {--password= : Demo user password (defaults to config demo.password)}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Create or reset the demo user with fixture notes.';

    public function __construct(private readonly DemoFixtureService $fixtureService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->option('email') ?? config('demo.email');
        $password = $this->option('password') ?? config('demo.password');

        if (! $email || ! $password) {
            $this->error('Demo email and password must be configured (DEMO_USER_EMAIL / DEMO_USER_PASSWORD).');

            return self::FAILURE;
        }

        $existing = User::where('is_demo', true)->orWhere('email', $email)->first();

        if ($existing && ! $this->option('force')) {
            $confirmed = $this->confirm(sprintf('Demo user [%s] already exists. Reset all their data?', $email));

            if (! $confirmed) {
                $this->line('Aborted.');

                return self::SUCCESS;
            }
        }

        if ($existing) {
            $this->line('  Clearing existing demo data...');
            $this->fixtureService->clear($existing);
            $existing->delete();
        }

        $this->line('  Creating demo user...');
        $user = User::create([
            'name' => 'Demo',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'locale' => 'fr',
            'is_demo' => true,
        ]);
        $user->assignRole(Role::User->value);

        $this->line('  Seeding fixture notes and images...');
        $this->fixtureService->seed($user);

        $noteCount = $user->notes()->count();

        $this->newLine();
        $this->info(sprintf('✅ Demo user ready [%s].', $email));
        $this->table(['Field', 'Value'], [
            ['Email', $user->email],
            ['Name',  $user->name],
            ['Notes', $noteCount],
        ]);

        return self::SUCCESS;
    }
}
