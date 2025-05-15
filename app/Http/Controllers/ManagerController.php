<?php
namespace App\Http\Controllers;

use App\Models\ConfigModel;
use App\Models\ModelLog;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ManagerController extends Controller
{
    const ADMIN_ROLE_ID = 1;
    const SELLER_ROLE_ID = 2;
    const CUSTOMER_ROLE_ID = 3;
    
    protected function getProduct()
    {
        return Product::orderBy('id', 'desc')->take(10)->get();
    }

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
     
    public function showAdminPanel()
    {
        if(session('user_authority') !== self::ADMIN_ROLE_ID){
            return redirect()->route('login');
        }
        return view('admin_panel');
    }

    public function showSaticiPanel()
    {
        if (session('user_authority') !== self::SELLER_ROLE_ID) {
            return redirect()->route('login');
        }
    
        return redirect()->route('saticiPanel');
    }
    
    public function showMusteriPanel()
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
    public function showSellerStores()
    {
        if (session('user_authority') !== self::SELLER_ROLE_ID) {
            return redirect()->route('login');
        }

        $memberId = Auth::id();
        $stores = DB::table('stores')
            ->join('member_store', 'stores.id', '=', 'member_store.store_id')
            ->where('member_store.member_id', $memberId)
            ->select('stores.id', 'stores.store_name') 
            ->get();

        return view('seller_store_selection', ['stores' => $stores]);
    }

    public function showSellerOrders(Request $request, $storeId)
    {
        if (session('user_authority') !== self::SELLER_ROLE_ID) {
            return redirect()->route('login');
        }
    
        $orderStatusFilter = $request->query('order_status');
    
        $orders = OrderLine::with('size', 'product') 
            ->where('store_id', $storeId)
            ->when($orderStatusFilter, function ($query, $orderStatusFilter) {
                return $query->where('order_status', $orderStatusFilter);
            })
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('order_id') 
            ->map(function ($orderLines) {
                return $orderLines->groupBy('store_id'); 
            });
    
        return view('seller_orders', ['groupedOrders' => $orders, 'orderStatusFilter' => $orderStatusFilter]);
    }

    public function updateLineStatusForStore(Request $request)
    {
        $orderId = $request->input('order_id');
        $storeId = $request->input('store_id');
        $newStatus = $request->input('order_status');

        $orderLines = OrderLine::where('order_id', $orderId)
            ->where('store_id', $storeId)
            ->where('order_status', '!=', 'iptal talebi onaylandı')
            ->get();

        foreach ($orderLines as $orderLine) {
            $currentStatus = $orderLine->order_status;
            $allowedTransitions = [
                'sipariş alındı' => ['hazırlanıyor', 'iptal talebi alındı'],
                'hazırlanıyor' => ['kargoya verildi', 'iptal talebi alındı'],
                'kargoya verildi' => [],
                'iptal talebi alındı' => ['iptal talebi onaylandı'],
                'iptal talebi onaylandı' => [],
            ];

            if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
                return response()->json(['error' => "Geçersiz statü geçişi yaptınız."], 400);
            }

            $orderLine->update(['order_status' => $newStatus]);
        }

        return response()->json(['success' => true, 'message' =>'Sipariş Durumu: ' . $newStatus . ' olarak güncellendi.']);
    }

    public function approveCancellation(Request $request) 
    {
        $orderId = $request->input('order_id');
        $storeId = $request->input('store_id');
    
        $orderLinesToCancel = OrderLine::where('order_id', $orderId)
            ->where('store_id', $storeId)
            ->where('order_status', 'iptal talebi alındı')
            ->get();
    
        DB::beginTransaction();
        try {
            foreach ($orderLinesToCancel as $orderLine) {
                $orderLine->update(['order_status' => 'iptal talebi onaylandı']);
    
                $product = Product::where('product_sku', $orderLine->product_sku)->first();
                if ($product) {
                    $stock = \App\Models\Stock::where('product_sku', $orderLine->product_sku)
                        ->where('store_id', $orderLine->store_id)
                        ->where('size_id', $orderLine->product_size_id)
                        ->first();
                    if ($stock) {
                        $stock->increment('product_piece', $orderLine->quantity);
                    } else {
                        \App\Models\Stock::create([
                            'product_sku' => $orderLine->product_sku,
                            'store_id' => $orderLine->store_id,
                            'product_piece' => $orderLine->quantity,
                            'size_id' => $orderLine->product_size_id,
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => $orderId . ' ID\'li siparişin iptal talebi onaylandı ve stoklar güncellendi.']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'İptal talebi onaylanırken bir hata oluştu: ' . $e->getMessage()]);
        }
    }

    public function campaignAdd(Request $request, $id)
    {
        $request->validate([
            'discount_rate' => 'required|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($id);
        $discountRate = $request->input('discount_rate');

        $product->update([
            'discount_rate' => $discountRate,
        ]);

        return redirect()->route('seller.products')->with('success', $product->product_name . ' ürününe %' . $discountRate . ' kampanya eklendi.');
    }
    
}
