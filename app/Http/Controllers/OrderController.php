<?php

namespace App\Http\Controllers;

use App\Models\ConfigModel;
use App\Models\MemberStore;
use App\Models\OrderBatch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{  
    public function index()
    {
        { if (!Auth::check()) { 
            return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
        }
        $memberId = Auth::id();
        
        $products = Product::where('customer_id', $memberId)->get()
        ->map(function ($product) {
            if ($product->discount_rate > 0) {
                $product->discounted_price = $product->product_price - ($product->product_price * ($product->discount_rate / 100));
            } else {
                $product->discounted_price = null;
            }
            return $product;
        });
        return view('seller_product', compact('products'));
        }
    }

    public function sellerProduct(Request $request)
    {
        
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
        }
        $memberId = Auth::id();

        $query = Product::where('customer_id', $memberId);

        $storeName = $request->input('depo_adi');
        if ($storeName) {
            $query->whereHas('stocks.store', function ($q) use ($storeName) {
                $q->where('store_name', 'like', '%' . $storeName . '%');
            });
        }

        $products = $query->get()->map(function ($product) {
            $apiConfig = ConfigModel::where('api_name', 'stok_api')->first();
            $apiUrl= $apiConfig->api_url;
            $stockHave = false;
            foreach ($product->stocks as $stock) {
                try {
                    $response = Http::timeout(4)->get($apiUrl . "{$product->product_sku}/{$stock->size_id}");
                    if ($response->successful()) {
                        $stockData = $response->json();
                        $stockQuantity = collect($stockData['stores'])->sum('stock');
                        if ($stockQuantity > 0) {
                            $stockHave = true;
                            break;
                        }
                    }
                } catch (\Exception $e) {
                }
            }
            $product['stokta_var'] = $stockHave;
            return $product;
        });

        $stokStatus = $request->input('stok_durumu');
        if ($stokStatus === 'stokta_var') {
            $products = $products->where('stokta_var', true);
        } elseif ($stokStatus === 'stokta_yok') {
            $products = $products->where('stokta_var', false);
        }

        $stores = \App\Models\Stock::with('store')
            ->whereHas('product', function ($q) use ($memberId) {
                $q->where('customer_id', $memberId);
            })
            ->distinct('store_id')
            ->get()
            ->pluck('store.store_name', 'store.store_name')
            ->toArray();

        return view('seller_product', compact('products', 'stores'));
    }
}  