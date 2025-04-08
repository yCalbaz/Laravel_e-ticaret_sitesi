<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
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
 
class BasketController extends Controller
{
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
        foreach ($cartItems as $item) {
            $size = Size::find($item->size_id);
            //dd($item->size_id, $size);
            $item->size_name = $size ? $size->size_name : 'Beden Yok';
        }

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += ($item->product_price * $item->product_piece);
        }
        $cargoTotalPrice= $totalPrice + 45;
    
        return view('cart', compact('cartItems', 'totalPrice','cargoTotalPrice'));
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
        //tokenin validasyonunu da ekle!!

        
        $product = Product::where('product_sku', $product_sku)->first();

        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }

        $response = Http::get("http://host.docker.internal:3000/stock/{$product->product_sku}");

        if ($response->failed()) {
            return response()->json(['error' => 'Servise ulaşılamadı'], 500);
        }

        $stockData = $response->json();
        if (!isset($stockData['stores'])) {
            return response()->json(['error' => 'Servis yanıtı geçersiz'], 500);
        }

        $totalStock = collect($stockData['stores'])->sum('stock');
        if ($totalStock < $request->quantity) {
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
            
        return response()->json(['success' => 'Ürün sepete eklendi!', 'cartCount' => $cartCount]);
        } catch (TokenMismatchException $exception) {
            return response()->json(['error' => 'CSRF token uyuşmazlığı. Lütfen sayfayı yenileyin ve tekrar deneyin.'], 419);
        }
    }

    public function delete($id)
    {
        $cartItem = BasketItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Ürün sepetten kaldırıldı!'], 200);
    }

    public function approvl(Request $request)
    {
        $customerId = Session::get('customer_id');
        
            if (!$customerId) {
                if (Auth::check()) {
                    $member = Member::where('id', Auth::id())->first();
                    if ($member) {
                        $customerId = $member->customer_id;
                    } else {
                        $customerId = null;
                    }
                } else {
                    $customerId = mt_rand(10000000, 99999999);
                }
            }
        
            $basket = Basket::where('customer_id', $customerId)->where('is_active', 1)->first();
        
            if (!$basket) {
                return redirect()->back()->with('error', 'Sepet bulunamadı.');
            }
        
            if ($request->isMethod('post')) {
                $request->validate([
                    'name' => ['required', 'string', 'min:3', 'max:255'],
                    'address' => ['required', 
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
                $totalPrice = 0;
                $stokError = false;
                $storeId = [];
        
                foreach ($cartItems as $item) {
                    if ($item->product_piece < 1) {
                        return redirect()->back()->with('error', 'Sepette geçersiz ürün adedi var!');
                    }
                    $response = Http::get("http://host.docker.internal:3000/stock/{$item->product_sku}");
        
                    if ($response->failed()) {
                        return redirect()->with('error', 'Stok servisine ulaşılmıyor');
                    }
        
                    $stockData = $response->json();
                    if (!isset($stockData['stores'])) {
                        return redirect()->back()->with('error', 'Yeterli stok yok');
                    }
        
                    
                    usort($stockData['stores'], function($a, $b) {
                        return $a['store_priority'] - $b['store_priority'];
                    });
                    
        
                    $totalStock = 0;
                    $requestedQuantity = $item->product_piece;
                    foreach ($stockData['stores'] as $store) {
                        $isActiveStore = DB::table('stores')->where('id', $store['store_id'])->where('is_active',1)->exists();
                    if (!$isActiveStore) {
                        continue; 
                    }
                        
                        $dailyTotal = DB::table('order_lines')
                        ->where('store_id', $store['store_id']) 
                        ->where('product_sku', $item->product_sku) 
                        ->whereDate('created_at', today()) 
                        ->sum('quantity'); 
        
                        
                        $maxSales = $store['store_max'];
                        $availableStock = min($store['stock'], $requestedQuantity, $maxSales - $dailyTotal);
        
                        
                        if ($availableStock > 0) {
                        
                            $storeId[$item->product_sku] = $store['store_id'];
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
        
                    $totalPrice += ($item->product_price * $item->product_piece);
                }
        
                if ($stokError) {
                    return redirect()->back()->with('error', 'Yeterli stok yok');
                }
        
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
                    'product_price' => $totalPrice,
                ]);
        
                $orderId = $orderBatch->id;
                $orderBatch->order_id = $orderId;
                $orderBatch->save();
        
                $groupedItems = [];
                foreach ($cartItems as $item) {
                    $store = $storeId[$item->product_sku];
                    if (!isset($groupedItems[$store])) {
                        $groupedItems[$store] = [];
                    }
                    $groupedItems[$store][] = $item;
                }
        
                $subOrderId = 1;
                foreach ($groupedItems as $store => $items) {
                    $orderLinesData = [];
                    foreach ($items as $item) {
                        $size = Size::find($item->size_id);
            $sizeName = $size ? $size->size_name : 'Beden Yok';

            for ($i = 0; $i < $item->product_piece; $i++) {
            $orderLinesData[] = [
                'product_sku' => $item->product_sku,
                'product_name' => $item->product_name,
                'store_id' => $store,
                'order_id' => (count($groupedItems) > 1) ? $orderId . '-' . $subOrderId : $orderId,
                'order_batch_id' => $orderId,
                'quantity' => 1,
                'product_size_id' => $size ? $size->id : null, // Varsayılan değeri kaydedin
                            ];
                            //dd($sizeName);
                        }
                    }
                    try {
                        OrderLine::insert($orderLinesData);
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', 'Sipariş oluşturulurken hata oldu.' . $e->getMessage());
                    }
                    $subOrderId++;
                }
        
                foreach ($groupedItems as $store => $items) {
                    foreach ($items as $item) {
                        Log::info("Stok Güncelleme İşlemi Başlatılıyor: " . json_encode([
                            'product_sku' => $item->product_sku,
                            'product_piece' => $item->product_piece,
                            'store_id' => $store,
                        ]));
        
                        $currentStock = DB::table('stocks')
                            ->where('product_sku', $item->product_sku)
                            ->where('store_id', $store)
                            ->value('product_piece');
        
                        if ($currentStock < $item->product_piece) {
                            Log::error("Stok Yetersiz! " . json_encode([
                                'product_sku' => $item->product_sku,
                                'store_id' => $store,
                                'current_stock' => $currentStock,
                                'requested_stock' => $item->product_piece,
                            ]));
        
                            return redirect()->back()->with('error', 'Yeterli stok bulunmamaktadır!');
                        }
        
                        DB::table('stocks')
                            ->where('product_sku', $item->product_sku)
                            ->where('store_id', $store)
                            ->decrement('product_piece', $item->product_piece);
        
                        Log::info("Stok Güncellendi: " . json_encode([
                            'product_sku' => $item->product_sku,
                            'store_id' => $store,
                            'used_stock' => $item->product_piece,
                            'remaining_stock' => $currentStock - $item->product_piece
                        ]));
                    }
                }
                $basket->update(['is_active' => 0]);
                BasketItem::where('order_id', $basket->id)->delete();
                return redirect()->route('cart.index')->with('success', 'Sipariş onaylandı!');
            }
        
            $cartItems = BasketItem::where('order_id', $basket->id)->get();
            foreach ($cartItems as $item) {
                $size = Size::find($item->size_id);
                if ($size) {
                    $item->size_name = $size->size_name; // size_name'i ekleyin
                } else {
                    $item->size_name = 'Beden Yok'; // size bulunamazsa
                }
            }
        
            //dd($cartItems);
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += ($item->product_price * $item->product_piece);
            }
            
            $data = compact('cartItems', 'totalPrice');
            return view('cart_approve', $data);
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