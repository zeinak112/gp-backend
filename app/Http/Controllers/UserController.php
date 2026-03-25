<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function updateGender(Request $request)
    {
        $request->validate(['gender' => 'required|in:male,female,Male,Female']);

        // هنجيب اليوزر بالتوكن
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not found (Check Token)'], 401);
        }

        $user->update(['gender' => strtolower($request->gender)]);

        return response()->json([
            'status' => true,
            'message' => 'Gender updated successfully!',
            'data' => ['gender' => $user->gender]
        ]);
    }

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
            'message' => 'Physical info updated successfully!',
            'data' => ['height' => $user->height, 'weight' => $user->weight]
        ]);
    }
}