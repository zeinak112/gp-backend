
<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::get('/get-all-users', function () {
    return User::all();
});

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
});



Route::get('/test-api', function () {
    return response()->json(['message' => 'Hello Zeina, API is working!']);
});
