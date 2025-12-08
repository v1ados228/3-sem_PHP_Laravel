<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewArticleNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Экземпляр статьи
     */
    public $article;

    /**
     * Создать новый экземпляр сообщения.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Получить конверт сообщения.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая статья добавлена: ' . $this->article->title,
        );
    }

    /**
     * Получить определение содержимого сообщения.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-article',
        );
    }

    /**
     * Получить вложения для сообщения.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

