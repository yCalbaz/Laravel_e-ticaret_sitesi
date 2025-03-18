<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function showDetails($sku)
    {
        
        $product = Product::where('product_sku', $sku)->first();

        
        if (!$product) {
            abort(404, 'Ürün bulunamadı.');
        }

        
        return view('product_details', compact('product'));
    }
}
