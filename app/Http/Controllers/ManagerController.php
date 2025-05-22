<?php
namespace App\Http\Controllers;

use App\Models\ModelLog;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    const ADMIN_ROLE_ID = 1;
    const SELLER_ROLE_ID = 2;
    const CUSTOMER_ROLE_ID = 3;
    
    protected function getProduct()
    {
        return Product::orderBy('id', 'desc')->take(10)->get();
    }
     
    public function showAdminPanel()
    {
        if(session('user_authority') !== self::ADMIN_ROLE_ID){
            return redirect()->route('login');
        }
        return view('admin_panel');
    }

    public function showSellerPanel()
    {
        if (session('user_authority') !== self::SELLER_ROLE_ID) {
            return redirect()->route('login');
        }
    
        return redirect()->route('saticiPanel');
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

        DB::beginTransaction();
        try {
            $orderLineIdsToCancel = OrderLine::where('order_id', $orderId)
                ->where('store_id', $storeId)
                ->where('order_status', 'iptal talebi alındı')
                ->pluck('id');

            if ($orderLineIdsToCancel->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'Bu sipariş ID\'si ve mağaza ID\'si ile iptal talebi alınmış sipariş kalemi bulunamadı.'
                ]);
            }

            OrderLine::whereIn('id', $orderLineIdsToCancel)->update(['order_status' => 'iptal talebi onaylandı']);

            $cancelledOrderLinesDetails = OrderLine::whereIn('id', $orderLineIdsToCancel)
                ->select('product_sku', 'store_id', 'product_size_id', 'quantity')
                ->get();

            $stockChanges = [];
            foreach ($cancelledOrderLinesDetails as $orderLine) {
                $key = $orderLine->product_sku . '_' . $orderLine->store_id . '_' . $orderLine->product_size_id;
                if (!isset($stockChanges[$key])) {
                    $stockChanges[$key] = [
                        'product_sku' => $orderLine->product_sku,
                        'store_id' => $orderLine->store_id,
                        'product_size_id' => $orderLine->product_size_id,
                        'total_quantity' => 0,
                    ];
                }
                $stockChanges[$key]['total_quantity'] += $orderLine->quantity;
            }

            foreach ($stockChanges as $change) {
                $stock = Stock::where('product_sku', $change['product_sku'])
                    ->where('store_id', $change['store_id'])
                    ->where('size_id', $change['product_size_id'])
                    ->first();

                if ($stock) {
                    $stock->increment('product_piece', $change['total_quantity']);
                } else {
                    Stock::create([
                        'product_sku' => $change['product_sku'],
                        'store_id' => $change['store_id'],
                        'product_piece' => $change['total_quantity'],
                        'size_id' => $change['product_size_id'],
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $orderId . ' ID\'li siparişin iptal talebi onaylandı ve stoklar güncellendi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'İptal talebi onaylanırken bir hata oluştu: ' . $e->getMessage()
            ]);
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
