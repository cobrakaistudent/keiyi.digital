<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $courseTitle,
        public string $courseId,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Inscripción Confirmada — ' . $this->courseTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment_confirmation',
        );
    }
}
