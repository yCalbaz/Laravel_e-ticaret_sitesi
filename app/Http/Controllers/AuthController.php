<?php
namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    const ADMIN_ROLE_ID = 1;
    const SATICI_ROLE_ID = 2;
    const MUSTERI_ROLE_ID = 3;

    protected function databaseCustomerId()
    {
        $lastCustomer = cache()->remember('last_customer_id', 60, function () {
            return Member::whereNotNull('customer_id')->orderBy('customer_id', 'desc')->first();
        });
        return $lastCustomer ? $lastCustomer->customer_id + 1 : 1;
    }

    protected function redirectUser($user)
    {
        $authority = Session::get('user_authority');
        switch ($authority) {
            case  self::ADMIN_ROLE_ID:
                return redirect()->route('adminPanel');
            case self::SATICI_ROLE_ID:
                return redirect()->route('saticiPanel');
            case self::MUSTERI_ROLE_ID:
                return redirect()->route('musteriPanel');
            default:
                return route('/');
        }
    }

    public function showLoginForm()
    {
        if(Auth::check()) {
            $user = Auth::user();
            Session::put('user_authority', $user->authority_id);
            if ($user->authority_id== self::MUSTERI_ROLE_ID){
                Session::put('customer_id', $user->customer_id);
            }
            return $this->redirectUser($user);
        }
        return view('admin_panel_login'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::user();
            Session::put('user_authority', $user->authority_id);
            if ($user->authority_id== self::MUSTERI_ROLE_ID){
                Session::put('customer_id', $user->customer_id);
            }
            return $this->redirectUser($user);
        }

        return redirect()->back()->with('error', 'Giriş bilgileri hatalı.')->withInput();
    }

    public function showRegisterForm()
    {
        return view('customer_sing_up');
    }

    public function customerRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:members,email',
            'password' => 'required',
        ]);

        $customer_id = $this->databaseCustomerId();

        Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'authority_id' => self::MUSTERI_ROLE_ID,
            'customer_id' => $customer_id,
        ]);

        return redirect()->route('musteri.uye_ol')->with('success', 'Müşteri üye eklendi :)');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
