<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeSetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public Employee $employee;
    public string $token;
    public bool $isNewAccount;
    public string $setPasswordUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Employee $employee, string $token, bool $isNewAccount = true)
    {
        $this->employee = $employee;
        $this->token = $token;
        $this->isNewAccount = $isNewAccount;
        $this->setPasswordUrl = url('/employee/set-password?token=' . $token . '&email=' . urlencode($employee->email));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isNewAccount
            ? "Welcome to IntraEats & Talisha Software — Set Your Employee Password"
            : "IntraEats & Talisha Software — Reset Your Employee Password";

        return new Envelope(
            subject: $subject,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(env('MAIL_HR', 'hr@intraeats.com'), 'IntraEats & Talisha Software HR'),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.employee-set-password',
        );
    }
}
