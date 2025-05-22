<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ConfigModel;
use App\Models\ModelLog;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    const CUSTOMER_ROLE_ID = 3;
    private function logRequest($operation, $message = null, $requestData = null, $error = null, $response = null)
    {
        ModelLog::create([
            'log_title' => 'API Request',
            'operaton' => $operation,
            'message' => $message,
            'error' => $error ?? "",
            'success' => $error ? null : 'Başarılı İstek',
            'request' => json_encode($requestData),
            'response' => $response ? json_encode($response) : null,
        ]);
    }
    public function index()
    {
        $products = Product::all()->filter(function($product) {
            foreach($product->stocks as $stock){
            try {
                $apiConfig = ConfigModel::where('api_name','stok_api')->first();
                $apiUrl = $apiConfig->api_url;
                $response = Http::timeout(4)->get($apiUrl."{$product->product_sku}/{$stock->size_id}");
                $this->logRequest(
                    'ProductController/index',  
                    'Stok API isteği gönderildi', 
                    ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                    $response->failed() ?  'Stok servisine ulaşılamadı' : null, 
                    $response->successful() ? $response->json() : null  
                );
                if ($response->successful()) {
                    $stockData = $response->json();
                    $stock = $stockData['stores'][0]['stock'] ?? 0;
                    return $stock > 0;
                }
            } catch (\Exception $e) {
                $this->logRequest(
                    'ProductController/productHome',
                    'Stok API isteği sırasında hata oluştu',
                    ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                    $e->getMessage()
                );
                }
            }
                return false;
            })->map(function ($product) {
                if ($product->discount_rate > 0) {
                    $product->discounted_price = $product->product_price - ($product->product_price * ($product->discount_rate / 100));
                } else {
                    $product->discounted_price = null;
                }
                return $product;
            });
            
         
            return view('product', ['urunler' => $products]);
    }
    
    public function showDetails($sku)
    {
        $product = Product::where('product_sku', $sku)->firstOrFail();

        $discountRate = $product->discount_rate ?? 0;
        $discountedPrice = null;
        if ($discountRate > 0) {
            $discountedPrice = $product->product_price - ($product->product_price * ($discountRate / 100));
        }

        $product->load('stocks.size');
        $groupedStocks = $product->stocks->groupBy('size.id')->map(function ($items) {
            return [
                'size' => $items->first()->size,
                'total_piece' => $items->sum('product_piece'),
            ];
        })->values();

        return view('product_details', compact('product', 'groupedStocks','discountRate','discountedPrice'));
    }
        
    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('product_name', 'LIKE', "%$query%")
            ->orWhere('details', 'LIKE', "%$query%")
            ->orWhereHas('categories', function ($q) use ($query) {
                $q->where('category_name', 'LIKE', "%$query%");
            })
            ->get()
            ->filter(function ($product) {
                $apiConfig = ConfigModel::where('api_name','stok_api')->first();
                $apiUrl= $apiConfig->api_url;
                foreach ($product->stocks as $stock) {
                    try {
                        $response = Http::timeout(4)->get($apiUrl . "{$product->product_sku}/{$stock->size_id}");

                        if ($response->successful()) {
                            $stockData = $response->json();
                            $stockAdedi = $stockData['stores'][0]['stock'] ?? 0;
                            $this->logRequest(
                                'ProductController/productCategory',  
                                'Stok API isteği gönderildi', 
                                ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                                $response->failed() ?  'Stok servisine ulaşılamadı' : null, 
                                $response->successful() ? $response->json() : null  
                            );
                            return $stockAdedi > 0;
                        }
                    } catch (\Exception $e) {
                        $this->logRequest(
                            'ProductController/search',
                            'Stok API isteği sırasında hata oluştu',
                            ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                            $e->getMessage());
                    }
                }
                return false;
            })
            ->map(function ($product) {
                if ($product->discount_rate > 0) {
                    $product->discounted_price = $product->product_price - ($product->product_price * ($product->discount_rate / 100));
                } else {
                    $product->discounted_price = null;
                }
                return $product;
            });

        return view('search-results', compact('products', 'query'));
    }
    
    public function productCategory($category_slug)
    {
        $kategori = Category::where('category_slug', $category_slug)->first();
        $altKategori = null;

        if (!$kategori) {
            $altKategori = Category::whereHas('parentCategories', function ($query) use ($category_slug) {
                $query->where('category_slug', $category_slug);
            })->first();

            if (!$altKategori) {
                return abort(404, 'Kategori bulunamadı.');
            }
        }

        $productsQuery = ($kategori ? $kategori->products() : $altKategori->products());
        $products = $productsQuery->get()->filter(function ($product) {
            $apiConfig = ConfigModel::where('api_name','stok_api')->first();
            $apiUrl= $apiConfig->api_url;
            foreach ($product->stocks as $stock) {
                try {
                    $response = Http::timeout(4)->get($apiUrl . "{$product->product_sku}/{$stock->size_id}");

                    if ($response->successful()) {
                        $stockData = $response->json();
                        $stockAdedi = $stockData['stores'][0]['stock'] ?? 0;
                        $this->logRequest(
                            'ProductController/productCategory',  
                            'Stok API isteği gönderildi', 
                            ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                            $response->failed() ?  'Stok servisine ulaşılamadı' : null, 
                            $response->successful() ? $response->json() : null  
                        );
                        return $stockAdedi > 0;
                        
                    }
                } catch (\Exception $e) {
                    $this->logRequest(
                        'ProductController/productCategory',
                        'Stok API isteği sırasında hata oluştu',
                        ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                        $e->getMessage()
                    );
                }
            }
            return false;
        })->map(function ($product) {
            if ($product->discount_rate > 0) {
                $product->discounted_price = $product->product_price - ($product->product_price * ($product->discount_rate / 100));
            } else {
                $product->discounted_price = null;
            }
            return $product;
        });

        return view('category_product', ['urunler' => $products, 'kategori' => $kategori ? $kategori->category_name : $altKategori->category_name]);
    }

    public function getProductsByCategory(Request $request)
    {
        $categories = $request->categories;

        if (empty($categories)) {
            $urunler = Product::all();
        } else {
            $urunler = Product::whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('category_slug', $categories);
            })->get();
        }

        return response()->json($urunler);
    }

    public function filterProducts(Request $request)
    {
        $categories = $request->input('categories', []);
        $genders = $request->input('genders', []);

        $urunler = Product::query();
    
        if (!empty($categories)) {
            $urunler->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            });
        }

        if (!empty($genders)) {
            $urunler->whereIn('gender', $genders);
        }

        $urunler = $urunler->get();

        return view('partials.product_list', ['urunler' => $urunler]);
    }

    public function getSizes($sku)
    {
        $product = Product::where('product_sku', $sku)->firstOrFail();
        $stocks = Stock::where('product_sku', $sku) 
            ->where('product_piece', '>', 0)
            ->whereNotNull('size_id')
            ->with('size')
            ->get();

        $sizes = $stocks->pluck('size');

        return response()->json($sizes);
    }

    public function productHome()
    { 
        $products = Product::orderBy('id', 'desc')->get()->filter(function($product) {
            $apiConfig = ConfigModel::where('api_name','stok_api')->first();
            $apiUrl = $apiConfig->api_url;
            foreach ($product->stocks as $stock) {
            try {
                $response = Http::timeout(4)->get($apiUrl . "{$product->product_sku}/{$stock->size_id}");
                $this->logRequest(
                    'HomeController/productHome',  
                    'Stok API isteği gönderildi', 
                    ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                    $response->failed() ?  'Stok servisine ulaşılamadı' : null, 
                    $response->successful() ? $response->json() : null  
                );
                if ($response->successful()) {
                    $stockData = $response->json();
                    $stock = $stockData['stores'][0]['stock'] ?? 0; 
                    return $stock > 0;
                }
            } catch (\Exception $e) {
                $this->logRequest(
                    'HomeController/productHome',
                    'Stok API isteği sırasında hata oluştu',
                    ["product_sku" => $product->product_sku, "size_id" => $stock->size_id],
                    $e->getMessage()
                );
            }}
            return false;
        })->map(function ($product) {
            if ($product->discount_rate > 0) {
                $product->discounted_price = $product->product_price - ($product->product_price * ($product->discount_rate / 100));
            } else {
                $product->discounted_price = null;
            }
            return $product;
        })->take(4);
    
        return view('home', compact('products'));
    }

    public function showCustomerPanel()
    {
        if(session('user_authority') !== self::CUSTOMER_ROLE_ID){
            return redirect()->route('login');
        }
        $products = Product::orderBy('id', 'desc')->get()->filter(function($product) {
            $apiConfig = ConfigModel::where('api_name', 'stok_api')->first();
            $apiUrl= $apiConfig->api_url;
            foreach ($product->stocks as $stock){
            try {
                $response = Http::timeout(4)->get($apiUrl."{$product->product_sku}/{$stock->size_id}");
                $this->logRequest(
                    'Stok API isteği gönderildi',
                    " {$product->product_sku}, Size ID: {$stock->size_id}",
                    ['url' => $apiUrl."{$product->product_sku}/{$stock->size_id}"],
                    null, 
                    $response->json() 
                );
                if ($response->successful()) {
                    $stockData = $response->json();
                    $stock = $stockData['stores'][0]['stock'] ?? 0; 
                    return $stock > 0;
                }
            } catch (\Exception $e) {
                $this->logRequest(
                    'Stok API Hatası', 
                    "{$product->product_sku}, Size ID: {$stock->size_id}", 
                    ['url' => $apiUrl."{$product->product_sku}/{$stock->size_id}"], 
                    $e->getMessage() 
                );
            }}
            return false;
        })->map(function ($product) {
            if ($product->discount_rate > 0) {
                $product->discounted_price = $product->product_price - ($product->product_price * ($product->discount_rate / 100));
            } else {
                $product->discounted_price = null;
            }
            return $product;
        })->take(4);
    
        return view('home', compact('products'));
    }
}