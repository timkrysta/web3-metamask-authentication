<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function login()
    {
        if (Auth::user() && Auth::check()) {
            return redirect()->route('home');
        }
        return view('login', [
            'nonce' => Str::random(24)
        ]);
    }

    public function home()
    {
        if (Auth::user() && Auth::check()) {
            return view('home', [
                'eth_address' => Auth::user()->eth_address,
            ]);
        }
        return redirect()->route('login');
    }
}
