<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\SisaCutiRolloverJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sisa-cuti:rollover {year?}', function ($year = null) {
    $year = $year ?? (int) now()->year;
    $this->info("Starting sisa cuti rollover for year {$year}");
    
    SisaCutiRolloverJob::dispatch($year);
    
    $this->info("Sisa cuti rollover job dispatched successfully!");
})->purpose('Run sisa cuti rollover for specified year');
