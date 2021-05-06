<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('home')->with('message', 'Inicio de sesión correcto');
        } else {
            // TODO Mensaje de error
            return redirect()->route('login')->with('error', 'Usuario y/o password incorrectos');
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('message', 'Sesión finalizada');
    }
}
