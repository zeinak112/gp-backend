<?php

namespace App\Models;

// استخدام كلاس الموديل الخاص بالمونجو بدلاً من الـ Eloquent العادي
use MongoDB\Laravel\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
    use HasFactory, Notifiable, Authenticatable;

    // تحديد نوع الاتصال واسم الكولكشن
    protected $connection = 'mongodb';
    protected $collection = 'users';

    // الحقول المسموح بكتابتها
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // إخفاء الباسورد عند إرسال البيانات للفلاتر
    protected $hidden = [
        'password',
        'remember_token',
    ];
}