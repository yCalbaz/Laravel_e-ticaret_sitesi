<?php

namespace App\Http\Controllers;

use App\Models\OrderBatch;
use App\Models\OrderCanceled;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class OrderDetailController extends Controller
{
    const SELLER_ROLE_ID = 2;
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

        $orders = OrderBatch::with('orderLines.product')->where('customer_id', $customer)->orderBy('created_at', 'desc')->get();

        
        foreach ($orders as $order) {
            $order->totalPrice = $order->orderLines->sum(function ($line) {
                return $line->product_price * $line->product_piece;
            });
        }

        return view('order_details', compact('orders'));
    
}
    

public function showDetails($orderId)

{

$order = OrderBatch::with(['orderLines.product', 'orderLines.store'])->where('order_id', $orderId)->first();



if (!$order) {

return back()->with('error', 'Sipariş bulunamadı.');

}



$allOrderStatuses = ['sipariş alındı', 'hazırlanıyor', 'kargoya verildi', 'iptal edildi'];



$orderStatusHistory = $order->orderLines->pluck('order_status')->toArray();

return view('order_details_show', compact('order', 'allOrderStatuses', 'orderStatusHistory'));

}


    public function showReturnForm(Request $request)
    {
        $orderId = $request->orderId;
        $storeId = $request->store_id;
    
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
            'order_id' => 'required',
            'product_sku' => 'required',
            'details' => 'required|string',
        ]);
    
        $order = OrderBatch::where('id', $request->order_id)->first();
        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }
        if ($order->created_at->diffInDays(now()) > 15) {
            return response()->json('Bu sipariş için iade süresi dolmuştur.');
        }
    
        $storeId = $request->store_id;
        $orderLine = $order->orderLines()->where('product_sku', $request->product_sku)->where('store_id', $storeId)->first();
        $productImage = $orderLine ? $orderLine->product_image : null;
        $productPrice = $orderLine ? $orderLine->product_price : 0;
        
        
    
        OrderCanceled::create([
            'order_id' => $request->order_id,
            'product_sku' => $request->product_sku,
            'details' => $request->details,
            'store_id' => $request->store_id,
            'product_price' => $productPrice,
            'product_image' => $productImage,
            'customer_id' => Auth::user()->customer_id,
        ]);
    
        return redirect()->route('orders.index')->with('success', 'İade talebiniz alındı.');
    }

    public function showCanceledForm(Request $request)
{
    $orderId = $request->query('orderId');
    $storeId = $request->query('storeId');

    $order = OrderBatch::with('orderLines.product')->find($orderId);

    if (!$order) {
        return back()->with('error', 'Sipariş bulunamadı.');
    }

    $order->totalPrice = $order->orderLines->sum(function ($line) {
        return $line->product_price * $line->product_piece;
    });

    return view('order_canceled_form', compact('order', 'orderId', 'storeId'));
}
    
    
}