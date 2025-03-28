<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ManagerController extends Controller
{
    const ADMIN_ROLE_ID = 1;
    const SELLER_ROLE_ID = 2;
    const CUSTOMER_ROLE_ID = 3;

    public function showAdminPanel()
    {
        if(session('user_authority') !== self::ADMIN_ROLE_ID){
            return redirect()->route('login');
        }
        return view('admin_panel');
    }

    public function showSaticiPanel()
    {
        if(session('user_authority') !== self::SELLER_ROLE_ID){
            return redirect()->route('login');
        }
        $memberId = Auth::id();
        $stores = DB::table('stores')
        ->join('member_store', 'stores.id', '=', 'member_store.store_id')
        ->where('member_store.member_id', $memberId)
        ->select('stores.*')
        ->get();

        
        return view('seller_panel', ['stores' => $stores]);
    }

    public function showMusteriPanel()
    {
        if(session('user_authority') !== self::CUSTOMER_ROLE_ID){
            return redirect()->route('login');
        }
        $products = $this->getProduct();
        return view('home', compact('products'));
    }

    protected function getProduct()
    {
        return Product::orderBy('id', 'desc')->take(10)->get();
    }
}
