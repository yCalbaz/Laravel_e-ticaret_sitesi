<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Store;

class StockController extends Controller
{
public function index(){
    return view('stock_panel');
}

public function store(Request $request)
{
    $request->validate([
        'product_sku'=> 'required|string|max:255',
        'store_id'=> 'required|numeric|min:0',
        'product_piece'=> 'required|numeric'
    ]);
    $storeExists = Store::where('id', $request->store_id)->exists();

        if (!$storeExists) {
            return redirect()->back()->withErrors(['store_id' => 'Geçerli depo girin.']);
        }


    Stock::create([
        'product_sku'=> $request->product_sku,
        'store_id'=> $request->store_id,
        'product_piece'=> $request->product_piece
    ]);

    return redirect()->route('stock.index.form')->with('success', 'Stok başarıyla eklendi :)');

}



}