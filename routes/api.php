
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


Route::get('/test-env', function () {
    return response()->json([
        'status' => 'Checking Environment Variables...',
        'google' => [
            'id' => env('GOOGLE_CLIENT_ID') ? '✅ Found' : '❌ Not Found',
            'secret' => env('GOOGLE_CLIENT_SECRET') ? '✅ Found' : '❌ Not Found',
            'redirect' => env('GOOGLE_REDIRECT_URL') ?: '⚠️ Missing',
        ],
        'facebook' => [
            'id' => env('FACEBOOK_CLIENT_ID') ? '✅ Found' : '❌ Not Found',
            'secret' => env('FACEBOOK_CLIENT_SECRET') ? '✅ Found' : '❌ Not Found',
            'redirect' => env('FACEBOOK_REDIRECT_URL') ?: '⚠️ Missing',
        ],
        'instagram' => [
            'id' => env('INSTAGRAM_CLIENT_ID') ? '✅ Found' : '❌ Not Found',
            'secret' => env('INSTAGRAM_CLIENT_SECRET') ? '✅ Found' : '❌ Not Found',
            'redirect' => env('INSTAGRAM_REDIRECT_URL') ?: '⚠️ Missing',
        ],
        'app_url' => env('APP_URL'),
    ]);
});