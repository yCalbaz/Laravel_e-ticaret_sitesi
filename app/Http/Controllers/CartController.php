<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderBatch;
use App\Models\OrderLine;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::all();
        return view('sepet', compact('cartItems'));
    }

    public function add(Request $request, Product $product)
{
    $response = Http::get("http://host.docker.internal:3000/stock/{$product->product_sku}");
    
    if($response->failed()) {
        return redirect()->back()->with('error','Stok servise ulasılmadı');
    }
    
    $stockData = $response->json();
    
    
    if (!isset($stockData['total_stock'])) {
        return redirect()->back()->with('error', 'Stok verisi alınamadı.');
    }

    
    if ($stockData['total_stock'] < $request->quantity) {
        return redirect()->back()->with('error', 'Yeterli stok bulunmamaktadır.');
    }
    
   
    $cartItem = Cart::where('product_sku', $product->product_sku)->first();

   
    if ($cartItem) {
        if ($cartItem->product_piece + $request->quantity > $stockData['total_stock']) {
            return redirect()->back()->with('error', 'Yeterli stok bulunmamaktadır.');
        }

        $cartItem->product_piece += $request->quantity;
        $cartItem->save();
    } else {
        Cart::create([
            'product_name' => $product->product_name,
            'product_sku' => $product->product_sku,
            'product_piece' => $request->quantity,
            'product_price' => $product->product_price,
            'product_image' => $product->product_image,
        ]);
    }

    return redirect()->back()->with('success', 'Ürün sepete eklendi');
}


    public function delete($id)
    {
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Ürün sepetten kaldırıldı!');
    }

    public function approvl(Request $request)
    {
        
        if ($request->isMethod('post')) {
            $cartItems = Cart::all();
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += ($item->product_price * $item->product_piece);
            }

            $adSoyad = $request->input('adSoyad');
            $adres = $request->input('adres');

            $orderBatch = OrderBatch::create([
                'customer_name' => $adSoyad,
                'customer_address' => $adres,
                'product_price' => $totalPrice,
            ]);

            $orderId = $orderBatch->id;

            $orderBatch->order_id = $orderId;
            $orderBatch->save();

            foreach ($cartItems as $item) {
                OrderLine::create([
                    'product_sku' => $item->product_sku,
                    'product_name' => $item->product_name,
                    'store_id' => 1,
                    'product_piece' => $item->product_piece,
                    'order_id' => $orderId,
                ]);
            }
            Cart::truncate(); 

        return redirect()->route('sepet.approvl')->with('success', 'Siparişiniz başarıyla oluşturuldu.');
    

             }

        $cartItems = Cart::all();
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += ($item->product_price * $item->product_piece);
        }

        return view('sepet_onay', compact('cartItems', 'totalPrice'));
    }
}