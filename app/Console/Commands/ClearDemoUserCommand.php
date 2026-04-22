<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DemoFixtureService;
use Illuminate\Console\Command;

class ClearDemoUserCommand extends Command
{
    protected $signature = 'onyx:demo:clear
                            {--email= : Demo user email (defaults to config demo.email)}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Delete the demo user and all their data.';

    public function __construct(private readonly DemoFixtureService $fixtureService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->option('email') ?? config('demo.email');

        $user = User::where('is_demo', true)->orWhere('email', $email)->first();

        if (! $user) {
            $this->warn(sprintf('No demo user found with email [%s].', $email));

            return self::SUCCESS;
        }

        if (! $this->option('force')) {
            $confirmed = $this->confirm(sprintf('Delete demo user [%s] and all their data?', $user->email));

            if (! $confirmed) {
                $this->line('Aborted.');

                return self::SUCCESS;
            }
        }

        $this->line(sprintf('  Clearing data for [%s]...', $user->email));
        $this->fixtureService->clear($user);
        $user->delete();

        $this->info('✅ Demo user deleted.');

        return self::SUCCESS;
    }
}
