<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Article;
use App\Policies\ArticlePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Шлюз для проверки прав модератора
        // Используется before для проверки прежде остальных шлюзов
        Gate::before(function (User $user, string $ability) {
            // Если пользователь модератор, разрешаем все действия
            if ($user->isModerator()) {
                return true;
            }
            // Иначе продолжаем проверку через политики
            return null;
        });

        // Дополнительный шлюз для явной проверки модератора
        Gate::define('is-moderator', function (User $user) {
            return $user->isModerator();
        });

        // Шлюз для проверки прав читателя
        Gate::define('is-reader', function (User $user) {
            return $user->isReader();
        });
    }
}
