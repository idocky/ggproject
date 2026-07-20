<?php

use App\Console\Commands\GridSyncUpcomingCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(GridSyncUpcomingCommand::class)
    ->dailyAt('02:00')
    ->timezone('UTC')
    ->onFailure(fn () => Log::error('grid:sync-upcoming scheduled run failed'));
