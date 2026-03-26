<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function updateGender(Request $request)
{
    // بنقبل male أو female زي ما ظاهر في الصور بتاعتك
    $request->validate(['gender' => 'required|in:male,female,Male,Female']);
    
    $user = $request->user(); 

    // بنحدث جدول البروفايل لليوزر ده بالظبط
    $updated = DB::collection('profiles')->where('user_id', $user->_id)->update([
        'gender' => strtolower($request->gender),
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Gender saved successfully!',
        'next_step' => 'Redirect to Physical Info screen'
    ]);
}

}