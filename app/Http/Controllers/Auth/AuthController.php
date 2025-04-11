<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully.'], 201);
    }

    // Login with password grant (returns access & refresh token)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Please enter correct email and password.'], 400);
        }

        $http = \Illuminate\Http\Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '*',
        ]);

        $response = app()->handle($http);
        $data = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 200) {
            return response()->json(['message' => $data['message'] ?? 'Invalid credentials'], $response->getStatusCode());
        }
        $responseData['tokens'] = $data;
        $responseData['user'] = Auth::user();

        return response()->json(
            $responseData
        );
    }

    // Refresh token
    public function refreshToken(Request $request)
    {
        if ($request->input('refresh_token') !== null) {

            $originalRequest = app('request');

            // Obtain Refresh Token
            $http = \Illuminate\Http\Request::create('/oauth/token', 'POST', [
                'grant_type' => 'refresh_token',
                'client_id' => env('PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSWORD_CLIENT_SECRET'),
                'refresh_token' => $request->input('refresh_token'),
                'scope' => '*',
            ]);

            $response = app()->handle($http);
            $data = json_decode($response->getContent(), true);

            if ($response->getStatusCode() !== 200) {
                return response()->json(['message' => $data['message'] ?? 'Invalid credentials'], $response->getStatusCode());
            }

            return response()->json($data);
        }

        return response()->json([
            'message' => 'Please verify your inputs and try again.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();

        app(RefreshTokenRepository::class)->revokeRefreshTokensByAccessTokenId($accessToken->id);
        app(TokenRepository::class)->revokeAccessToken($accessToken->id);

        return response()->json(['message' => 'Successfully logged out.']);
    }
}
