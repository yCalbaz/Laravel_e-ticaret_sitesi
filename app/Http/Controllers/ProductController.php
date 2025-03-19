<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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

    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('product_name', 'LIKE', "%$query%")
            ->orWhere('details', 'LIKE', "%$query%")
            ->get();

        return view('search-results', compact('products', 'query'));
    }
}