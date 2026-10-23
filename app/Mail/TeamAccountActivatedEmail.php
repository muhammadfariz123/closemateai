<?php

namespace App\Mail;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamAccountActivatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $user;
    public $dashboardUrl;
    public $loginUrl;

    public function __construct(TeamMember $member, User $user)
    {
        $this->member = $member;
        $this->user = $user;
        $this->dashboardUrl = url('/dashboard');
        $this->loginUrl = url('/login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di CloseMate AI — Akun Anda Sudah Aktif!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team_account_activated',
        );
    }
}
