<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('transfer:pending-to-buysell')->everySecond()->withoutOverlapping();

Schedule::command('app:trade-closer-command')->everySecond()->withoutOverlapping();
