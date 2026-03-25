<?php

namespace App\Models;


use MongoDB\Laravel\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
    use HasFactory, Notifiable, Authenticatable;

  
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