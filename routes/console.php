<?php

declare(strict_types=1);

use App\Console\Commands\PruneOrphanedImagesCommand;
use App\Console\Commands\SeedDemoUserCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (config('demo.enabled')) {
    Schedule::command(SeedDemoUserCommand::class, ['--force' => true])
        ->dailyAt('02:00')
        ->withoutOverlapping();
}

Schedule::command(PruneOrphanedImagesCommand::class)
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->withoutOverlapping();
