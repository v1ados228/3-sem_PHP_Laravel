<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    /**
     * Определить, может ли пользователь просматривать список статей.
     */
    public function viewAny(User $user): bool
    {
        // Все авторизованные пользователи могут просматривать список
        return true;
    }

    /**
     * Определить, может ли пользователь просматривать статью.
     */
    public function view(User $user, Article $article): bool
    {
        // Все авторизованные пользователи могут просматривать статьи
        return true;
    }

    /**
     * Определить, может ли пользователь создавать статьи.
     */
    public function create(User $user): bool
    {
        // Только модератор может создавать статьи
        return $user->isModerator();
    }

    /**
     * Определить, может ли пользователь обновлять статью.
     */
    public function update(User $user, Article $article): bool
    {
        // Только модератор может обновлять статьи
        return $user->isModerator();
    }

    /**
     * Определить, может ли пользователь удалять статью.
     */
    public function delete(User $user, Article $article): bool
    {
        // Только модератор может удалять статьи
        return $user->isModerator();
    }
}

