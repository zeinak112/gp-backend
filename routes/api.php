
<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// تجربة لجلب كل المستخدمين (للتأكد من الربط مع مونجو)
Route::get('/get-all-users', function () {
    return User::all();
});

// مسارات المصادقة (Authentication Routes)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/social-login', [AuthController::class, 'socialLogin']);


// مسار لطلب إرسال كود التحقق (أو التأكد من وجود الإيميل)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// مسار لتعيين الباسورد الجديد (بعد ما اليوزر يتأكد من إيميله)
Route::post('/reset-password', [AuthController::class, 'resetPassword']);   