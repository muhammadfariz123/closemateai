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
        'package_price' => 'float',
        'package_qty' => 'integer',
        'paid_amount' => 'float',
        'discount' => 'float',
        'total_income' => 'float',
        'total_operational_cost' => 'float',
        'net_profit' => 'float',
        // WA Booking Form Fields
        'dp_amount' => 'float',
        'transport_cost' => 'float',
        'total_dp' => 'float',
        'wa_form_filled_at' => 'datetime',
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
