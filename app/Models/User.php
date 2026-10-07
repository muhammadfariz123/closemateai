<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'business_name',
        'category',
        'google_id',
        'fonnte_token',
        'wa_status',
        'wa_number',
        'business_wa_number',
        'business_description',
        'webhook_secret',
        'notification_number',
        'owner_whatsapp',
        'notify_hot_lead',
        'notify_human_takeover',
        'ai_limit_enabled',
        'ai_multi_bubble_enabled',
        'ai_max_bubbles',
        'ai_require_data_before_price',
        'ai_required_data',
        'ai_custom_questions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ai_limit_enabled' => 'boolean',
            'ai_multi_bubble_enabled' => 'boolean',
            'ai_require_data_before_price' => 'boolean',
            'ai_required_data' => 'array',
        ];
    }
}
