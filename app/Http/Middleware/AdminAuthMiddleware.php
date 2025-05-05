<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware extends Middleware
{
    const ADMIN_ROLE_ID =1;
    const SATICI_ROLE_ID = 2;
    const MUSTERI_ROLE_ID=3;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if( ! Auth::check()){
                return route('login');
            }
            $userRole =(int) session('user_authority');

            if(! $userRole){
                return route('login');
            }
            if($request->is('adminPanel')|| $request->is('adminPanel/*')){
                if($userRole !== self::ADMIN_ROLE_ID){
                    return route('login');
                }
            }
            elseif ($request->is('saticiPanel') || $request->is('saticiPanel/*')) {
                if($userRole !== self::SATICI_ROLE_ID){
                    return route('login');
                }
            }
            elseif ($request->is('musteriPanel') || $request->is('musteriPanel/*')){
                if($userRole !== self::MUSTERI_ROLE_ID){
                    return route('home.product');
                }
            }
        }

        return null;
    }
}