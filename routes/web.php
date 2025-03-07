<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MusteriGirisController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SaticiGirisController;
use App\Models\Product;



Route::get('/', function () {
    $products = Product::orderBy('id', 'desc')->take(10)->get(); 
    return view('anasayfa', compact('products'));
});


Route::get('/musteri-giris', [MusteriGirisController::class, 'showLoginForm'])->name('musteri_giris');
Route::post('/musteri-giris', [MusteriGirisController::class, 'login'])->name('musteri_giris.post');
Route::get('/musteri/uye-ol', [MusteriGirisController::class, 'showRegisterForm'])->name('musteri.uye_ol');
Route::post('/musteri/uye-ol', [MusteriGirisController::class, 'Register'])->name('musteri.uye_ol.kayit');

Route::get('/satici-giris', [SaticiGirisController::class, 'showLoginForm'])->name('satici_giris');
Route::post('/satici-giris', [SaticiGirisController::class, 'login'])->name('satici_giris.post');
Route::get('/satici/uye-ol', [SaticiGirisController::class, 'showRegisterForm'])->name('satici.uye_ol');
Route::post('/satici/uye-ol', [SaticiGirisController::class, 'Register'])->name('satici.uye_ol.kayit');

Route::get('/admin-giris', [AdminPanelController::class, 'showLoginForm'])->name('admin_giris');
Route::post('/admin-giris', [AdminPanelController::class, 'login'])->name('admin_giris.post');

Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout'); 


Route::get('/urunPanel', [ProductController::class, 'create'])->name('product.create.form');
Route::get('/depoPanel', [StoreController::class, 'create'])->name('store.create.form');
Route::get('/stokPanel', [StockController::class, 'create'])->name('stock.create.form');
Route::get('/adminPanel', function () {
    return view('admin_panel');});
Route::get('/saticiPanel', function () { return view('satici_panel'); });


Route::get('/sepet', [CartController::class, 'index'])->name('sepet.index');
Route::get('/urun', function () {  $products = Product::all(); 
    return view('urun', compact('products'));});

Route::post('/products', [ProductController::class, 'store'])->name('products.store'); 
Route::post('/store', [StoreController::class, 'store'])->name('store.store');
Route::post('/stock', [StockController::class, 'store'])->name('stock.store');

Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::delete('/cart/{id}', [CartController::class, 'delete'])->name('cart.delete');
Route::post('/sepet/onay', [CartController::class, 'approvl'])->name('sepet.approvl');
Route::get('/sepet/onay', [CartController::class, 'approvl'])->name('sepet.approvl');