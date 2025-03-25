<?php

namespace App\Http\Controllers;

use App\Models\OrderBatch;
use App\Models\OrderCanceled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class OrderDetailController extends Controller
{
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

       
        $orders = OrderBatch::with('orderLines')->where('customer_id', $customer)->get();

        return view('order_details', compact('orders'));
    }
    

    public function showDetails($orderId)
    {
        
        $order = OrderBatch::with(['orderLines.product'])->where('order_id', $orderId)->first();

        if (!$order) {
            return back()->with('error', 'Sipariş bulunamadı.');
        }

        return view('order_details_show', compact('order'));
    }

    public function showReturnForm(Request $request)
    {
        return view('order_canceled_form');
    }

    public function processReturn(Request $request)
    {
        //dd("fonksiyona girdi");
        $request->validate([
            dd($request),
            'order_id' => 'required',
            'product_sku' => 'required',
            'details' => 'required|string',
        ]);

        $order = OrderBatch::where('id', $request->order_id)->first();
        if (!$order) { 
            return back()->with('error', 'Sipariş bulunamadı.');
        }

        OrderCanceled::create([
            'order_id' => $request->order_id,
            'product_sku' => $request->product_sku,
            'details' => $request->details,
            'store_id'=>$request->store_id,
            'product_price' => $order->orderLines->where('product_sku', $request->product_sku)->first()->product_price ?? 0,
            'product_image'=>$order->orderLines->where('product_image',$request->product_sku)->first()->product_image ?? 0,

            'customer_id' => Auth::user()->customer_id,
        ]);

        return redirect()->route('orders.index')->with('success', 'İade talebiniz alındı.');
    }
}
