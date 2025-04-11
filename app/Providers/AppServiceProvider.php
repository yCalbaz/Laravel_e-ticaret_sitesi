<?php

namespace App\Providers;

use App\Models\Basket;
use App\Models\BasketItem;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $customerId = Session::get('customer_id');
            $sepetSayisi = 0;
    
            if ($customerId) {
                $basket = Basket::where('customer_id', $customerId)->where('is_active', 1)->first();
                if ($basket) {
                    $sepetSayisi = BasketItem::where('order_id', $basket->id)->sum('product_piece');
                }
            }
    
            $view->with('sepetSayisi', $sepetSayisi);
        });
        Paginator::useBootstrapFour();
    }
    
}
