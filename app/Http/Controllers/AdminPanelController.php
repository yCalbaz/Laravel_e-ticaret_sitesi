<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; 

class AdminPanelController extends Controller
{
    public function showLoginForm()
    {
        return view('admin_panel_giris');
    }

    public function login(Request $request)
    {
        
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        
        if (Auth::guard('web')->attempt($credentials)) {
            
            return redirect()->route('admin.panel');
        }

        return back()->withErrors(['email' => 'Giriş bilgileri hatalı.'])->withInput();
        echo "hata ver";
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }


}