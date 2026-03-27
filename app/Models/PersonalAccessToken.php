<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasAbilities;

class PersonalAccessToken extends Model implements HasAbilities
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';

    protected $fillable = [
        'name', 'token', 'abilities', 'last_used_at', 'expires_at', 'tokenable_id', 'tokenable_type'
    ];

    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

   
    public static function findToken($token)
{
    
    $plainToken = strpos($token, '|') !== false ? explode('|', $token, 2)[1] : $token;
    
    return static::where('token', hash('sha256', $plainToken))->first() 
           ?? static::where('token', $plainToken)->first();
}
    public function tokenable()
    {
        return $this->morphTo();
    }

    public function can($ability): bool
    {
        return in_array('*', $this->abilities) || in_array($ability, $this->abilities);
    }

    public function cant($ability): bool
    {
        return !$this->can($ability);
    }
}