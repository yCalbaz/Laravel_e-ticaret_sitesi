<?php
namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Product;
use App\Models\Member;
use App\Services\BasketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    protected $basketService; 

    public function __construct(BasketService $basketService)
    {
        $this->basketService = $basketService;
    }

    public function index()
    {
        if (Auth::check()) {
            $customer = Auth::user(); 
            Session::put('customer_id', $customer->customer_id); 
        }
        $customer = Session::get('customer_id');
      
    if (!$customer) {
        return view('sepet', ['cartItems' => []]); 
    }

    $basket = Basket::where('customer_id', $customer)->where('is_active', 1)->first();

    if (!$basket) {
        return view('sepet', ['cartItems' => []]); 
    }

    $cartItems = BasketItem::where('order_id', $basket->id)->get();
    return view('sepet', compact('cartItems'));
    }

    public function add(Request $request, Product $product)
    {
        try {
            $stockData = $this->basketService->getStockData($product->product_sku);

            $totalStock = 0;
            foreach ($stockData['stores'] as $store) {
                $totalStock += $store['stock'];
            }

            if ($totalStock < $request->quantity) {
                return redirect()->back()->with('error', 'Yeterli stok bulunmamaktadır.');
            }

            $customerId = Session::get('customer_id');
            if (!$customerId) {
                if (Auth::check()) {
                    $member = Member::where('id', Auth::id())->first();
                    $customerId = $member->customer_id;
                } else {
                    $customerId = mt_rand(10000000, 99999999);
                }
                Session::put('customer_id', $customerId);
            }

            $basket = $this->basketService->createOrUpdateBasket($customerId);

            $this->basketService->addProductToBasket($basket, $product, $request->quantity);

            return redirect()->back()->with('success', 'Ürün sepete eklendi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $cartItem = BasketItem::findOrFail($id);
        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Ürün sepetten kaldırıldı!');
    }

    public function approvl(Request $request)
    {
        $customerId = Session::get('customer_id');

        if (!$customerId) {
            if (Auth::check()) {
                $member = Member::where('id', Auth::id())->first();
                $customerId = $member->customer_id;
            } else {
                $customerId = null;
            }
        }

        $basket = Basket::where('customer_id', $customerId)->where('is_active', 1)->first();

        if (!$basket) {
            return redirect()->back()->with('error', 'Sepet bulunamadı.');
        }

        if ($request->isMethod('post')) {

            $request->validate([
                'adSoyad' => 'required|string|min:3|max:255',
                'adres' => 'required|string|min:3|max:255',
            ]);

            $cartItems = BasketItem::where('order_id', $basket->id)->get();

            try {
                $this->basketService->processOrder($basket, $customerId, $request->adSoyad, $request->adres, $cartItems);

                $basket->update(['is_active' => 0]);
                BasketItem::where('order_id', $basket->id)->delete();

                return redirect()->route('cart.index')->with('success', 'Sipariş onaylandı!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        $cartItems = BasketItem::where('order_id', $basket->id)->get();
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += ($item->product_price * $item->product_piece);
        }

        return view('sepet_onay', compact('cartItems', 'totalPrice'));
    }
}
