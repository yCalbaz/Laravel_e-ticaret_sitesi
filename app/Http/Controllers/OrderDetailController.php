<?php

namespace App\Http\Controllers;

use App\Models\OrderBatch;
use App\Models\OrderCanceled;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class OrderDetailController extends Controller
{
    const SELLER_ROLE_ID = 2;
    const ORDER_STATUS_RECEIVED = 'sipariş alındı';
    const ORDER_STATUS_PREPARING = 'hazırlanıyor';
    const ORDER_STATUS_SHIPPED = 'kargoya verildi';
    const ORDER_STATUS_DELIVERED = 'teslim edildi';
    const ORDER_STATUS_CANCEL_REQUESTED = 'iptal talebi alındı';
    const ORDER_STATUS_CANCEL_APPROVED = 'iptal talebi onaylandı';
    public function index()
    {
        if (Auth::check()) {
            $customer = Auth::user();
            Session::put('customer_id', $customer->customer_id);
        }
        $customer = Session::get('customer_id');
        
        if (!$customer) {
            return view('order_details', ['orders' => []]);
        }

        $orders = OrderBatch::with('orderLines.product')
        ->where('customer_id', $customer)
        ->orderBy('created_at', 'desc')
        ->paginate(5);  


        foreach ($orders as $order) {
            $order->totalPrice = $order->orderLines->sum(function ($line) {
                return $line->product_price * $line->product_piece;
            });
        }

        return view('order_details', compact('orders'))->with([
            'status_received' => self::ORDER_STATUS_RECEIVED,
            'status_preparing' => self::ORDER_STATUS_PREPARING,
            'status_shipped' => self::ORDER_STATUS_SHIPPED,
            'status_delivered' => self::ORDER_STATUS_DELIVERED,
            'status_canseled' => self::ORDER_STATUS_CANCEL_REQUESTED,
            'status_canseled_approve' => self::ORDER_STATUS_CANCEL_APPROVED,
        ]);;
    
}
    

    public function showDetails($orderId) 

    {
        $order = OrderBatch::with(['orderLines.product', 'orderLines.store', 'orderLines.size'])->where('order_id', $orderId)->first();
        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }

        $allOrderStatuses = ['sipariş alındı', 'hazırlanıyor', 'kargoya verildi',];
        $orderStatusHistory = $order->orderLines->pluck('order_status')->toArray();
        $isCancelable = true;
        foreach ($order->orderLines as $line) {
            if (in_array($line->order_status, ['iptal talebi alındı', 'iptal talebi onaylandı'])) {
                $isCancelable = false;
                break; 
            }
        }
        return view('order_details_show', compact('order', 'allOrderStatuses', 'orderStatusHistory','isCancelable'));
    }

    public function showReturnForm(Request $request)
    {
        //ajax uyarısı için 
        $orderId = $request->orderId;
    
        $order = OrderBatch::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Sipariş bulunamadı.'], 404);
        }
    
        if ($order->created_at->diffInDays(now()) > 15) {
            return response()->json(['error' => 'Bu sipariş için iade süresi dolmuştur.'], 400);
        }
    
        return response()->json(['success' => 'İptal formu başarıyla alındı.']);
    }
    

    public function processReturn(Request $request)
    {
        $request->validate([
            'details' => 'required|string',
            'return_address' => 'nullable|string',
            'product_sku' => 'required|array', // Birden fazla SKU beklenebilir
        ]);
    
        $order = OrderBatch::where('id', $request->order_id)->first();
        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }
    
        if ($order->created_at->diffInDays(now()) > 15) {
            return response()->json('Bu sipariş için iade süresi dolmuştur.');
        }
    
        $storeId = $request->store_id;
        $productSkusToReturn = $request->product_sku; 
        $skuList = implode(',', $productSkusToReturn);
        $productImages = [];
        $productPrices = [];
    
        DB::beginTransaction();
        try {
            
            $allOrderLinesOfStore = $order->orderLines()->where('store_id', $storeId)->get();
            foreach ($allOrderLinesOfStore as $orderLine) {
                $orderLine->update(['order_status' => self::ORDER_STATUS_CANCEL_REQUESTED]);
                if ($orderLine->product) {
                    $productImages[] = $orderLine->product->product_image;
                    $productPrices[] = $orderLine->product->product_price;
                }
            }
    
            $totalPrice = array_sum($productPrices);
    
            OrderCanceled::create([
                'order_id' => $request->order_id,
                'product_sku' => $skuList, 
                'details' => $request->details,
                'store_id' => $storeId,
                'product_price' => $totalPrice,
                'product_image' => implode(',', array_unique($productImages)),
                'customer_id' => Auth::user()->customer_id,
                'return_address' => $request->return_address
            ]);
    
            DB::commit();
            return redirect()->route('orders.index')->with('success', 'İade talebiniz alındı.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'İade işlemi sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }
    

    public function showCanceledForm(Request $request)
    {
        $orderId = $request->query('orderId');
        $storeId = $request->query('storeId');

        $order = OrderBatch::with('orderLines.product')->find($orderId);

        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }
        
        $filteredOrderLines = $order->orderLines->where('store_id', $storeId);
        $totalPrice = 0;
        foreach ($filteredOrderLines as $line) {
            //dd($line->product->product_price, $line->quantity);
            $totalPrice += $line->product->product_price * $line->quantity;
        }
        
        $order->setRelation('orderLines', $filteredOrderLines);
        $order->totalPrice = $totalPrice;

        return view('order_canceled_form', compact('order', 'orderId', 'storeId'));
    } 

    public function adminOrders()
    {
        $orders = OrderBatch::with('orderLines.product')->orderBy('created_at', 'desc')->paginate(4);  

         
        foreach ($orders as $order) {
            $order->totalPrice = $order->orderLines->sum(function ($line) {
                return $line->product_price * $line->product_piece;
            });
        }

        return view('admin_order_detail', compact('orders'));
    }

    public function showAdminDetails($orderId)
    {
        $order = OrderBatch::with(['orderLines.product', 'orderLines.store'])->where('id', $orderId)->first();
        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }
    
        $groupedOrderLines = $order->orderLines->groupBy(function ($line) {
            return $line->product_sku . '-' . $line->product_size;
        })->map(function ($lines) {
            $firstLine = $lines->first();
            $firstLine->product_piece = $lines->sum('quantity');
            return $firstLine;
        })->values(); 
    
        $allOrderStatuses = ['sipariş alındı', 'hazırlanıyor', 'kargoya verildi'];
        $orderStatusHistory = $order->orderLines->pluck('order_status')->toArray();
    
        return view('admin_order_details_show', compact('order', 'groupedOrderLines', 'allOrderStatuses', 'orderStatusHistory'));
    }

    
} 