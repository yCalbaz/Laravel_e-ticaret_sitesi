<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;


class StockController extends Controller
{
public function create(){
    return view('stok_panel');
}

public function store(Request $request)
{
    $request->validate([
        'product_sku'=> 'required|string|max:255',
        'store_id'=> 'required|numeric|min:0',
        'product_piece'=> 'required|numeric'
    ]);


    Stock::create([
        'product_sku'=> $request->product_sku,
        'store_id'=> $request->store_id,
        'product_piece'=> $request->product_piece
    ]);

    return redirect()->route('stock.create.form')->with('success', 'Stok başarıyla eklendi :)');



}
}