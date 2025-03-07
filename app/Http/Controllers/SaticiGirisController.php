<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class SaticiGirisController extends Controller
{
    public function showLoginForm()
    {
        return view('satici_giris');
    }

    public function showRegisterForm()
    {
        return view('satici_uye_ol');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:members,email',
            'password' => 'required',
        ]);

        Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'authority_id' => 2,
            'customer_id' => null,
        ]);

        return redirect()->route('satici.uye_ol')->with('success', 'Satıcı eklendi :)');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();

            if ($user->authority_id == 2) {
                return redirect()->route('saticiPanel');
            }

            Auth::logout();
        }

        return redirect()->back()->with('error', 'Giriş bilgileri hatalı.')->withInput();
    }
    public function saticiPanel()
    {
        return view('satici_panel');
    }
}