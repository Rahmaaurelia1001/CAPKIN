<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =====================================================
// SIMONIKA: Jadwal Pengiriman Reminder Deadline Email
// =====================================================
// Kirim reminder ke masing-masing role sesuai jadwal:
//   - PIC          : tgl 28 tiap bulan (H-3 sebelum deadline tgl 1)
//   - Kabid/Kabag  : tgl 31/30/28 (H-1 sebelum tgl 1 bulan depan) 
//   - Katim Umper  : tgl 1 tiap bulan (H-1 sebelum deadline tgl 2)
//   - Kabag Umum   : tgl 2 tiap bulan (H-1 sebelum deadline tgl 3)
//   - Kapus        : tgl 2 tiap bulan (H-1 sebelum deadline tgl 3)
// =====================================================
Schedule::command('simonika:kirim-reminder')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->appendOutputTo(storage_path('logs/reminder-deadline.log'));
