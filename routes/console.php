<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;

Schedule::call(function () {
    // Get current date
    $currentDate = Carbon::now();

    // Delete exceptions where the date has passed
    DB::table('exceptions')
        ->where('end', '<', $currentDate)
        ->delete();
})->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
