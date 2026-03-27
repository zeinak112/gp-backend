<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; // موديل المونجو
use Laravel\Sanctum\Contracts\HasAbilities; // العقد بتاع الصلاحيات

class PersonalAccessToken extends Model implements HasAbilities
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens'; // تأكدي من اسم الكولكشن

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'tokenable_id',
        'tokenable_type'
    ];

    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // الـ Relationship الأساسية
    public function tokenable()
    {
        return $this->morphTo();
    }

    // دوال الصلاحيات اللي Sanctum بيحتاجها
    public function can($ability): bool
    {
        return in_array('*', $this->abilities) || in_array($ability, $this->abilities);
    }

    public function cant($ability): bool
    {
        return !$this->can($ability);
    }
}