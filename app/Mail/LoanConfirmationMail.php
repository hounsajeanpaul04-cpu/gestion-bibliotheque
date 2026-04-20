<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Mail\Mailable;

class LoanConfirmationMail extends Mailable
{
    public $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre emprunt')
                    ->view('emails.loan_confirmation');
    }
}