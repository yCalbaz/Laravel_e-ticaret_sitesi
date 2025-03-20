<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
use App\Models\Product; 



Route::get('/', [HomeProductController::class, 'productHome']);

Route::get('/urun', [ProductController::class, 'index'])->name('urun');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); 

Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');



Route::middleware(['auth'])->group(function () {
    Route::get('/adminPanel', [ManagerController::class, 'showAdminPanel'])->name('adminPanel'); 
    Route::get('/saticiPanel', [ManagerController::class, 'showSaticiPanel'])->name('saticiPanel');
    Route::get('/musteriPanel', [ManagerController::class, 'showMusteriPanel'])->name('musteriPanel'); 
 
    Route::get('/urunPanel', [HomeProductController::class, 'index'])->name('product.index.form');
    Route::get('/depoPanel', [StoreController::class, 'index'])->name('store.index.form');
    Route::get('/stokPanel', [StockController::class, 'index'])->name('stock.index.form');
    Route::get('/uyeler', [MemberController::class, 'index'])->name('members.index');
    Route::delete('/uyeler/{id}', [MemberController::class, 'delete'])->name('members.delete');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});


Route::post('/products', [HomeProductController::class, 'store'])->name('products.store'); 
Route::post('/store', [StoreController::class, 'store'])->name('store.store');
Route::post('/stock', [StockController::class, 'store'])->name('stock.store');

Route::get('/sepet', [BasketController::class, 'index'])->name('sepet.index');
Route::post('/cart/add/{product_sku}', [BasketController::class, 'add'])->name('cart.add');
Route::get('/cart', [BasketController::class, 'index'])->name('cart.index');
Route::delete('/cart/{id}', [BasketController::class, 'delete'])->name('cart.delete');
Route::post('/sepet/onay', [BasketController::class, 'approvl'])->name('sepet.approvl');
Route::get('/sepet/onay', [BasketController::class, 'approvl'])->name('sepet.approvl');


Route::get('/musteri/uye-ol', [AuthController::class, 'showRegisterForm'])->name('musteri.uye_ol');
Route::post('/musteri/uye-ol', [AuthController::class, 'customerRegister'])->name('musteri.uye_ol.kayit');
Route::get('/order/return', [OrderDetailController::class, 'showReturnForm'])->name('order.returnForm');
Route::post('/order/return', [OrderDetailController::class, 'processReturn'])->name('order.processReturn');

Route::get('/product/{sku}', [ProductController::class, 'showDetails'])->name('product.details');


Route::middleware(['auth'])->group(function () {
    Route::get('/siparisler', [OrderDetailController::class, 'index'])->name('orders.index');
    Route::get('/siparis/{orderId}/detaylar', [OrderDetailController::class, 'showDetails'])->name('order.showDetails');
    Route::get('/siparis/{orderId}/iade', [OrderDetailController::class, 'showReturnForm'])->name('order.returnForm');
    Route::post('/siparis/iade', [OrderDetailController::class, 'processReturn'])->name('order.processReturn');
});

Route::put('/sepet/guncelle/{id}', [BasketController::class, 'update'])->name('cart.update');
Route::get('/search', [App\Http\Controllers\ProductController::class, 'search'])->name('search');

Route::get('/kategori/{category_slug}', [ProductController::class, 'productCategory'])->name('kategori.urunler');

