<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderBatch;
use App\Models\OrderLive;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::all();
        return view('sepet', compact('cartItems'));
    }

    public function add(Request $request, Product $product)
    {
        $cartItem = Cart::where('product_sku', $product->product_sku)->first();

        if ($cartItem) {
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
                OrderLive::create([
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