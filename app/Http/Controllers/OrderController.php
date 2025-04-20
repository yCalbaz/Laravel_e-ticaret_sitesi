<?php

namespace App\Http\Controllers;

use App\Models\MemberStore;
use App\Models\OrderBatch;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{  
    public function index()
    {
        { if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
        }
        $memberId = Auth::id();
        
        $urunler = Product::where('customer_id', $memberId)->get();
        return view('seller_product', compact('urunler'));
        }
    }

    public function sellerProduct()
    { if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Lütfen giriş yapın.');
    }
    $memberId = Auth::id();
    
    $urunler = Product::where('customer_id', $memberId)->get();
    return view('seller_product', compact('urunler'));
    }
}