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


public function updateAge(Request $request)
{
    $request->validate([
        'age' => 'required|integer|min:10|max:100'
    ]);

    $user = $request->user();

    DB::connection('mongodb')->table('profiles')->updateOrInsert(
        ['user_id' => (string) $user->_id],
        [
            'age' => (int) $request->age,
            'updated_at' => now()
        ]
    );

    
    return response()->json(['status' => true, 'message' => 'Age updated successfully!']);
}

public function updateGoal(Request $request)
{
    $request->validate([
       
        'goal' => 'required|string|in:Lose Weight,Build Muscle,Physical Therapy,lose weight,build muscle,physical therapy'
    ]);

    $user = $request->user();

   
    $cleanGoal = strtolower($request->goal);

    DB::connection('mongodb')->table('profiles')->updateOrInsert(
        ['user_id' => (string) $user->_id],
        [
            'goal' => $cleanGoal,
            'updated_at' => now()
        ]
    );

    return response()->json([
        'status' => true, 
        'message' => 'Goal updated successfully!',
        'stored_as' => $cleanGoal 
    ]);
}


public function updateTargetMuscles(Request $request)
{
    $request->validate([
        'target_muscles' => 'required|array',
       
        'target_muscles.*' => 'string|in:Shoulder,Triceps,Biceps,Chest,Neck,Legs,Abs,Salf Muscles,Trapezius,Deltoids,Hips,shoulder,triceps,biceps,chest,neck,legs,abs,salf muscles,trapezius,deltoids,hips'
    ]);

    $user = $request->user();

    
    $lowerMuscles = array_map('strtolower', $request->target_muscles);

    DB::connection('mongodb')->table('profiles')->updateOrInsert(
        ['user_id' => (string) $user->_id],
        [
            'target_muscles' => $lowerMuscles,
            'updated_at' => now()
        ]
    );

    return response()->json([
        'status' => true,
        'message' => 'Target muscles updated successfully!',
        'data' => ['target_muscles' => $lowerMuscles]
    ]);
}


public function updateFullProfile(Request $request)
    {
    
        $request->validate([
            'birthdate' => 'nullable|date',
            'age'       => 'nullable|integer|min:10|max:100',
            'gender'    => 'nullable|in:male,female,Male,Female',
            'height'    => 'nullable|numeric',
            'weight'    => 'nullable|numeric',
        ]);

        $user = $request->user();

        
        DB::connection('mongodb')->table('profiles')->updateOrInsert(
            ['user_id' => (string) $user->_id],
            [
                'birthdate'  => $request->birthdate, 
                'age'        => $request->age ? (int) $request->age : null,
                'gender'     => $request->gender ? strtolower($request->gender) : null,
                'height'     => $request->height,
                'weight'     => $request->weight,
                'updated_at' => now()
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully!',
            'data' => [
                'birthdate' => $request->birthdate,
                'age'       => $request->age
            ]
        ]);
    }
}