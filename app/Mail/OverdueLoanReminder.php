<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverdueLoanReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * On utilise la promotion de propriété de PHP 8 pour déclarer $loan.
     * Cela rend l'objet accessible automatiquement dans votre vue Blade.
     */
    public function __construct(
        public Loan $loan
    ) {}

    /**
     * Définit l'objet de l'email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel : Votre prêt de livre est en retard',
        );
    }

    /**
     * Définit la vue Blade qui sera utilisée pour le corps de l'email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue_loan',
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