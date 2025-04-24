<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

use App\Enums\AuthProvider;
use App\Http\Controllers\Controller;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function handleGoogle(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Fetch and cache Google certs
            $googleCerts = Cache::remember('google_oauth_certs', 60 * 24, function () {
                return Http::get('https://www.googleapis.com/oauth2/v3/certs')->json();
            });

            $decoded = JWT::decode($request->token, JWK::parseKeySet($googleCerts));

            // Validate token claims
            if ($decoded->aud !== env('GOOGLE_CLIENT_ID')) {
                return response()->json(['message' => 'Invalid audience.'], 401);
            }

            if ($decoded->iss !== 'https://accounts.google.com') {
                return response()->json(['message' => 'Invalid issuer.'], 401);
            }

            if ($decoded->exp < time()) {
                return response()->json(['message' => 'Token has expired.'], 401);
            }

            // Create or fetch user
            $user = User::firstOrCreate(
                ['email' => $decoded->email],
                [
                    'name' => $decoded->name ?? $decoded->email,
                    'auth_provider' => AuthProvider::GOOGLE,
                    'email_verified' => true,
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(40)),
                    'set_password_at' => null,
                    'picture' => $decoded->picture,
                    'role' => 'USER'
                ]
            );

            if ($user->auth_provider !== AuthProvider::GOOGLE) {
                return response()->json(['message' => 'Account already exists. Please login using email and password.'], 403);
            }

            // revoke all previous tokens that haven't been revoked
            $user->tokens()
                ->where('revoked', false)
                ->where('expires_at', '>', now())
                ->update(['revoked' => true]);

            // Generate new access token
            $accessToken = $user->createToken('Personal Access Token')->accessToken;

            DB::commit();

            return response()->json(['user' => $user])->cookie(
                'access_token',
                $accessToken,
                60 * 24,
                null,
                null,
                true,
                true,
                false,
                'Strict'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Google login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
