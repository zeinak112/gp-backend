<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{ 

    // 1. وظيفة التسجيل التقليدي (Register)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:mongodb.users,email',
            ],
            'password' => [
                'required',
                'min:8',
                'max:50',
                'regex:/[a-z]/',      // حرف صغير
                'regex:/[A-Z]/',      // حرف كبير
                'regex:/[0-9]/',      // رقم
                'regex:/[@$!%*#?&]/', // رمز خاص
            ],
        ], [
            'email.email' => 'Please enter a valid email address.',
            'password.regex' => 'Password must contain uppercase, lowercase letters, numbers, and special symbols.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Account created successfully', 'user' => $user], 201);
    }

    // 2. وظيفة تسجيل الدخول (Login)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.email' => 'The email format is incorrect.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Email not found. Redirecting to registration...',
                'action' => 'go_to_register'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password!'], 401);
        }

        return response()->json(['message' => 'Logged in successfully!', 'user' => $user], 200);
    }

    // 3. السوشيال لوجن
    public function socialLogin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'provider' => 'required|in:google,facebook,instagram',
        ]);

        try {
            $socialUser = Socialite::driver($request->provider)->userFromToken($request->token);
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    $request->provider . '_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            return response()->json(['user' => $user, 'message' => 'Success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
    }

   // 4. نسيان الباسورد (التأكد من الحساب وإرجاع كود وهمي)
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found!'
            ], 404);
        }

        // بنرجع كود 1234 عشان بنت الفلاتر تعمل بيه Check عندها
        return response()->json([
            'status' => 'success',
            'message' => 'Email verified. Use the verification code to reset your password.',
            'otp_code' => '1234' 
        ], 200);
    }

    // 5. تعيين الباسورد الجديد (بعد التأكد من الكود في الموبايل)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => [
                'required', 'confirmed', 'min:8', 'max:50',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.regex' => 'Password must be strong (Uppercase, Lowercase, Numbers, and Symbols).',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        // تحديث الباسورد في المونجو
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully. You can now login with your new password.'
        ], 200);
    }

} 