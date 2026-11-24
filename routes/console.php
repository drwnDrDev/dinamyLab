<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

if (config('backups.enabled')) {
    Schedule::command('db:backup')
        ->cron(config('backups.schedule'))
        ->timezone(config('backups.timezone'));
}

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
