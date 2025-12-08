<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCommentNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Экземпляр комментария
     */
    public $comment;

    /**
     * Создать новый экземпляр сообщения.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Получить конверт сообщения.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новый комментарий к статье: ' . $this->comment->article->title,
        );
    }

    /**
     * Получить определение содержимого сообщения.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-comment',
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

