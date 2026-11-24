<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderDeadlineMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $namaUser,
        public string $role,
        public string $aktivitas,
        public string $deadline,
        public int    $hariTersisa,
        public string $bulanTahun,
        public string $urlSistem = 'http://localhost/monitoring-kinerja/public/app',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[SIMONIKA] Pengingat Deadline – {$this->bulanTahun}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reminder-deadline',
        );
    }
}
