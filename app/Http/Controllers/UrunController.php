<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class UrunController extends Controller
{

    public function index()
     {
        $products =Produc::take(10)->get();
        return view('urun', compact('products'));
     }

    
   
    
}
