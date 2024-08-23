<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class InviteUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Invitation  $invitation
     * @param  string|null  $password
     * @return void
     */
    public function __construct($invitation, $password = null)
    {
        $this->invitation = $invitation;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invite User Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invite_user',
            with: [
                'acceptUrl' => URL::temporarySignedRoute(
                    'company.accept_invitation',
                    now()->addMinutes(60),
                    ['token' => $this->invitation->token]
                ),
                'password' => $this->password,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
