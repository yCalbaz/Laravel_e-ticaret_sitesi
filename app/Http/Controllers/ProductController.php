<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // 🔹 Form verilerini doğrula
        $request->validate([
            'product_name'  => 'required|string|max:255',
            'product_sku'   => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 🔹 Resmi kaydet
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('product_images', 'public');
        } else {
            return back()->withErrors(['product_image' => 'Resim yüklenemedi.']);
        }

        // 🔹 Veriyi veritabanına ekleme
        Product::create([
            'product_name'  => $request->product_name,
            'product_sku'   => $request->product_sku,
            'product_price' => $request->product_price,
            'product_image' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Ürün başarıyla eklendi!');
    }
}
