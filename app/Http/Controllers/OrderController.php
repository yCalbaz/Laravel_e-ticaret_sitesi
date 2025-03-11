<?php

namespace App\Http\Controllers;

use App\Models\OrderBatch;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = OrderBatch::with('orderLines')->get(); 

        return view('order', compact('orders'));
    }
}