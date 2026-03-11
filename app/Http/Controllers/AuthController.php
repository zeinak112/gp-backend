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
                'regex:/[a-z]/',      
                'regex:/[A-Z]/',      
                'regex:/[0-9]/',      
            ],
        ], [
            'email.unique' => 'This email is already registered.',
            'email.email' => 'Please enter a valid email address.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain uppercase, lowercase letters and numbers.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user
        ], 201);
    }

    // 2. وظيفة تسجيل الدخول التقليدي (Login)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password!'
            ], 401);
        }

        return response()->json([
            'message' => 'Logged in successfully!',
            'user' => $user
        ], 200);
    }

    
    public function socialLogin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'provider' => 'required|in:google,facebook,instagram',
        ]);

        try {
            // التحقق من التوكن وجلب بيانات المستخدم من المزود (جوجل مثلاً)
            $socialUser = Socialite::driver($request->provider)->userFromToken($request->token);

            // البحث عن المستخدم بالإيميل أو الـ Provider ID
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // لو أول مرة يدخل، نكريت له حساب
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // باسورد عشوائي للأمان
                    $request->provider . '_id' => $socialUser->getId(), // تخزين الـ ID (google_id مثلاً)
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                // لو موجود قبل كدة، بنحدث الـ ID الخاص بالمزود لو مكنش موجود
                $providerIdField = $request->provider . '_id';
                if (!$user->$providerIdField) {
                    $user->update([$providerIdField => $socialUser->getId()]);
                }
            }

            return response()->json([
                'message' => 'Social login successful!',
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid credentials from ' . $request->provider,
                'error' => $e->getMessage()
            ], 401);
        }
    }
}