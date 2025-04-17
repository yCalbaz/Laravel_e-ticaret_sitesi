<?php
namespace App\Http\Controllers;

use App\Models\MemberStore;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
public function index(){
    return view('store_panel');
}
public function store(Request $request)
{
    $request->validate([
        'store_name' => 'required|string|max:255',
        'store_max' => 'required|numeric|min:1',
        'store_priority' => 'required|numeric|min:1|unique:stores,store_priority'
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
        return redirect()->route('store.index.form')->with('error', 'Oturum bilgisi bulunamadı. Depo yetkisi atanamadı.');
    }

    return redirect()->route('store.index.form')->with('success', 'Depo başarıyla eklendi ve yetkisi verildi :)');
}

}