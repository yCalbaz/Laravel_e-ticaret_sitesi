<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CartController;
use App\Models\Product;
use App\Models\Store;


Route::get('/', function () {
    $products = Product::orderBy('id', 'desc')->take(10)->get(); 
    return view('anasayfa', compact('products'));
});


Route::get('/urunPanel', [ProductController::class, 'create'])->name('products.create'); 
Route::get('/depoPanel', [StoreController::class, 'create'])->name('stores.create');
Route::get('/adminPanel', function () { return view('admin_panel'); })->middleware('auth')->name('admin.panel');
Route::get('/sepet', function () { return view('sepet'); });
Route::get('/urun', function () {  $products = Product::orderBy('id', 'desc')->take(10)->get(); 
    return view('urun', compact('products'));});


Route::get('/login', [AdminPanelController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/login', [AdminPanelController::class, 'login'])->name('admin.login');
Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout');

Route::post('/products', [ProductController::class, 'store'])->name('products.store'); 
Route::post('/store', [StoreController::class, 'store'])->name('store.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
