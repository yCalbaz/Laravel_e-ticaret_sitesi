<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{


public function showCreateForm($productSku) {
    $product = Product::where('product_sku', $productSku)->firstOrFail();
    $sizes = Size::all();
    $memberID = Auth::id();
  
    $memberStore = DB::table('member_store')
    ->where('member_id',$memberID)
    ->join('stores','member_store.store_id','=','stores.id')
    ->select('stores.id' , 'stores.store_name')
    ->get();
    return view('stock_create_form',compact('product','sizes','memberStore'));
}

public function store(Request $request)
{
    $request->validate([
        'store_id'=>'required',
        'sizes' => 'required|nullable|array',
        'sizes.*' => 'required|numeric|min:1',
    ]);

    $storeExists = Store::where('id', $request->store_id)->exists();

    if (!$storeExists) {
        return redirect()->back()->withErrors(['store_id' => 'Geçerli depo girin.'])->withInput();
        }

    $memberId = Auth::id();

    $authorityStores = DB::table('member_store')
        ->where('member_id', $memberId)
        ->pluck('store_id')
        ->toArray();

    if (!in_array($request->store_id, $authorityStores)) {
        return redirect()->back()->withErrors(['store_id' => 'Bu depoya stok ekleme yetkiniz yok.']);
    }

    if ($request->has('sizes')) {
        foreach ($request->sizes as $sizeId => $piece) {
            if (in_array($sizeId, $request->input('size_ids', []))) {
                Stock::updateOrCreate(
                    [
                        'product_sku' => $request->product_sku,
                        'store_id' => $request->store_id,
                        'size_id' => $sizeId,
                    ],
                    [
                        'product_piece' => $piece,
                    ]
                );
            }
        }
        return redirect()->route('stock.create.form', ['product_sku' => $request->product_sku])->with('success', 'Stok başarıyla eklendi :)');
    } else {
        Stock::create([
            'product_sku' => $request->product_sku,
            'store_id' => $request->store_id,
            'product_piece' => $request->input('product_piece', 0),
            'size_id' => null,
        ]);

        return redirect()->route('stock.create.form', ['product_sku' => $request->product_sku])
        ->with('success', 'Stok başarıyla eklendi :)');
    }
}



}