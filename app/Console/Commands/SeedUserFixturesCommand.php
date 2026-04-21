<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DemoFixtureService;
use Illuminate\Console\Command;

class SeedUserFixturesCommand extends Command
{
    protected $signature = 'onyx:seed-fixtures
                            {--email= : Email of the user to seed}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Clear all notes for a user and reseed with demo fixtures (production-safe, no factories).';

    public function __construct(private readonly DemoFixtureService $fixtureService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Email of the user to seed');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error(sprintf('No user found with email [%s].', $email));

            return self::FAILURE;
        }

        if (! $this->option('force')) {
            $noteCount = $user->notes()->count();
            $confirmed = $this->confirm(
                sprintf('This will delete %d note(s) for [%s] and reseed with fixtures. Continue?', $noteCount, $email)
            );

            if (! $confirmed) {
                $this->line('Aborted.');

                return self::SUCCESS;
            }
        }

        $this->line(sprintf('  Clearing notes for %s...', $email));
        $this->fixtureService->clear($user);

        $this->line('  Seeding fixture notes and images...');
        $this->fixtureService->seed($user);

        $noteCount = $user->notes()->count();

        $this->newLine();
        $this->info(sprintf('✅ Fixtures applied to [%s].', $email));
        $this->table(['Field', 'Value'], [
            ['Email',    $user->email],
            ['Name',     $user->name],
            ['Notes',    $noteCount],
            ['Sections', 'Guide, Projects, Journal, Reference, Cuisine, Livres, Voyages, Sport, Finance, Ideas'],
            ['Images',   '8 (dashboard, architecture, photo, terminal, cuisine, voyage, livre, sport)'],
        ]);

        return self::SUCCESS;
    }
}
