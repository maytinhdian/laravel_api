<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $checkLogin = Auth::attempt([
            'email' => $email,
            'password' => $password
        ]);

        if ($checkLogin) {
            $user = Auth::user();
            $token = $user -> createToken('auth_token')->plainTextToken;
            $reponse = [
                'status' => 200,
                'token' => $token,
            ];
            return $reponse;
        } else {
            $reponse = [
                'status' => 400,
                'title' => 'Unauthorized'
            ];
        }
    }
}
