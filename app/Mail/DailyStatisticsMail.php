<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyStatisticsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $viewsCount;
    public $commentsCount;
    public $articleViews;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct($viewsCount, $commentsCount, $articleViews, $date)
    {
        $this->viewsCount = $viewsCount;
        $this->commentsCount = $commentsCount;
        $this->articleViews = $articleViews;
        $this->date = $date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Статистика использования сайта за ' . $this->date,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-statistics',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

