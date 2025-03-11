<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Product;

class AdminPanelController extends Controller
{
    const ADMIN_ROLE_ID =1;
    const SATICI_ROLE_ID = 2;
    const MUSTERI_ROLE_ID=3;

    public function showLoginForm()
    {
        if(Auth::check()){
            $user = Auth::user();
            Session::put('user_authority', $user->authority_id);
            return $this->redirectUser($user);
        }
        return view('admin_panel_giris'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();
            Session::put('user_authority',$user->authority_id);
            return $this->redirectUser($user);
        }

        return redirect()->back()->with('error', 'Giriş bilgileri hatalı.')->withInput();
    }

    protected function redirectUser($user)
    {
        $authority = Session::get('user_authority');
        switch($authority){
            case self::ADMIN_ROLE_ID:
                return redirect()->route('adminPanel');
            case self::SATICI_ROLE_ID:
                return redirect()->route('saticiPanel');
            case self::MUSTERI_ROLE_ID:
                return redirect()->route('musteriPanel');

            default:
            return route('/');
        }
    }

    

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function showAdminPanel()
    {
        if(session('user_authority') !== self::ADMIN_ROLE_ID){
            return redirect()->route('login');
        }
        return view('admin_panel');
    }
    public function showSaticiPanel()
    {
        if(session('user_authority') !== self::SATICI_ROLE_ID){
            return redirect()->route('login');
        }
        return view('satici_panel');
    }
    public function showMusteriPanel()
    {
        if(session('user_authority') !== self::MUSTERI_ROLE_ID){
            return redirect()->route('login');
        }
        $products = Product::orderBy('id', 'desc')->take(10)->get(); 
        return view('anasayfa', compact('products'));
    }

    
}