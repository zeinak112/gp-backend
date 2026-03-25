<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // تحديث النوع
    public function updateGender(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:male,female,Male,Female'
        ]);

        $user = $request->user();
        
        $user->update([
            'gender' => strtolower($request->gender)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gender updated successfully',
            'data' => ['gender' => $user->gender]
        ], 200);
    }

    // تحديث الطول والوزن (عشان تخلصي البروفايل كله)
    public function updatePhysicalInfo(Request $request)
    {
        $request->validate([
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
        ]);

        $user = $request->user();
        $user->update([
            'height' => $request->height,
            'weight' => $request->weight,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Physical info updated successfully',
            'data' => [
                'height' => $user->height,
                'weight' => $user->weight
            ]
        ], 200);
    }
}