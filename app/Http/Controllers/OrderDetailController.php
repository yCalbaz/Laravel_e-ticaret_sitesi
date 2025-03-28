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
    
        return view('order_details_show', compact('order'));
    }

    public function showReturnForm(Request $request)
    {
        $orderId = $request->id;
        dd($orderId);
        $productSku = $request->product_sku; 
        $productPrice = $request->product_price;
    
        $order = OrderBatch::find($orderId);
        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }
    
        $fifteenDaysAgo = Carbon::now()->subDays(15);
        if ($order->created_at < $fifteenDaysAgo) {
            return back()->with('error', 'Bu sipariş için iade süresi dolmuştur.');
        }
    
        return view('order_canceled_form', compact('orderId', 'productSku', 'productPrice')); 
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
    
        $orderLine = $order->orderLines()->where('product_sku', $request->product_sku)->first();
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
    
    
}