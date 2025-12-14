<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewArticleNotification extends Notification
{
    use Queueable;

    /**
     * Экземпляр статьи
     */
    public $article;

    /**
     * Create a new notification instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Убеждаемся, что связь user загружена
        if (!$this->article->relationLoaded('user')) {
            $this->article->load('user');
        }
        
        return [
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'article_text' => $this->article->text,
            'article_date_public' => $this->article->date_public,
            'author_name' => $this->article->user->name ?? 'Неизвестен',
            'message' => 'Добавлена новая статья: ' . $this->article->title,
        ];
    }
}

