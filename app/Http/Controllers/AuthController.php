<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $ethAddress = Str::lower($request->ethAddress);
        $message   = $request->message;
        $signature = $request->signature;

        $valid = \App\Helpers\EcRecover::personalVerifyEcRecover($message,  $signature,  $ethAddress);
        if (!$valid) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $user = User::where('eth_address', $ethAddress)->first();

        if (!$user) {
            $user = new User;
            $user->eth_address = $ethAddress;
            $user->save();
        }

        Auth::login($user);

        return response()->json(['message' => 'Login successful'], 200);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
