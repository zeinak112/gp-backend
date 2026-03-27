<?php

namespace App\Models;


use MongoDB\Laravel\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Eloquent implements Authenticatable
{
    
    use HasApiTokens, HasFactory, Notifiable, AuthenticableTrait;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'google_id', 
        'facebook_id', 'instagram_id', 'avatar'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];



    

     public function tokens()
        {
  
           return $this->hasMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable_id');
          }

    
}