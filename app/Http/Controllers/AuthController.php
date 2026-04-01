<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str; 
use Laravel\Socialite\Facades\Socialite;

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

        $userExists = User::where('email', $request->email)->first();
        if ($userExists) {
            return response()->json(['message' => 'The email has already been taken.'], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // توليد توكن يدوي لـ Sanctum عشان يشتغل مع MongoDB
        $plainTextToken = Str::random(40);
        
        DB::connection('mongodb')->table('personal_access_tokens')->insert([
            'tokenable_id'   => (string) $user->_id, 
            'tokenable_type' => get_class($user),
            'name'           => 'auth_token',
            'token'          => hash('sha256', $plainTextToken),
            'abilities'      => ['*'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $tokenString = $user->_id . '|' . $plainTextToken;

        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
            'token' => $tokenString 
        ], 201);
    }

    // 2. Login (بنفس الطريقة)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email', 
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $plainTextToken = Str::random(40);

        DB::connection('mongodb')->table('personal_access_tokens')->insert([
            'tokenable_id'   => (string) $user->_id,
            'tokenable_type' => get_class($user),
            'name'           => 'auth_token',
            'token'          => hash('sha256', $plainTextToken),
            'abilities'      => ['*'],
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $tokenString = $user->_id . '|' . $plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully!',
            'user' => $user,
            'token' => $tokenString 
        ], 200);
    }

   public function socialLogin(Request $request)
    {
        $request->validate([
            'token' => 'required', // التوكن اللي جاي من الفلاتر
            'provider' => 'required|in:google,facebook,instagram'
        ]);

        try {
            // التعديل هنا: استخدمي userFromToken بدل user()
            $socialUser = Socialite::driver($request->provider)
                            ->stateless()
                            ->userFromToken($request->token);

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $providerIdField = $request->provider . '_id'; 

                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    $providerIdField => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            $plainTextToken = Str::random(40);
            
            DB::connection('mongodb')->table('personal_access_tokens')->insert([
                'tokenable_id'   => (string) $user->_id,
                'tokenable_type' => get_class($user),
                'name'           => 'auth_token',
                'token'          => hash('sha256', $plainTextToken),
                'abilities'      => ['*'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $user->_id . '|' . $plainTextToken
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage() // هنا هيقولك ليه فشل بالظبط
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
        $request->validate(['email' => 'required|email', 'password' => 'required|min:8']);
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found!'], 404);

        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['status' => 'success', 'message' => 'Password reset successfully.'], 200);
    }
}