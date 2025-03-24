<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckBasketStockJob;

class RunBasketStockCheck extends Command
{
    protected $signature = 'basket:check-stock';
    protected $description = 'Sepet stoklarını kontrol eder.';

    public function handle()
    {
        $this->info('Sepet stok kontrolü başlatılıyor...');
        CheckBasketStockJob::dispatch();
        $this->info('Sepet stok kontrolü işi kuyruğa eklendi.');
    }
}