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
        'event_date' => 'date:Y-m-d',
        'payment_date' => 'date:Y-m-d',
        'production_tracks' => 'array',
    ];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

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
