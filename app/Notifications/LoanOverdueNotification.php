<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanOverdueNotification extends Notification
{
    use Queueable;

    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable): array
    {
        return ['mail']; // On envoie par email
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alerte : Emprunt en retard !')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le livre suivant n\'a pas été rendu à temps :')
            ->line('**Livre :** ' . $this->loan->book->title)
            ->line('**Emprunteur :** ' . $this->loan->user->name)
            ->line('**Date limite était le :** ' . $this->loan->due_date->format('d/m/Y'))
            ->action('Voir l\'emprunt', url('/admin/dashboard'))
            ->line('Merci de contacter l\'adhérent rapidement.');
    }
}