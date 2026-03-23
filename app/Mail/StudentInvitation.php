<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $studentEmail,
        public string $profesorName,
        public string $courseTitle,
        public string $tempPassword,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Te han inscrito en un curso — Keiyi Academy',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student_invitation',
        );
    }
}
