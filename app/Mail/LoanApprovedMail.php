<?php

namespace App\Mail;

use App\Models\Loan; // N'oublie pas d'importer ton modèle Loan
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    // 1. On déclare la variable publique pour que Laravel puisse l'utiliser
    public $loan;

    /**
     * Create a new message instance.
     * On reçoit le $loan ici depuis le contrôleur
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre emprunt a été validé ! 📚',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loan_approved',
            with: [
                'user' => $this->loan->user, 
                'book' => $this->loan->book
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}