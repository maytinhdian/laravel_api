<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Client;
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


            // // $token = $user->createToken('auth_token')->plainTextToken;
            // $tokenResult = $user->createToken('auth_api');

            // //Thiết lập expires 
            // $token = $tokenResult->token ; 
            // $token -> expires_at = Carbon::now()->addMinutes(60);


            // //Trả về access token        
            // $accessToken = $tokenResult->accessToken ;

            //  //Trả về expires 
            // $expires = Carbon::parse($token -> expires_at)->toDayDateTimeString();

            $client = Client::where('password_client', 1)->first();

            if ($client) {

                $clientSecret =  $client->secret;
                $clientId = $client->id;

                $reponse = Http::asForm()->post('http://127.0.0.1:8001/oauth/token',[
                    'grant_type'=>'password',
                    'client_id'=>$clientId,
                    'client_secret'=> $clientSecret,
                    'username'=>$email,
                    'password'=>$password ,
                    'scope'=>'',
                ]);
                return $reponse;
            }


            // $reponse = [
            //     'status' => 200,
            //     'token' => $accessToken,
            //     'expires'=> $expires,
            // ];
            // return $reponse;
        } else {
            $reponse = [
                'status' => 400,
                'title' => 'Unauthorized'
            ];
        }
    }

    public function logout()
    {
        $user = Auth::user();
        $status = $user->token()->revoke();

        $reponse = [
            'status' => 200,
            'title' => 'Logout'
        ];
        return $reponse;
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

        // if ($request->header('authorization')) {
        //     $hashToken = $request->header('authorization');
        //     $hashToken = trim(str_replace('Bearer', '', $hashToken));

        //     $token = PersonalAccessToken::findToken($hashToken);

        //     if ($token) {

        //         $tokenCreated = $token->created_at;
        //         $expire = Carbon::parse($tokenCreated)->addMinutes(config('sanctum.expiration'));

        //         if (Carbon::now() >= $expire) {
        //             $userId = $token->tokenable_id;

        //             $user = User::find($userId);
        //             $user->tokens()->delete();

        //             $newToken = $user->createToken('auth_token')->plainTextToken;

        //             $reponse = [
        //                 'status' => 200,
        //                 'token' => $newToken,
        //             ];
        //         } else {
        //             $reponse = [
        //                 'status' => 200,
        //                 'title' => 'Unexpired',
        //             ];
        //         }
        //     } else {
        //         $reponse = [
        //             'status' => 401,
        //             'title' => 'Unauthorized',
        //         ];
        //     }
        //     return $reponse;
        // }

        $client = Client::where('password_client',1)->first();
        if ($client) {
            $clientSecret = $client->secret;
            $clientId = $client->id;
            $refreshToken = $request->refresh;

            $reponse =Http::asForm()->post('http://127.0.0.1:8001/oauth/token',[
                'grant_type'=>'refresh_token',
                'refresh_token'=>$refreshToken,
                'client_id'=>$clientId,
                'client_secret'=>$clientSecret,
                'scope'=>'',
            ]);

            return $reponse;
        }

    }
}
