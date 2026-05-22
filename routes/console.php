<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled tasks ─────────────────────────────────────────
Schedule::command('activitylog:clean --days=90')->dailyAt('02:00');
Schedule::command('telescope:prune --hours=48')->dailyAt('02:30');
Schedule::command('stock:check')->dailyAt('08:00');

