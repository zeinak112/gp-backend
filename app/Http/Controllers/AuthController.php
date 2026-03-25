<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // 1. Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:mongodb.users,email'],
            'password' => ['required', 'min:8', 'max:50', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Account created successfully', 'user' => $user], 201);
    }

    // 2. Login
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['message' => 'Logged in successfully!', 'user' => $user], 200);
    }

    // 3. Social Login
    public function socialLogin(Request $request)
    {
        $request->validate(['token' => 'required', 'provider' => 'required|in:google,facebook,instagram']);
        try {
            $socialUser = Socialite::driver($request->provider)->userFromToken($request->token);
            $user = User::where('email', $socialUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                ]);
            }
            return response()->json(['user' => $user, 'message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
    }

    // 4. Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'Email not found!'], 404);

        return response()->json(['status' => 'success', 'message' => 'OTP sent', 'otp_code' => '1234'], 200);
    }

    // 5. Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required|confirmed|min:8']);
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found!'], 404);

        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['status' => 'success', 'message' => 'Password reset successfully.'], 200);
    }

    // 6. Update Gender
    // 6. Update Gender
    public function updateGender(Request $request)
    {
        $request->validate(['gender' => 'required|in:male,female,Male,Female']);

        // استخدمي Request عشان تجيبي اليوزر، دي أضمن وبتقلل الـ Errors
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized - Please Login First'], 401);
        }

        $user->update([
            'gender' => strtolower($request->gender)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gender updated successfully',
            'data' => ['gender' => $user->gender]
        ], 200);
    }
}
