<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('anasayfa');
});

Route::get('/urunPanel', function () {
    return view('urun_panel');
});
Route::get('/depoPanel', function () {
    return view('depo_panel');
});

Route::get('/adminPanel', function () {
    return view('admin_panel');
});
Route::get('/sepet', function () {
    return view('sepet');
});

Route::get('/login', [AdminPanelController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/login', [AdminPanelController::class, 'login'])->name('admin.login');
Route::post('/logout', [AdminPanelController::class, 'logout'])->name('admin.logout');

// 🔹 Admin Panel Sayfası (Giriş yapmadan erişilmesin)
Route::middleware('auth')->get('/adminPanel', function () {
    return view('admin_panel');
})->name('admin.panel');


Route::get('/logout', function () {
    Auth::logout();
    return redirect('/'); // Ana sayfaya yönlendir
})->name('admin.logout');









