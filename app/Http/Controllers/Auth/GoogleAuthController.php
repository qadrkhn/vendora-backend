<?php

namespace App\Http\Auth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Controller;
use App\Enums\AuthProvider;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function handleGoogle(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string'
            ]);

            DB::beginTransaction();

            try {
                $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Invalid Google token.'], 401);
            }

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'auth_provider' => AuthProvider::GOOGLE,
                    'email_verified_at' => now(),
                    'email_verified' => true
                ]
            );

            if ($user->auth_provider !== AuthProvider::GOOGLE) {
                return response()->json(['message' => 'Please log in using email/password.'], 403);
            }


            $tokenResult = $user->createToken('Access Token');
            $accessToken = $tokenResult->accessToken;

            $responseData = [
                'tokens' => [
                    'access_token' => $accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => $tokenResult->token->expires_at,
                ],
                'user' => $user
            ];

            return response()->json($responseData);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }

    }
}
