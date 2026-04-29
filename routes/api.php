<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/social-login', [AuthController::class, 'socialLogin']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']); 


Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/profile/update-gender', [UserController::class, 'updateGender']);
    Route::post('/profile/update-physical', [UserController::class, 'updatePhysicalInfo']);
    Route::post('/profile/update-age', [UserController::class, 'updateAge']);
    Route::post('/profile/update-goal', [UserController::class, 'updateGoal']);
    Route::post('/profile/update-muscles', [UserController::class, 'updateTargetMuscles']);
    Route::get('/user/me', [AuthController::class, 'getMe']);
    Route::post('/user/update-full-profile', [UserController::class, 'updateFullProfile']);
});


Route::get('/test-api', function () {
    return response()->json(['message' => 'Hello Zeina, API is working!']);
});
Route::get('/get-all-users', function () {
    $users = \App\Models\User::all();
    
    foreach ($users as $user) {
        $profile = Illuminate\Support\Facades\DB::connection('mongodb')
            ->table('profiles')
            ->where('user_id', $user->_id) 
            ->orWhere('user_id', (string) $user->_id) 
            ->orWhere('user_id', new \MongoDB\BSON\ObjectId((string) $user->_id)) 
            ->first();
            
        $user->profile_info = $profile; 
    }
    
    return response()->json($users);
});




