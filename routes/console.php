<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily SMS reminders to check hourly for cafe configured reminder_hour
Schedule::command('sms:send-daily-reminders')->hourly();

// Schedule daily birthday rewards dispatch at 8:00 AM
Schedule::command('loyalty:birthday-rewards')->dailyAt('08:00');

// Schedule daily cleanup of card-to-card receipt images older than 7 days
Schedule::command('receipts:cleanup-card')->daily();

// Schedule daily PWA metrics rollup at 00:30 AM
Schedule::command('pwa:aggregate-daily')->dailyAt('00:30');

