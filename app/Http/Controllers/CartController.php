<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    
    public function index()
    {
        $cartItems = Cart::all();
        return view('sepet', compact('carts'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'product_name'  => 'required|string|max:255',
            'product_sku'   => 'required|string|max:100',
            'product_price' => 'required|numeric',
            'product_image' => 'nullable|string',
            'product_piece' => 'required|integer|min:1',
        ]);

        Cart::create($request->all());

        return redirect()->route('cart.index')->with('success', 'Ürün sepete eklendi!');
    }

    
    public function update(Request $request, $id)
    {
        $cartItem = Cart::findOrFail($id);

        $request->validate([
            'product_name'  => 'required|string|max:255',
            'product_sku'   => 'required|string|max:100',
            'product_price' => 'required|numeric',
            'product_image' => 'nullable|string',
            'product_piece' => 'required|integer|min:1',
        ]);

        $cartItem->update($request->all());

        return redirect()->route('cart.index')->with('success', 'Ürün güncellendi!');
    }

   
    public function destroy($id)
    {
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Ürün sepetten kaldırıldı!');
    }

    
}
