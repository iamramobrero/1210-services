<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function showLoginPage(){
        return view('adminlte::auth.login');
    }

    public function doLogin(Request $request){
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $remember = $request->get('remember');

        if(!Auth::attempt(['email' => $request->username, 'password' => $request->password], $remember)){
            if(!Auth::attempt(['username' => $request->username, 'password' => $request->password], $remember))
                return redirect('/')->withErrors(['username' => 'Invalid credentials','password' => 'Invalid credentials']);
        }

        $user = Auth::user();
        $apiToken = $user->createToken('MyApp')->accessToken;

        return redirect()->route('showDashboard')->withCookie(cookie('apiToken', $apiToken, 2630000 ));

    }

    public function doLogout(){
        Auth::logout();
        Cookie::queue(Cookie::forget('apiToken'));
        return redirect()->route('login');
    }
}
