<?php

namespace App\Console\Commands;

use App\Jobs\CheckBasketStockJob;

use Illuminate\Console\Command;

class TestJobCommand extends Command
{
   protected $signature = 'test:job';
    protected $description = 'CheckBasketStockJob jobını tetikler.';

    public function handle()
    {
        CheckBasketStockJob::dispatch();
        $this->info('CheckBasketStockJob jobı kuyruğa eklendi!');
    }
}

