<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Role;
use App\Mail\NewArticleNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class VeryLongJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Экземпляр статьи
     */
    public $article;

    /**
     * Количество попыток выполнения задания
     */
    public $tries = 3;

    /**
     * Время ожидания перед повторной попыткой (в секундах)
     */
    public $backoff = 60;

    /**
     * Создать новый экземпляр задания.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
        \Log::info('VeryLongJob created for article ID: ' . $article->id);
    }

    /**
     * Выполнить задание.
     */
    public function handle(): void
    {
        \Log::info('VeryLongJob handle() started for article ID: ' . $this->article->id);
        
        // Загружаем связь с пользователем для email
        $this->article->load('user');
        
        // Находим всех модераторов и отправляем им уведомления
        $moderatorRole = Role::where('slug', 'moderator')->first();
        
        if ($moderatorRole) {
            $moderators = $moderatorRole->users()->get();
            
            \Log::info('Found ' . $moderators->count() . ' moderators to notify');
            
            foreach ($moderators as $moderator) {
                \Log::info('Sending email to moderator: ' . $moderator->email);
                Mail::to($moderator->email)->send(new NewArticleNotification($this->article));
            }
        }
        
        \Log::info('VeryLongJob handle() completed for article ID: ' . $this->article->id);
    }

    /**
     * Обработать провал задания.
     */
    public function failed(\Throwable $exception): void
    {
        // Логируем ошибку или отправляем уведомление администратору
        \Log::error('Ошибка при отправке уведомления о новой статье: ' . $exception->getMessage());
    }
}

