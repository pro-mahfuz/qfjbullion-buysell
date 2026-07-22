<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::command('transfer:pending-to-buysell')->everySecond()->withoutOverlapping();

Schedule::command('app:trade-closer-command')->everySecond()->withoutOverlapping();
