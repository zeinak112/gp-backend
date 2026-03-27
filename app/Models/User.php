<?php
namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable; 
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\NewAccessToken; // سطر جديد مهم

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'google_id', 
        'facebook_id', 'instagram_id', 'avatar'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

        public function createToken(string $name, array $abilities = ['*'], $expiresAt = null)
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = \Illuminate\Support\Str::random(40)),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }
}