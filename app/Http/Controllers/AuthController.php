<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // 1. Register
    public function register(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);

      
        $userExists = User::on('mongodb')->where('email', $request->email)->first();
        if ($userExists) {
            return response()->json([
                'message' => 'The email has already been taken.',
                'errors' => [
                    'email' => ['The email has already been taken.']
                ]
            ], 422);
        }

        
        $user = User::create([
              'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // 2. Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email', 
            'password' => 'required'
        ]);

        // البحث في المونجو
        $user = User::on('mongodb')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully!',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // 3. Social Login
    public function socialLogin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'provider' => 'required|in:google,facebook,instagram'
        ]);

        try {
            $socialUser = Socialite::driver($request->provider)->stateless()->user();

            if (!$socialUser) {
                return response()->json(['error' => 'Invalid credentials from provider'], 401);
            }

            $user = User::on('mongodb')->where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::on('mongodb')->create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    $request->provider . '_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    // 4. Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::on('mongodb')->where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'Email not found!'], 404);

        return response()->json(['status' => 'success', 'message' => 'OTP sent', 'otp_code' => '1234'], 200);
    }

    // 5. Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required|confirmed|min:8']);
        $user = User::on('mongodb')->where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found!'], 404);

        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['status' => 'success', 'message' => 'Password reset successfully.'], 200);
    }
}