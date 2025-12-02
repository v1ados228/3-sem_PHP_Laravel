<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Получить пользователей с этой ролью
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Проверка, является ли роль модератором
     */
    public function isModerator(): bool
    {
        return $this->slug === 'moderator';
    }

    /**
     * Проверка, является ли роль читателем
     */
    public function isReader(): bool
    {
        return $this->slug === 'reader';
    }
}

