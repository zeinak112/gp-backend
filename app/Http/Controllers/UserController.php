<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
   public function updateGender(Request $request)
{
    $request->validate([
        'gender' => 'required|in:male,female,Male,Female'
    ]);
    
    $user = $request->user();

    // استبدلي collection بـ table هنا
    DB::connection('mongodb')->table('profiles')->updateOrInsert(
        ['user_id' => (string) $user->_id], 
        [
            'gender' => strtolower($request->gender),
            'updated_at' => now()
        ]
    );

    return response()->json([
        'status' => true,
        'message' => 'Gender updated successfully!',
        'data' => ['gender' => strtolower($request->gender)]
    ]);
}
    
  public function updatePhysicalInfo(Request $request)
{
    $request->validate([
        'height' => 'required|numeric',
        'weight' => 'required|numeric',
    ]);

    $user = $request->user();

    // استبدلي collection بـ table هنا
    DB::connection('mongodb')->table('profiles')->updateOrInsert(
        ['user_id' => (string) $user->_id],
        [
            'height' => $request->height,
            'weight' => $request->weight,
            'updated_at' => now()
        ]
    );

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