<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request) {
        $tokenRequest = Request::create('/api/tokens/create', 'POST', $request->only('email', 'password', 'device_name'));
        $tokenResponse = Route::dispatch($tokenRequest);
        $credentials = $request->only('email', 'password');

        if ($tokenResponse->exception === null && Auth::attempt($credentials)) {
            return redirect()->intended('home');
        } else {
            // TODO Mensaje de error
            return redirect()->route('login');
        }
    }
}