<?php

namespace App\Mail;

use App\Models\DownloadToken;
use App\Models\PrintCatalog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DownloadLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DownloadToken $downloadToken,
        public PrintCatalog $item,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tu link de descarga — ' . $this->item->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.download_link');
    }
}
