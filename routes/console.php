<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek setiap hari jam 08:00 — kirim reminder H-1 jatuh tempo
Schedule::command('borrowings:send-due-reminders')->dailyAt('08:00');