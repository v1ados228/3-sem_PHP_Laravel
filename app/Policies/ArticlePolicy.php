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
        // Только модераторы могут создавать статьи
        return $user->isModerator();
    }

    /**
     * Определить, может ли пользователь обновлять статью.
     */
    public function update(User $user, Article $article): bool
    {
        // Пользователь может обновлять только свою статью
        return $article->users_id === $user->id;
    }

    /**
     * Определить, может ли пользователь удалять статью.
     */
    public function delete(User $user, Article $article): bool
    {
        // Пользователь может удалять только свою статью
        return $article->users_id === $user->id;
    }
}

