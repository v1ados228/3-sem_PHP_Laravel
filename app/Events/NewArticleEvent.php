<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewArticleEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Экземпляр статьи
     */
    public $article;

    /**
     * Создать новый экземпляр события.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Получить каналы, на которые должно транслироваться событие.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('articles');
    }

    /**
     * Имя события для трансляции.
     */
    public function broadcastAs(): string
    {
        return 'NewArticleEvent';
    }

    /**
     * Получить данные для трансляции.
     */
    public function broadcastWith(): array
    {
        return [
            'article' => [
                'id' => $this->article->id,
                'title' => $this->article->title,
                'text' => $this->article->text,
                'date_public' => $this->article->date_public,
                'author' => $this->article->user->name ?? 'Неизвестен',
                'author_id' => $this->article->users_id,
            ]
        ];
    }
}

