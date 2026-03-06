<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/get-all-users', function () {
    return User::all();
});

Route::post('/register', [AuthController::class, 'register']);


Route::post('/login', [AuthController::class, 'login']);