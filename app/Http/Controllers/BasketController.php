<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\ConfigModel;
use App\Models\ModelLog;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Product;
use App\Models\OrderBatch;
use App\Models\OrderLine;
use App\Models\Size;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

 
class BasketController extends Controller
{
    const CARGO_PRICE = 45;
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
    public function index()
    {
        
        if (Auth::check()) {
            $customer = Auth::user(); 
            Session::put('customer_id', $customer->customer_id); 
        }
        $customer = Session::get('customer_id');
          
        if (!$customer) {
            return view('cart', ['cartItems' => [], 'sepetSayisi' => 0]); 
        }
    
        $basket = Basket::where('customer_id', $customer)->where('is_active', 1)->first();
    
        if (!$basket) {
            return view('cart', ['cartItems' => [], 'sepetSayisi' => 0]); 
        }
    
        $cartItems = BasketItem::where('order_id', $basket->id)->get();
        $sizeIds = $cartItems->pluck('size_id')->unique();
        $sizes = Size::whereIn('id', $sizeIds)->get()->keyBy('id');

        $productSkus = $cartItems->pluck('product_sku')->unique();
        $products = Product::whereIn('product_sku', $productSkus)->get()->keyBy('product_sku'); 

        foreach ($cartItems as $item) {
            $size = $sizes->get($item->size_id);
            $product = $products->get($item->product_sku);
            //dd($item->size_id, $size);
            $item->size_name = $size ? $size->size_name : 'Beden Yok';
            $item->discount_rate = $product ? $product->discount_rate : 0;
            $item->discounted_price = $product && $product->discount_rate > 0 ? ($item->product_price - ($item->product_price * ($product->discount_rate / 100))) : null;
        }

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item->discounted_price !== null) {
                $totalPrice += ($item->discounted_price * $item->product_piece);
            } else {
                $totalPrice += ($item->product_price * $item->product_piece);
            }
        
        }
        $cargoPrice= self::CARGO_PRICE;
        $cargoTotalPrice= $totalPrice + $cargoPrice;
        return view('cart', compact('cartItems', 'totalPrice','cargoTotalPrice','cargoPrice'));
    }
    
    public function add(Request $request, $product_sku)
    {
        
        try {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size_id' => 'required|integer|exists:sizes,id',
        ], [
            'quantity.required' => 'Ürün adedi belirtilmelidir.',
            'quantity.integer' => 'Ürün adedi sayı olmalıdır.',
            'quantity.min' => 'Ürün adedi en az 1 olmalıdır.',
            'size_id.required' => 'Beden seçimi zorunludur.',
            'size_id.integer' => 'Beden ID\'si sayı olmalıdır.',
            'size_id.exists' => 'Seçilen beden geçersizdir.',
        ]);
        $product = Product::where('product_sku', $product_sku)->first();
        
        if (!$product) {
            $this->logRequest('add_to_cart', 'Ürün bulunamadı', $request->all(), 'Ürün bulunamadı');
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }
        $apiConfig = ConfigModel::where('api_name', 'stok_api')->first();
        $apiUrl= $apiConfig->api_url;
        $response = Http::get($apiUrl."{$product->product_sku}/{$request->size_id}");

        if ($response->failed()) {
            $this->logRequest('add_to_cart', 'Stok servisine ulaşılamadı', $request->all(), 'Servise ulaşılamadı', $response->body());
            return response()->json(['error' => 'Servise ulaşılamadı'], 500);
        }

        $stockData = $response->json();
        $this->logRequest('add_to_cart','Stok servisi yanıtı',$request->all(), null, $stockData );
        
        if (!isset($stockData['stores'])) {
            $this->logRequest('add_to_cart', 'Geçersiz stok servisi yanıtı', $request->all(), 'Geçersiz servis yanıtı', $response->body());
            return response()->json(['error' => 'Servis yanıtı geçersiz'], 500);
        }

        $totalStock = collect($stockData['stores'])->sum('stock');
        if ($totalStock < $request->quantity) {
            $this->logRequest('add_to_cart', 'Yetersiz stok', $request->all(), 'Yeterli stok yok', $response->body());
            return response()->json(['error' => 'Yeterli stok bulunmamaktadır.'], 400);
        }

        $customerId = Session::get('customer_id');
        if (!$customerId) {
            if (Auth::check()) {
                $customerId = Auth::id();
            } else {
                $customerId = mt_rand(10000000, 99999999);
            }
            Session::put('customer_id', $customerId);
        }

        $basket = Basket::firstOrCreate([
            'customer_id' => $customerId,
            'is_active' => 1
        ]);

        $basketItem = BasketItem::where('order_id', $basket->id)
            ->where('product_sku', $product->product_sku)
            ->where('size_id', $request->size_id)
            ->first();

        if ($basketItem) {
            if ($basketItem->product_piece + $request->quantity > $totalStock) {
                $this->logRequest('add_to_cart', 'Sepetteki ürün adedi toplam stoku aşıyor', $request->all(), 'Yeterli stok yok', $response->body());
                return response()->json(['error' => 'Yeterli stok yok.'], 400);
            }
            $basketItem->increment('product_piece', $request->quantity);
        } else {
            BasketItem::create([
                'product_name' => $product->product_name,
                'product_sku' => $product->product_sku,
                'product_piece' => $request->quantity,
                'product_price' => $product->product_price,
                'product_image' => $product->product_image,
                'order_id' => $basket->id,
                'size_id' => $request->size_id,
            ]);
        }

        $cartCount = BasketItem::where('order_id', $basket->id)->sum('product_piece');
        $this->logRequest('add_to_cart', 'Ürün sepete eklendi', $request->all(), null, [
            'product_sku' => $product->product_sku,
            'cartCount' => $cartCount
        ]);
        return response()->json(['success' => 'Ürün sepete eklendi!', 'cartCount' => $cartCount]);
        } catch (TokenMismatchException $exception) {
            $this->logRequest('add_to_cart', 'Token uyuşmazlığı', $request->all(), $exception->getMessage());
            return response()->json(['error' => 'CSRF token uyuşmazlığı. Lütfen sayfayı yenileyin ve tekrar deneyin.'], 419);
        }
    }

    public function approvl(Request $request)
    {  
        $customerId = Session::get('customer_id');
    
        $basket = Basket::where('customer_id', $customerId)->where('is_active', 1)->first();

        if (!$basket) {
            return response()->json(['error' => 'Sepet bulunamadı.'], 404);
        }
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email'=>['required'],
                'address' => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    'regex:/^([a-zA-ZÇçĞğİıÖöŞşÜü\s]+),\s*([a-zA-ZÇçĞğİıÖöŞşÜü\s]+),\s*([a-zA-ZÇçĞğİıÖöŞşÜü\s]+),\s*([a-zA-ZÇçĞğİıÖöŞşÜü\s]+),\s*(\d+),\s*([a-zA-ZÇçĞğİıÖöŞşÜü\s]+)$/u'
                ],
                'cardNumber' => ['required', 'digits:16', 'regex:/^[0-9]{16}$/'],
                'expiryDate' => ['required', 'regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/'],
                'cvv' => ['required', 'digits:3', 'regex:/^[0-9]{3}$/'],
                'cardHolderName' => ['required', 'string', 'min:3', 'max:255']
            ]);
            $cartItems = BasketItem::where('order_id', $basket->id)->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Sepette ürün bulunamadı.'], 400);
            }
            if (!$customerId) {
                if (Auth::check()) {
                    $member = Member::where('id', Auth::id())->first();
                    if ($member) {
                        $customerId = $member->customer_id;
                    } else {
                        $customerId = null;
                    }
                } 
            } else {
                if (!Member::where('id', $customerId)->exists()) {
                    $memberName = $request->input('name'); 
                    $member = new Member();
                    $member->id = $customerId;
                    $member->name = $request->input('name')  ?? 'Misafir Kullanıcı'; 
                    $member->email = $request->input('email');
                    $member->password = null;
                    $member->authority_id = 3;
                    $member->customer_id = $customerId;
                    $member->save();
                }
            }
            $productSkus = $cartItems->pluck('product_sku')->unique();
            $products = Product::whereIn('product_sku', $productSkus)->get()->keyBy('product_sku');
            $apiConfig = ConfigModel::where('api_name', 'stok_api')->first();
            if (!$apiConfig) {
                return response()->json(['error' => 'Stok API yapılandırması bulunamadı.'], 500);
            }
            $activeStoreIds = DB::table('stores')->where('is_active', 1)->pluck('id')->toArray();

            $dailyOrderQueryData = [];
            foreach ($cartItems as $item) {
                $dailyOrderQueryData[$item->product_sku . '_' . $item->size_id] = [
                    'product_sku' => $item->product_sku,
                    'product_size_id' => $item->size_id,
                ];
            }
            $dailyOrderTotals = DB::table('order_lines')
                ->whereIn('product_sku', array_column($dailyOrderQueryData, 'product_sku'))
                ->whereIn('product_size_id', array_column($dailyOrderQueryData, 'product_size_id'))
                ->whereDate('created_at', today())
                ->select('store_id', 'product_sku', 'product_size_id', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('store_id', 'product_sku', 'product_size_id')
                ->get()
                ->keyBy(function ($item) {
                    return $item->store_id . '_' . $item->product_sku . '_' . $item->product_size_id;
                });

            $stokError = false;
            $groupedItems = [];
            $totalPrice = 0;

            foreach ($cartItems as $item) {
                $product = $products->get($item->product_sku);
                $item->discount_rate = $product ? $product->discount_rate : 0;
                $item->discounted_price = $product && $product->discount_rate > 0 ? ($item->product_price - ($item->product_price * ($product->discount_rate / 100))) : null;

                if ($item->product_piece < 1) {
                    return response()->json(['error' => 'Sepette geçersiz ürün adedi var!'], 400);
                }
                $apiUrl = $apiConfig->api_url;
                $response = Http::get($apiUrl . "{$item->product_sku}/{$item->size_id}");

                if ($response->failed()) {
                    return response()->json(['error' => 'Stok servis bağlantısında bir hata oluştu.'], 503);
                }

                $stockData = $response->json();
                if (!isset($stockData['stores']) || empty($stockData['stores'])) {
                    return response()->json(['error' => 'Ürün için yeterli stok bilgisi bulunamadı.'], 400);
                }

                usort($stockData['stores'], function ($a, $b) {
                    return $a['store_priority'] - $b['store_priority'];
                });

                $totalStock = 0;
                $requestedQuantity = $item->product_piece;

                foreach ($stockData['stores'] as $store) {
                    if (!in_array($store['store_id'], $activeStoreIds)) {
                        continue;
                    }
                    $dailyTotalKey = $store['store_id'] . '_' . $item->product_sku . '_' . $item->size_id;
                    $dailyTotal = isset($dailyOrderTotals[$dailyTotalKey]) ? $dailyOrderTotals[$dailyTotalKey]->total_quantity : 0;

                    $maxSales = $store['store_max'];
                    $availableStock = min($store['stock'], $requestedQuantity, $maxSales - $dailyTotal);

                    if ($availableStock > 0) {
                        if (!isset($groupedItems[$store['store_id']])) {
                            $groupedItems[$store['store_id']] = [];
                        }
                        for ($i = 0; $i < $availableStock; $i++) {
                            $groupedItems[$store['store_id']][] = $item;
                        }
                        $totalStock += $availableStock;
                        $requestedQuantity -= $availableStock;

                        if ($requestedQuantity <= 0) {
                            break; 
                        }
                    }
                }
                if ($totalStock < $item->product_piece) {
                    $stokError = true;
                    break;
                }
                if ($item->discounted_price !== null) {
                    $totalPrice += ($item->discounted_price * $item->product_piece);
                } else {
                    $totalPrice += ($item->product_price * $item->product_piece);
                }
            } 

            if ($stokError) {
                return response()->json(['error' => 'Yeterli stok yok!'], 400);
            }

            $cargoTotalPrice = $totalPrice + self::CARGO_PRICE; 

            $name = $request->input('name');
            $address = $request->input('address');
            $cardNumber = $request->input('cardNumber');
            $expiryDate = $request->input('expiryDate');
            $cvv = $request->input('cvv');
            $cardHolderName = $request->input('cardHolderName');

            $orderBatch = OrderBatch::create([
                'customer_id' => $customerId,
                'customer_name' => $name,
                'customer_address' => $address,
                'product_price' => $cargoTotalPrice,
            ]);

            $orderId = $orderBatch->id;
            $orderBatch->order_id = $orderId; 
            $orderBatch->save();

            $subOrderIdCounter = 1;
            $allOrderLinesData = [];

            foreach ($groupedItems as $storeId => $items) {
                $currentOrderIdPrefix = (count($groupedItems) > 1) ? $orderId . '-' . $subOrderIdCounter : $orderId;
                foreach ($items as $item) {
                    $allOrderLinesData[] = [
                        'product_sku' => $item->product_sku,
                        'product_name' => $item->product_name,
                        'store_id' => $storeId,
                        'order_id' => $currentOrderIdPrefix,
                        'order_batch_id' => $orderId,
                        'quantity' => 1, 
                        'product_size_id' => $item->size_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $subOrderIdCounter++;
            }

            try {
                
                OrderLine::insert($allOrderLinesData);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Sipariş satırları oluşturulurken hata oldu.', 'message' => $e->getMessage()], 500);
            }

           $stockDecrements = [];
            foreach ($groupedItems as $storeId => $items) {
                foreach ($items as $item) {
                    $key = $item->product_sku . '_' . $storeId . '_' . $item->size_id;
                    $stockDecrements[$key] = ($stockDecrements[$key] ?? 0) + 1;
                }
            }

            foreach ($stockDecrements as $key => $decrementAmount) {
                list($productSku, $storeId, $sizeId) = explode('_', $key);

                $this->logRequest(
                    'Stok Güncelleme Başladı',
                    'Stok azaltma işlemi başlatılıyor',
                    [
                        'product_sku' => $productSku,
                        'product_piece' => $decrementAmount,
                        'store_id' => (int)$storeId, 
                        'size_id' => (int)$sizeId, 
                    ]
                );

                $affectedRows = DB::table('stocks')
                    ->where('product_sku', $productSku)
                    ->where('store_id', $storeId)
                    ->where('size_id', $sizeId)
                    ->decrement('product_piece', $decrementAmount);

                if ($affectedRows < 1) {
                    $this->logRequest(
                        'Stok Güncelleme Hatası',
                        'Stok azaltma başarısız',
                        [
                            'product_sku' => $productSku,
                            'store_id' => (int)$storeId,
                            'size_id' => (int)$sizeId,
                        ],
                        'Stok azaltılamadı'
                    );
                    return response()->json(['error' => 'Stok güncelleme sırasında bir hata oluştu!'], 500);
                }

                Log::info("Stok Güncellendi: " . json_encode([
                    'product_sku' => $productSku,
                    'store_id' => (int)$storeId,
                    'size_id' => (int)$sizeId,
                    'decremented_by' => $decrementAmount,
                ]));
            }

            
            $basket->update(['is_active' => 0]);
            BasketItem::where('order_id', $basket->id)->delete();

            return response()->json(['success' => 'Sipariş onaylandı!']);
        }

       $cartItems = BasketItem::where('order_id', $basket->id)->get();

        if ($cartItems->isEmpty()) {
           
            return view('cart_approve', ['cartItems' => collect(), 'cargoTotalPrice' => self::CARGO_PRICE]);
        }

        $allProductSkus = $cartItems->pluck('product_sku')->unique();
        $allSizeIds = $cartItems->pluck('size_id')->unique();

        $products = Product::whereIn('product_sku', $allProductSkus)->get()->keyBy('product_sku');
        $sizes = Size::whereIn('id', $allSizeIds)->get()->keyBy('id');

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $product = $products->get($item->product_sku);
            $size = $sizes->get($item->size_id);

            $item->size_name = $size ? $size->size_name : 'Beden Yok';
            $item->discount_rate = $product ? $product->discount_rate : 0;
            $item->discounted_price = $product && $product->discount_rate > 0 ? ($item->product_price - ($item->product_price * ($product->discount_rate / 100))) : null;

            if ($item->discounted_price !== null) {
                $totalPrice += ($item->discounted_price * $item->product_piece);
            } else {
                $totalPrice += ($item->product_price * $item->product_piece);
            }
        }
        $cargoTotalPrice = $totalPrice + self::CARGO_PRICE;

        $data = compact('cartItems', 'cargoTotalPrice');
        return view('cart_approve', $data);
    }

    public function delete($id)
    {
        $cartItem = BasketItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Ürün sepetten kaldırıldı!'], 200);
    } 
    
       
    public function update(Request $request, $id)
    {
        $basketItem = BasketItem::find($id);

        if ($basketItem) {
            $basketItem->product_piece = $request->adet;
            $basketItem->save();

            $basket = Basket::find($basketItem->order_id);
            $cartItems = BasketItem::where('order_id', $basket->id)->get();

            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += ($item->product_price * $item->product_piece);
            }

            return response()->json(['success' => 'Sepet güncellendi.', 'totalPrice' => $totalPrice]);
        }

        return response()->json(['error' => 'Ürün bulunamadı.'], 404);
    }



}