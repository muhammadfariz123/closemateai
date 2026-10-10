<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'addons' => 'array',
        'operational_costs' => 'array',
        'team_members' => 'array',
        'event_date' => 'date',
        'payment_date' => 'date',
        'production_tracks' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
