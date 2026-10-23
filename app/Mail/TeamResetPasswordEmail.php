<?php

namespace App\Mail;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $owner;
    public $resetUrl;

    public function __construct(TeamMember $member, User $owner)
    {
        $this->member = $member;
        $this->owner = $owner;
        $this->resetUrl = url('/invite/accept/' . $member->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Atur ulang kata sandi Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team_reset_password',
        );
    }
}
