<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all()->filter(function($product) {
            try {
                $response = Http::timeout(4)->get("http://host.docker.internal:3000/stock/{$product->product_sku}");
    
                if ($response->successful()) {
                    $stockData = $response->json();
                    $stock = $stockData['stores'][0]['stock'] ?? 0;
                    return $stock > 0;
                }
            } catch (\Exception $e) {
                
            }
            return false;
        });
    
        return view('product', ['urunler' => $products]);
    }
    
    

    public function showDetails($sku)
    {
        
        $product = Product::where('product_sku', $sku)->firstOrFail();
        $product->load('stocks.size');
        $groupedStocks = $product->stocks->groupBy('size.id')->map(function ($items) {
            return [
                'size' => $items->first()->size,
                'total_piece' => $items->sum('product_piece'),
            ];
        })->values();
    

        
        return view('product_details', compact('product', 'groupedStocks'));
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

    
public function brand($brand_slug)
{
    $category = Category::where('category_slug', $brand_slug)->firstOrFail();

    $products = Product::where('product_name', 'LIKE', '%' . $category->category_name . '%')->get();

    return view('products.brand', compact('products', 'category'));
}

public function getSizes($sku)
{
    $product = Product::where('product_sku', $sku)->firstOrFail();
    $stocks = Stock::where('product_sku', $sku) 
        ->where('product_piece', '>', 0)
        ->whereNotNull('size_id')
        ->with('size')
        ->get();

    $sizes = $stocks->pluck('size');

    return response()->json($sizes);
}

}