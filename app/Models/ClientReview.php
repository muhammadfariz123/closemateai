<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientReview extends Model
{
    use HasFactory;

    protected $table = 'client_reviews';

    protected $fillable = [
        'user_id',
        'booking_id',
        'booking_uuid',
        'client_name',
        'client_phone',
        'field_crew_names',
        'rating_field_crew',
        'rating_punctuality',
        'rating_hospitality',
        'rating_overall',
        'positive_tags',
        'review_comment',
        'is_public',
    ];

    protected $casts = [
        'rating_field_crew' => 'integer',
        'rating_punctuality' => 'integer',
        'rating_hospitality' => 'integer',
        'rating_overall' => 'integer',
        'is_public' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
