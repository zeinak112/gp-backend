<?php

namespace App\Models;


use MongoDB\Laravel\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable 
{
    // ضيفي HasApiTokens هنا عشان الـ createToken تشتغل
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'facebook_id',
        'instagram_id',
        'avatar',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}