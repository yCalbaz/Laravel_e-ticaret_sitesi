<?php
namespace App\Services;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\OrderBatch;
use App\Models\OrderLine;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class BasketService
{
    public function getStockData($productSku)
    {
        $response = Http::get("http://host.docker.internal:3000/stock/{$productSku}");
        if ($response->failed()) {
            throw new \Exception('Stok servisine ulaşılamıyor');
        }
        return $response->json();
    }

    public function createOrUpdateBasket($customerId)
    {
        $basket = Basket::where('customer_id', $customerId)->where('is_active', 1)->first();

        if (!$basket) {
            $basket = Basket::create([
                'customer_id' => $customerId,
                'is_active' => 1,
            ]);
        }

        return $basket;
    }

    public function addProductToBasket($basket, $product, $quantity)
    {
        $basketItem = BasketItem::where('order_id', $basket->id)
            ->where('product_sku', $product->product_sku)
            ->first();

        if ($basketItem) {
            $basketItem->increment('product_piece', $quantity);
        } else {
            BasketItem::create([
                'product_name' => $product->product_name,
                'product_sku' => $product->product_sku,
                'product_piece' => $quantity,
                'product_price' => $product->product_price,
                'product_image' => $product->product_image,
                'order_id' => $basket->id,
            ]);
        }
    }

    public function processOrder($basket, $customerId, $name, $addres, $cartItems)
    {
        $totalPrice = 0;
        $storeIds = [];
        $stockResponses = [];
        $stockUpdates = [];

        foreach ($cartItems as $item) {
            $stockData = $this->getStockData($item->product_sku);
            $stockResponses[$item->product_sku] = $stockData;

            $totalStock = array_sum(array_column($stockData['stores'], 'stock'));

            if ($totalStock < $item->product_piece) {
                throw new \Exception('Yeterli stok yok');
            }

            
            foreach ($stockData['stores'] as $store) {
                if ($item->product_piece > 0 && $store['stock'] > 0) {
                    $deductQuantity = min($store['stock'], $item->product_piece);
                    $stockUpdates[] = [
                        'product_sku' => $item->product_sku,
                        'store_id' => $store['store_id'],
                        'quantity' => $deductQuantity
                    ];
                    $item->product_piece -= $deductQuantity;
                }
            }

            $totalPrice += ($item->product_price * $item->product_piece);
        }

        $orderBatch = OrderBatch::create([
            'customer_id' => $customerId,
            'customer_name' => $name,
            'customer_address' => $addres,
            'product_price' => $totalPrice,
        ]);

        $orderId = $orderBatch->id;

        $groupedItems = [];
        foreach ($cartItems as $item) {
            foreach ($stockResponses[$item->product_sku]['stores'] as $store) {
                if (!isset($groupedItems[$store['store_id']])) {
                    $groupedItems[$store['store_id']] = [];
                }
                $groupedItems[$store['store_id']][] = $item;
            }
        }

        $this->createOrderLinesAndUpdateStock($orderId, $groupedItems, $stockUpdates);
    }

    public function createOrderLinesAndUpdateStock($orderId, $groupedItems, $stockUpdates)
    {
        $orderLinesData = [];
        foreach ($groupedItems as $store => $items) {
            foreach ($items as $item) {
                for ($i = 0; $i < $item->product_piece; $i++) {
                    $orderLinesData[] = [
                        'product_sku' => $item->product_sku,
                        'product_name' => $item->product_name,
                        'store_id' => $store,
                        'order_id' => $orderId,
                        'order_batch_id' => $orderId,
                    ];
                }
            }
        }
        
        if (!empty($orderLinesData)) {
            OrderLine::insert($orderLinesData);
        }

        if (!empty($stockUpdates)) {
            $cases = [];
            $bindings = [];
            $productSkus = [];

            foreach ($stockUpdates as $update) {
                $cases[] = "WHEN product_sku = ? AND store_id = ? THEN product_piece - ?";
                $bindings[] = $update['product_sku'];
                $bindings[] = $update['store_id'];
                $bindings[] = $update['quantity'];
                $productSkus[] = $update['product_sku'];
            }

            $sql = "UPDATE stocks SET product_piece = CASE " . implode(" ", $cases) . " ELSE product_piece END WHERE product_sku IN (" . implode(',', array_fill(0, count($productSkus), '?')) . ")";
            DB::update($sql, array_merge($bindings, $productSkus));
        }
    }
}
