<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
     {
        $products =Product::take(10)->get();
        return view('anasayfa', compact('products'));
     }

    
    public function create()
    {
        return view('urun_panel'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_sku' => 'required|string|unique:products,product_sku',
            'product_price' => 'required|numeric|min:0',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('product_image');
        $imagePath = $image->store('images');
        $imageUrl = Storage::url($imagePath);


        $product = new Product;
        $product->product_name = $request->product_name;
        $product->product_sku = $request->product_sku;
        $product->product_price = $request->product_price;
        $product->product_image = $imageUrl;
        $product->save();

        return redirect()->route('product.create.form')->with('success', 'Ürün başarıyla eklendi.');
    }

    
}
