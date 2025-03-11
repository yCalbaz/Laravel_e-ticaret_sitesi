<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Models\Product;



Route::get('/', function () {
    $products = Product::orderBy('id', 'desc')->take(10)->get(); 
    return view('anasayfa', compact('products'));
});

    Route::get('/login', [AdminPanelController::class, 'showLoginForm'])->name('login'); 
    Route::post('/login', [AdminPanelController::class, 'login'])->name('login.post'); 


Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout'); 

Route::middleware(['auth'])->group(function () {
    Route::get('/adminPanel', [AdminPanelController::class, 'showAdminPanel'])->name('adminPanel'); 
    Route::get('/saticiPanel', [AdminPanelController::class, 'showSaticiPanel'])->name('saticiPanel');
    Route::get('/musteriPanel', [AdminPanelController::class, 'showMusteriPanel'])->name('musteriPanel'); 



    
    Route::get('/urunPanel', [ProductController::class, 'create'])->name('product.create.form');
    Route::get('/depoPanel', [StoreController::class, 'create'])->name('store.create.form');
    Route::get('/stokPanel', [StockController::class, 'create'])->name('stock.create.form');
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::delete('/members/{id}', [MemberController::class, 'delete'])->name('members.delete');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});


Route::get('/sepet', [BasketController::class, 'index'])->name('sepet.index');
Route::get('/urun', function () {  $products = Product::all(); 
    return view('urun', compact('products'));});

Route::post('/products', [ProductController::class, 'store'])->name('products.store'); 
Route::post('/store', [StoreController::class, 'store'])->name('store.store');
Route::post('/stock', [StockController::class, 'store'])->name('stock.store');

Route::post('/cart/add/{product}', [BasketController::class, 'add'])->name('cart.add');
Route::get('/cart', [BasketController::class, 'index'])->name('cart.index');
Route::delete('/cart/{id}', [BasketController::class, 'delete'])->name('cart.delete');
Route::post('/sepet/onay', [BasketController::class, 'approvl'])->name('sepet.approvl');
Route::get('/sepet/onay', [BasketController::class, 'approvl'])->name('sepet.approvl');