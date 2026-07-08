<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MitraStatusNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public object $submission;
    public string $customMessage;
    public bool $isApproved;

    public function __construct(object $submission, string $customMessage)
    {
        $this->submission = $submission;
        $this->customMessage = $customMessage;
        $this->isApproved = $submission->status === 'disetujui';
    }

    public function envelope(): Envelope
    {
        $statusText = $this->isApproved ? 'Disetujui' : 'Ditolak';

        return new Envelope(
            subject: "Status Pengajuan Kerja Sama: {$statusText} — {$this->submission->kode_pengajuan}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mitra-status',
        );
    }
}
