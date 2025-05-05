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


public function showCreateForm($productSku) 
{
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
        'sizes' => 'required|array',
        'sizes.*' => 'required|numeric|min:1',
        'size_ids' => 'required|array',
    ]);

    $storeExists = Store::where('id', $request->store_id)->exists();

    if (!$storeExists) {
        return response()->json(['error' => 'Geçerli depo girin.'], 400);
        }

    $memberId = Auth::id();

    $authorityStores = DB::table('member_store')
        ->where('member_id', $memberId)
        ->pluck('store_id')
        ->toArray();

    if (!in_array($request->store_id, $authorityStores)) {
        return response()->json([
            'error' => 'Bu depoya stok ekleme yetkiniz yok.'
        ], 403);
    }
    
    
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
        return response()->json([
            'success' => 'Stok başarıyla eklendi :)'
        ], 200);
    }


}