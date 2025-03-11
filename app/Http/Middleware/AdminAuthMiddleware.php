<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if( ! Auth::check()){
                return route('login');
            }
            $userRore =session('user_authority');

            if(! $userRore){
                return route('login');
            }
            if($request->is('adminPanel')|| $request->is('adminPanel/*')){
                if($userRore !== 1){
                    return route('login');
                }
            }
            elseif ($request->is('saticiPanel') || $request->is('saticiPanel/*')) {
                if($userRore !== 2){
                    return route('login');
                }
            }
            elseif ($request->is('musteriPanel') || $request->is('musteriPanel/*')){
                if($userRore !== 3){
                    return route('anasayfa');
                }
            }
        }

        return null;
    }
}