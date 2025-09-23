<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLoginMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $loginUrl,
        public string $recipientName
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu acesso ao Formulário de Abertura',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.magic-login',
            with: [
                'loginUrl' => $this->loginUrl,
                'recipientName' => $this->recipientName,
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}