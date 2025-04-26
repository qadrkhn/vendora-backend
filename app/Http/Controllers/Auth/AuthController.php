<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

use Symfony\Component\HttpFoundation\Cookie;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Enums\AuthProvider;


class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

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

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
                'auth_provider' => AuthProvider::EMAIL,
                'role' => 'USER'
            ]);

            $this->otpService->generateAndSend($user);

            DB::commit();

            return response()->json(['message' => 'User registered successfully.'], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed. Please try again later.'
            ], 500);
        }
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

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Account does not exist. Please create an account before logging in.'], 403);
        } elseif(!$user->email_verified) {
            return response()->json(['message' => 'Email is not verified.'], 403);
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

        $accessTokenCookie = cookie(
            'access_token', $data['access_token'], 60, null, null, true, true, false, 'Strict'
        );

        $refreshTokenCookie = cookie(
            'refresh_token', $data['refresh_token'], 43200, null, null, true, true, false, 'Strict'
        );

        $hasToken = cookie(
            'has_token', '1', 60 * 24, '/', null, false, false, false, 'Strict'
        );

        return response()->json([
            'user' => Auth::user(),
        ])->withCookie($accessTokenCookie)->withCookie($refreshTokenCookie)->withCookie($hasToken);
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

    // verify otp
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->email_verified) {
            return response()->json(['message' => 'Email already verified.'], 403);
        }

        if (!$this->otpService->verify($user, $request->otp)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $user->update([
            'email_verified' => true,
            'email_otp' => null,
            'email_otp_expires_at' => null,
            'email_verified_at' => now()
        ]);

        return response()->json(['message' => 'Email verified successfully.']);
    }

    // resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->email_verified) {
            return response()->json(['message' => 'Email already verified.'], 403);
        }

        if (!$this->otpService->canResend($user)) {
            return response()->json(['message' => 'OTP recently sent. Please wait before resending.'], 429);
        }

        $this->otpService->generateAndSend($user);

        return response()->json(['message' => 'OTP has been resent to your email.']);
    }

    // Logout
    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();

        app(RefreshTokenRepository::class)->revokeRefreshTokensByAccessTokenId($accessToken->id);
        app(TokenRepository::class)->revokeAccessToken($accessToken->id);

        // Clear cookies
        $clearAccessToken = cookie('access_token', '', -1);
        $clearRefreshToken = cookie('refresh_token', '', -1);
        $clearHasToken = cookie('has_token', '', -1);

        return response()->json(['message' => 'Successfully logged out.'])
            ->withCookie($clearAccessToken)
            ->withCookie($clearRefreshToken)
            ->withCookie($clearHasToken);
    }
}
