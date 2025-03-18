<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Product;
use App\Models\OrderBatch;
use App\Models\OrderLine;
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
        return view('cart', ['cartItems' => []]); 
    }

    $basket = Basket::where('customer_id', $customer)->where('is_active', 1)->first();

    if (!$basket) {
        return view('cart', ['cartItems' => []]); 
    }

    $cartItems = BasketItem::where('order_id', $basket->id)->get();
    return view('cart', compact('cartItems'));
    }

    

    public function add(Request $request, $product_sku)
{
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
        ]);
    }

    return response()->json(['success' => 'Ürün sepete eklendi!']);
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
        ], [
            
            'name.required' => 'Ad Soyad zorunludur.',
            'name.string' => 'Ad Soyad geçerli bir metin olmalıdır.',
            'name.min' => 'Ad Soyad en az 3 karakter olmalıdır.',
            'name.max' => 'Ad Soyad en fazla 255 karakter olabilir.',
            
            'address.required' => 'Adres zorunludur.',
            'address.regex' => 'Adres formatı geçersiz. Lütfen şu şekilde giriniz: İl, İlçe, Mahalle, Sokak, No, Ülke.',
            
            'cardNumber.required' => 'Kart numarası zorunludur.',
            'cardNumber.digits' => 'Kart numarası 16 haneli olmalıdır.',
            'cardNumber.regex' => 'Geçerli bir kart numarası giriniz.',
            
            'expiryDate.required' => 'Son kullanma tarihi zorunludur.',
            'expiryDate.regex' => 'Geçerli bir son kullanma tarihi giriniz (MM/YY).',
            
            'cvv.required' => 'CVV kodu zorunludur.',
            'cvv.digits' => 'CVV kodu 3 haneli olmalıdır.',
            'cvv.regex' => 'Geçerli bir CVV kodu giriniz.',
            
            'cardHolderName.required' => 'Kart sahibi ismi zorunludur.',
            'cardHolderName.min' => 'Kart sahibi ismi en az 3 karakter olmalıdır.',
            'cardHolderName.max' => 'Kart sahibi ismi en fazla 255 karakter olabilir.'
        ]);

        $cartItems = BasketItem::where('order_id', $basket->id)->get();
        $totalPrice = 0;
        $stokError = false;
        $storeId = [];

        foreach ($cartItems as $item) {
            $response = Http::get("http://host.docker.internal:3000/stock/{$item->product_sku}");

            if ($response->failed()) {
                return redirect()->with('error', 'Stok servisine ulaşılmıyor');
            }

            $stockData = $response->json();
            if (!isset($stockData['stores'])) {
                return redirect()->back()->with('error', 'Yeterli stok yok');
            }

            $totalStock = 0;
            foreach ($stockData['stores'] as $store) {
                $totalStock += $store['stock'];

                $storeId[$item->product_sku] = $store['store_id'];
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
                for ($i = 0; $i < $item->product_piece; $i++) {
                    $orderLinesData[] = [
                        'product_sku' => $item->product_sku,
                        'product_name' => $item->product_name,
                        'store_id' => $store,
                        'order_id' => (count($groupedItems) > 1) ? $orderId . '-' . $subOrderId : $orderId,
                        'order_batch_id' => $orderId,
                    ];
                }
            }
            try {
                OrderLine::insert($orderLinesData);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Sipariş oluşturulurken hata oludu.' . $e->getMessage());
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
    $totalPrice = 0;
    foreach ($cartItems as $item) {
        $totalPrice += ($item->product_price * $item->product_piece);
    }
    $data = compact('cartItems', 'totalPrice');
    return view('cart_approve', $data);
}
public function update(Request $request, $productId)
{
    $quantity = $request->input('quantity');
    $cart = session()->get('cart', []);

    if(isset($cart[$productId])){
        $cart[$productId]['product_piece'] = $quantity;
        session()->put('cart', $cart);

       
        $totalPrice = 0;
        foreach($cart as $item) {
            $totalPrice += ($item['product_price'] * $item['product_piece']);
        }

        return response()->json(['success' => 'Sepet güncellendi', 'totalPrice' => $totalPrice]);
        
    }

    return response()->json(['error' => 'Ürün bulunamadı'], 400);
    
}
}