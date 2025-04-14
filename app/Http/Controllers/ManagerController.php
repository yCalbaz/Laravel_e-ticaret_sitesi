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
        
        $siparisler = OrderLine::where('store_id', $storeId)
        ->orderBy('id', 'desc')
        ->get();
        
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

    public function approveCancellation(Request $request) //iade onaylamınca ilgili depoya stok gönder
{
    $orderId = $request->input('order_id');
    $storeId = $request->input('store_id');

    $orderLinesToCancel = OrderLine::where('order_id', $orderId)
        ->where('store_id', $storeId)
        ->where('order_status', 'iptal talebi alındı')
        ->get();
    //dd($orderLinesToCancel);
    DB::beginTransaction();
    try {
        foreach ($orderLinesToCancel as $orderLine) {
            //dd('İşlenen OrderLine:', $orderLine->id);
            $orderLine->update(['order_status' => 'iptal talebi onaylandı']);

            $product = Product::where('product_sku', $orderLine->product_sku)->first();
            //dd('Bulunan Ürün:', $product);
            if ($product) {
                //dd('Bulunan Ürün SKU:', $product->product_sku);
                $stock = \App\Models\Stock::where('product_sku', $orderLine->product_sku)
                    ->where('store_id', $orderLine->store_id)
                    ->where('size_id', $orderLine->product_size_id)
                    ->first();
                //dd('Bulunan Stok:', $stock);
                if ($stock) {
                    $stock->increment('product_piece', $orderLine->quantity);
                } else {
                    \App\Models\Stock::create([
                        'product_sku' => $orderLine->product_sku, 
                        'store_id' => $orderLine->store_id,
                        'product_piece' => $orderLine->quantity,
                        'size_id' => $orderLine->product_size_id,
                    ]);
                }
            }
        }
        //dd($product);
        DB::commit();
        return back()->with('success', $orderId . ' ID\'li siparişin ' . $storeId . ' deposundan çıkan ürünlerinin iptal talebi onaylandı ve stoklar güncellendi.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'İptal talebi onaylanırken bir hata oluştu: ' . $e->getMessage());
    }
}

    public function updateLineStatusForStore(Request $request)
{
    $orderId = $request->input('order_id');
    $storeId = $request->input('store_id');
    $orderStatus = $request->input('order_status');

    OrderLine::where('order_id', $orderId)
        ->where('store_id', $storeId)
        ->where('order_status', '!=', 'iptal talebi onaylandı') 
        ->update(['order_status' => $orderStatus]);

    return back()->with('success', $storeId . ' deposundaki ürünlerin durumu ' . $orderStatus . ' olarak güncellendi.');
}
}
