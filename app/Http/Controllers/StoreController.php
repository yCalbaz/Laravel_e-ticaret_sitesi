<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;


class StoreController extends Controller
{
public function create(){
    return view('depo_panel');
}

public function store(Request $request)
{
    $request->validate([
        'store_name'=> 'required|string|max:255',
        'store_max'=> 'required|numeric|min:0',
        'store_priority'=> 'required|numeric|min:0|unique:stores,store_priority'
    ]);


    Store::create([
        'store_name'=> $request->store_name,
        'store_max'=> $request->store_max,
        'store_priority'=> $request->store_priority
    ]);

    return redirect()->route('store.create.form')->with('success','Depo başarıyla eklendi :)');



}
}