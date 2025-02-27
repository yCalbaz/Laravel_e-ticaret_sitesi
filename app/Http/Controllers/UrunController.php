<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class UrunController extends Controller
{

    public function index()
     {
        $products =Product::all();
        return view('urun', compact('products'));
     }

    
   
    
}
