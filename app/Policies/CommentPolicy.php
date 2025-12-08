<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    /**
     * Определить, может ли пользователь обновлять комментарий.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Пользователь может обновлять только свой комментарий
        return $comment->users_id === $user->id;
    }

    /**
     * Определить, может ли пользователь удалять комментарий.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Пользователь может удалять только свой комментарий
        return $comment->users_id === $user->id;
    }
}

