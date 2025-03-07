<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

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
            $user = Auth::user();

            if ($user->authority_id == 1) {
                return redirect()->route('adminPanel'); 
            }

            Auth::logout(); 
        }

        return redirect()->back()->with('error', 'Giriş bilgileri hatalı.')->withInput();
    }

    

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function adminPanel()
    {
        return view('admin_panel');
    }

    
}