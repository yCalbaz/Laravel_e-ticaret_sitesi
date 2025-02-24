<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Store;


Route::get('/', function () {
    $products = Product::orderBy('id', 'desc')->take(10)->get(); 
    return view('anasayfa', compact('products'));
});


Route::get('/urunPanel', [ProductController::class, 'create'])->name('products.create'); // Ürün ekleme formu
Route::get('/depoPanel', [StoreController::class, 'create'])->name('stores.create');
Route::get('/adminPanel', function () { return view('admin_panel'); })->middleware('auth')->name('admin.panel');
Route::get('/sepet', function () { return view('sepet'); });


Route::get('/login', [AdminPanelController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/login', [AdminPanelController::class, 'login'])->name('admin.login');
Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout');


Route::post('/products/store', [ProductController::class, 'store'])->name('products.store'); // Ürün ekleme
Route::post('/store/store', [StoreController::class, 'store'])->name('store.store');