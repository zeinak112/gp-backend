<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{    // 1. Register
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'email', 'unique:mongodb.users,email'],
        'password' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // السطر ده هو اللي بيكريت البروفايل وبيربطه بالـ User ID
    DB::collection('profiles')->insert([
        'user_id' => $user->_id, 
        'gender' => null,
        'height' => null,
        'weight' => null,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // إصدار توكن عشان يدخل علطول يكمل بياناته
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
    $request->validate(['email' => 'required|email', 'password' => 'required']);
    $user = User::where('email', $request->email)->first();

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


public function socialLogin(Request $request)
{
    $request->validate([
        'token' => 'required', 
        'provider' => 'required|in:google,facebook,instagram'
    ]);

    try {
        // بنستخدم getUserByToken دي الأضمن وبنمرر لها التوكن اللي جاي من الموبايل
        $socialUser = Socialite::driver($request->provider)->stateless()->user();
        
        // لو لارفيل لسه معترض، السطر ده هو "الجوكر" اللي هيجيب البيانات بالتوكن:
        // $socialUser = Socialite::driver($request->provider)->actAsStateless()->userByToken($request->token);

        if (!$socialUser) {
            return response()->json(['error' => 'Invalid credentials from provider'], 401);
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(16)), 
                $request->provider . '_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            // ربط البروفايل فوراً
            DB::collection('profiles')->insert([
                'user_id' => $user->_id,
                'gender' => null,
                'created_at' => now(),
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
            'message' => $e->getMessage() // ده هيقولك السبب بالظبط لو فشل
        ], 401);
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

    
}