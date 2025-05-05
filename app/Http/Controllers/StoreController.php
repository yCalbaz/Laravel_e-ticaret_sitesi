<?php
namespace App\Http\Controllers;

use App\Models\MemberStore;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
public function index()
{
    return view('store_panel');
}

public function store(Request $request)
{
    $request->validate([
        'store_name' => 'required|string|max:255',
        'store_max' => 'required|numeric|min:1',
        'store_priority' => 'required|numeric|min:1|unique:stores,store_priority'
    ],[
        'store_name.required'=>'Depo ismi boş geçilemez',
        'store_name.string'=>'Depo ismi geçerli değil',
        'store_name.max'=>'Depo ismi çok uzun',

        'store_max.required'=>'Max satış adedi boş geçilemez',
        'store_max.numeric'=>'Max satış adedi sayı olmalıdır',
        'store_max.min'=>'Max satış adedi geçerli sayı olmalıdır',

        'store_priority.required'=>'Depo önceliği boş geçilemez',
        'store_priority.min'=>'Depo önceliği geçerli sayı olmalıdır',
        'store_priority.numeric'=>'Depo önceliği sayı olmalıdır',
        'store_priority.unique'=>'Depo önceliği geçerli değil',
    ]);

    $store = Store::create([
        'store_name' => $request->store_name,
        'store_max' => $request->store_max,
        'store_priority' => $request->store_priority,
        'is_active' => 1 
    ]);

    $memberId = Auth::id();
    //dd($memberId);

    if ($memberId) {
        MemberStore::create([
            'member_id' => $memberId,
            'store_id' => $store->id,
        ]);
    } else {
        return response()->json(['error' => 'Oturum bilgisi bulunamadı. Depo yetkisi atanamadı.' ], 401);
    }

    return response()->json(['success'=> 'Depo başarıyla eklendi ve yetkisi verildi :)'
    ]);
}

}