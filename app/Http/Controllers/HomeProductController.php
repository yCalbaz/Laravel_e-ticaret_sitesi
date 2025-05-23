<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ConfigModel;
use App\Models\ModelLog;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HomeProductController extends Controller 
{ 
    public function index()
    {
        $categories = Category::all();
        return view('product_panel', compact('categories'));
    } 

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_sku' => 'required|string|unique:products,product_sku',
            'product_price' => 'required|numeric|min:0.01|max:9999999',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_ids' => 'required|array|exists:categories,id',
            'details'=>'required',
        ],[
            'product_name.required' => 'Ürün adı gereklidir.',
            'product_name.string' => 'Ürün adı geçerli bir metin olmalıdır.',
            'product_name.max' => 'Ürün adı 255 karakterden uzun olamaz.',
            'product_sku.required' => 'Ürün SKU kodu gereklidir.',
            'product_sku.unique' => 'Bu SKU kodu zaten kullanılıyor.',
            'product_price.required' => 'Ürün fiyatı gereklidir.',
            'product_price.numeric' => 'Ürün fiyatı geçerli bir sayı olmalıdır.',
            'product_price.min' => 'Ürün fiyatı sıfırdan büyük olmalıdır.',
            'product_price.max' => 'Ürün fiyatı çok fazla.',
            'product_image.image' => 'Ürün görseli geçerli dosya olmalıdır.',
            'product_image.mimes' => 'Ürün görseli yalnızca jpeg, png, jpg veya gif formatlarında olabilir.',
            'product_image.max' => 'Ürün görseli boyutu çok fazla.',
            'category_ids.required' => 'Kategori seçimi gereklidir.',
            'category_ids.array' => 'Kategori seçimi gereklidir,',
            'category_ids.exists' => 'Seçilen kategori mevcut değil.',
            'details.required' => 'Ürün açıklaması gereklidir.',
        ]);

        if(Auth::check()){
            $customer_id =Auth::id();
        }else{
            return response()->json(['error' => 'Ürün eklemek için oturum açın.'], 401);
        }

        $image = $request->file('product_image');
        $imagePath = $image->store('images');
        $imageUrl = Storage::url($imagePath);


        $product = new Product;
        $product->product_name = $request->product_name;
        $product->product_sku = $request->product_sku;
        $product->product_price = $request->product_price;
        $product->product_image = $imageUrl;
        $product->details= $request->details;
        $product->customer_id= $customer_id;
        $product->save();

        if ($request->category_ids) {
            $product->categories()->attach($request->category_ids);
        }

        $productCategories = $product->categories()->pluck('category_name')->toArray();

        return response()->json([
            'success' => 'Ürün başarıyla eklendi :)'
        ], 200);

    }

    
}
