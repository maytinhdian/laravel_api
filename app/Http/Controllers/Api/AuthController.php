<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

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
            $token = $user->createToken('auth_token')->plainTextToken;
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

    public function getToken(Request $request)
    {
        $user = User::find(1);
        // foreach ($user->tokens as $token) {
        //     echo '</br>';
        //     echo $token->token;
        // }
        // $user->tokens()->where('id',10)->delete();
        // dd($user->currentAccessToken());
        return $request->user()->currentAccessToken()->delete();
    }
    public function refreshToken(Request $request)
    {

        if ($request->header('authorization')) {
            $hashToken = $request->header('authorization');
            $hashToken = trim(str_replace('Bearer', '', $hashToken));

            $token = PersonalAccessToken::findToken($hashToken);

            if ($token) {

                $tokenCreated = $token->created_at;
                $expire = Carbon::parse($tokenCreated)->addMinutes(config('sanctum.expiration'));

                if (Carbon::now() >= $expire) {
                    $userId = $token->tokenable_id;

                    $user = User::find($userId);
                    $user->tokens()->delete();

                    $newToken = $user->createToken('auth_token')->plainTextToken;

                    $reponse = [
                        'status' => 200,
                        'token' => $newToken,
                    ];
                } else {
                    $reponse = [
                        'status' => 200,
                        'title' => 'Unexpired',
                    ];
                }
            } else {
                $reponse = [
                    'status' => 401,
                    'title' => 'Unauthorized',
                ];
            }
            return $reponse;
        }
    }
}
