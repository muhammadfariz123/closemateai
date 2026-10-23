<?php

namespace App\Mail;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $owner;
    public $acceptUrl;

    public function __construct(TeamMember $member, User $owner)
    {
        $this->member = $member;
        $this->owner = $owner;
        $this->acceptUrl = url('/invite/accept/' . $member->token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan bergabung ke CloseMateAI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team_invitation',
        );
    }
}
