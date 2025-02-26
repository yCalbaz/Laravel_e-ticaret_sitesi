<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

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
            'store_id'=> 'required|numeric|min:0',
            'product_piece'=>'required|numeric|min:0',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('product_image');
        $imagePath = $image->store('images');
        $imageUrl = \Storage::url($imagePath);


        $product = new Product;
        $product->product_name = $request->product_name;
        $product->product_sku = $request->product_sku;
        $product->product_price = $request->product_price;
        $product->product_image = $imageUrl;
        $product->save();


        Stock::create([
            'product_sku'=> $request->product_sku,
            'store_id'=> $request->store_id,
            'product_piece'=> $request->product_piece,
        ]);
      

        return redirect()->route('products.create')->with('success', 'Ürün başarıyla eklendi.');
    }

    private function getImageFullUrl($image){
        return "/var/www/app/images". $image;
    }
}
