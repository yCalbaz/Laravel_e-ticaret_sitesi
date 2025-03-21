<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckBasketStockJob; 

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CheckBasketStockJob)->everyFiveMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}