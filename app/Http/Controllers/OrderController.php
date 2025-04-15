<?php

namespace App\Http\Controllers;

use App\Models\OrderBatch;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = OrderBatch::with('orderLines')->get(); 

        return view('order_panel', compact('orders'));
    }

    public function sellerProduct()
    {
        $urunler = Product::all();
        return view('seller_product' , compact('urunler'));
    }
}