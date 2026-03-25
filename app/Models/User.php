<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\HasApiTokens; 

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'google_id', 
        'facebook_id', 'instagram_id', 'avatar', 
        'gender', 'height', 'weight' 
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}