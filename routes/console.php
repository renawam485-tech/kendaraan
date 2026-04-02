<?php

use Illuminate\Support\Facades\Schedule;

// Jalankan setiap 10 menit.
// withoutOverlapping() → skip jika eksekusi sebelumnya belum selesai.
// runInBackground()    → tidak memblokir scheduler lain.
Schedule::command('bookings:check-approval-deadlines')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/check-approval-deadlines.log'));