<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\ConfigModel;
use App\Models\ModelLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckBasketStockJob implements ShouldQueue
{
    private function logRequest($operation, $message = null, $requestData = null, $error = null, $response = null)
    {
        ModelLog::create([
            'log_title' => 'API Request',
            'operaton' => $operation,
            'message' => $message,
            'error' => $error ?? "",
            'success' => $error ? 'Hata' : 'Başarılı İstek',
            'request' => json_encode($requestData),
            'response' => $response ? json_encode($response) : null,
        ]);
    }
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
    public function handle()
    {
        Log::info('Job başladı!');
        $baskets = Basket::where('is_active', 1)->get();

        foreach ($baskets as $basket) {
            $basketItems = BasketItem::where('order_id', $basket->id)->get();
            $apiConfig = ConfigModel::where('api_name', 'stok_api')->first();
            $apiUrl= $apiConfig->api_url;
            foreach ($basketItems as $item) {
                $response = Http::get($apiUrl."{$item->product_sku}");

                if ($response->failed()) {
                    Log::error("Stok servisine ulaşılmıyor {$item->product_sku}");
                    continue;
                }

                $stockData = $response->json();
                if (!isset($stockData['stores'])) {
                    Log::error("Servis Yanıtı uygunsuz {$item->product_sku}");
                    continue;
                }

                $totalStock = collect($stockData['stores'])->sum('stock');

                if ($totalStock < $item->product_piece) {
                    Log::info("Yetersiz stok, ürün sepetten siliniyor: {$item->product_sku}");
                    $item->delete();
                }
            }
        }

        Log::info('Sepet stok kontrolü tamamlandı.');
    }
}