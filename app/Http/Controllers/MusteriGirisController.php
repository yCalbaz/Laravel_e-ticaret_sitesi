<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class MusteriGirisController extends Controller
{
    public function showLoginForm()
    {
        return view('musteri_giris');
    }

    public function showRegisterForm()
    {
        return view('musteri_uye_ol');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:members,email',
            'password' => 'required',
        ]);

        $lastCustomer = Member::whereNotNull('customer_id')->orderBy('customer_id', 'desc')->first();
        $customer_id = $lastCustomer ? $lastCustomer->customer_id + 1 : 1;

        Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'authority_id' => 3,
            'customer_id' => $customer_id,
        ]);

        return redirect()->route('musteri.uye_ol')->with('success', 'Müşteri üye eklendi :)');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();

            if ($user->authority_id == 3) {
                return redirect('/');
            }

            Auth::logout();
        }

        return redirect()->back()->with('error', 'Giriş bilgileri hatalı.')->withInput();
    }
}