<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('product', ['urunler' => $products]);
    }
    

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
            ->orWhereHas('categories', function ($q) use ($query) {
                $q->where('category_name', 'LIKE', "%$query%");
            })
            ->get();

        return view('search-results', compact('products', 'query'));
    }
    
    public function productCategory($category_slug)
    {
        $kategori = Category::where('category_slug', $category_slug)->first();
        $altKategori = null;

        if (!$kategori) {
            $altKategori = Category::whereHas('parentCategories', function ($query) use ($category_slug) {
                $query->where('category_slug', $category_slug);
            })->first();

            if (!$altKategori) {
                return abort(404, 'Kategori bulunamadı.');
            }
        }

        $urunler = $kategori ? $kategori->products : $altKategori->products;
        //dd($urunler);

        return view('category_product', ['urunler' => $urunler, 'kategori' => $kategori ? $kategori->category_name : $altKategori->category_name]);
    }

    public function getProductsByCategory(Request $request)
{
    $categories = $request->categories;

    if (empty($categories)) {
        $urunler = Product::all();
    } else {
        $urunler = Product::whereHas('categories', function ($query) use ($categories) {
            $query->whereIn('category_slug', $categories);
        })->get();
    }

    return response()->json($urunler);
}

    public function filterProducts(Request $request)
{
    $categories = $request->input('categories', []);
    $genders = $request->input('genders', []);

    $urunler = Product::query();

    if (!empty($categories)) {
        $urunler->whereHas('categories', function ($query) use ($categories) {
            $query->whereIn('categories.id', $categories);
        });
    }

    if (!empty($genders)) {
        $urunler->whereIn('gender', $genders);
    }

    $urunler = $urunler->get();

    return view('partials.product_list', ['urunler' => $urunler]);
}
    

}