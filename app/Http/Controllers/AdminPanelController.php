<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; // 🔹 Member modelini ekledik

class AdminPanelController extends Controller
{
    public function showLoginForm()
    {
        return view('admin_panel_giris');
    }

    public function login(Request $request)
    {
        // Kullanıcıdan gelen giriş bilgilerini doğrula
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Member modelini kullanarak giriş yap
        if (Auth::guard('web')->attempt($credentials)) {
            // Giriş başarılıysa admin paneline yönlendir
            return redirect()->route('admin.panel');
        }

        // Eğer giriş başarısızsa, hata mesajı ve tekrar giriş formu
        return back()->withErrors(['email' => 'Giriş bilgileri hatalı.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }


}