<?php
namespace App\Http\Controllers;

use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if (session('user_authority') !== self::SELLER_ROLE_ID) {
            return redirect()->route('login');
        }
    
        return redirect()->route('saticiPanel');
    }
    

    public function showMusteriPanel()
    {
        if(session('user_authority') !== self::CUSTOMER_ROLE_ID){
            return redirect()->route('login');
        }
        $products = $this->getProduct();
        return view('home', compact('products'));
    }
    public function showSellerStores()
{
    if (session('user_authority') !== self::SELLER_ROLE_ID) {
        return redirect()->route('login');
    }

    $memberId = Auth::id();
    $stores = DB::table('stores')
        ->join('member_store', 'stores.id', '=', 'member_store.store_id')
        ->where('member_store.member_id', $memberId)
        ->select('stores.id', 'stores.store_name') 
        ->get();

    return view('seller_store_selection', ['stores' => $stores]);
}

public function showSellerOrders($storeId)
{
    if (session('user_authority') !== self::SELLER_ROLE_ID) {
        return redirect()->route('login');
    }

    $siparisler = OrderLine::where('store_id', $storeId)->get();
    return view('seller_orders', ['siparisler' => $siparisler]);
}
public function updateLineStatus(Request $request,$lineId)
{
    
    $orderLine = OrderLine::find($lineId);
        $orderId = $orderLine->order_id;
        $storeId = $orderLine->store_id;
        $orderStatus = $request->input('order_status');

        OrderLine::where('order_id', $orderId)
            ->where('store_id', $storeId)
            ->update(['order_status' => $orderStatus]);
    return redirect()->back()->with('success', 'Sipariş durumu güncellendi.');
}

protected function getProduct()
    {
        return Product::orderBy('id', 'desc')->take(10)->get();
    }
}
