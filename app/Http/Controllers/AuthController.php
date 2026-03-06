<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. وظيفة التسجيل (Register)
    public function register(Request $request)
    {
        // التأكد من البيانات اللي جاية من الفلاتر
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:mongodb.users,email',
            'password' => 'required|min:6'
        ]);

        // تخزين المستخدم في المونجو
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // تشفير الباسورد
        ]);

        // الرد على الفلاتر برسالة نجاح
        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح يا بطل!',
            'user' => $user
        ], 201);
    }

    // 2. وظيفة تسجيل الدخول (Login) اللي دمجناها دلوقتي
    public function login(Request $request)
    {
        // التأكد من البيانات
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // البحث عن المستخدم بالإيميل
        $user = User::where('email', $request->email)->first();

        // نتشيك لو اليوزر مش موجود أو الباسورد غلط
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'الإيميل أو الباسورد غلط يا هندسة!'
            ], 401);
        }

        // الرد بنجاح
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح!',
            'user' => $user
        ], 200);
    }
}