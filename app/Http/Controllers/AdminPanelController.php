<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; 
use Illuminate\Support\Facades\Hash; 

class AdminPanelController extends Controller
{

    public function showRegistrationForm()
    {
        return view('uye_ol'); 
    }
    
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

        return back()->withErrors(['email' => 'Giriş bilgileri hatalı.'])->withInput(); //çalışmıyor bu
        
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'=> 'required|string|max:255',
            'email'=> 'required|string|unique:members,email',
            'password'=> 'required'
        ]);


        Member::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        return redirect()->route('uye_ol')->with('success','üye eklendi :)');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

}
