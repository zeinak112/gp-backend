<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // 1. تحديث النوع (Gender)
    public function updateGender(Request $request)
    {
        // بنقبل القيم اللي الموبايل هيبعتها (male او female)
        $request->validate([
            'gender' => 'required|in:male,female,Male,Female'
        ]);
        
        $user = $request->user(); // بجيب اليوزر من التوكن

        // بنروح نحدث السطر اللي كريتناه في الـ register
        DB::collection('profiles')->where('user_id', $user->_id)->update([
            'gender' => strtolower($request->gender),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gender updated successfully!',
            'data' => ['gender' => $request->gender]
        ]);
    }

    // 2. تحديث الطول والوزن (Physical Info)
    public function updatePhysicalInfo(Request $request)
    {
        $request->validate([
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
        ]);

        $user = $request->user();

        // تحديث الطول والوزن في نفس جدول الـ profiles
        DB::collection('profiles')->where('user_id', $user->_id)->update([
            'height' => $request->height,
            'weight' => $request->weight,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Physical info updated successfully!',
            'data' => [
                'height' => $request->height, 
                'weight' => $request->weight
            ]
        ]);
    }
}