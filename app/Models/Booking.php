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

    public function setEventDateAttribute($value)
    {
        $this->attributes['event_date'] = (empty($value) || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setPaymentDateAttribute($value)
    {
        $this->attributes['payment_date'] = (empty($value) || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = (empty($value) || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = (empty($value) || (is_string($value) && trim($value) === '')) ? null : $value;
    }

    public function setPaymentStatusAttribute($value)
    {
        $this->attributes['payment_status'] = (empty($value) || (is_string($value) && trim($value) === '')) ? 'DP 1' : $value;
    }

    public function setProductionStatusAttribute($value)
    {
        $this->attributes['production_status'] = (empty($value) || (is_string($value) && trim($value) === '')) ? 'Pre-Event' : $value;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (empty($model->payment_status)) {
                $model->payment_status = 'DP 1';
            }
            if (empty($model->production_status)) {
                $model->production_status = 'Pre-Event';
            }
        });

        static::saving(function ($model) {
            if (empty($model->payment_status)) {
                $model->payment_status = 'DP 1';
            }
            if (empty($model->production_status)) {
                $model->production_status = 'Pre-Event';
            }
            if (empty($model->client_name)) {
                $model->client_name = 'Klien Baru';
            }
            if (empty($model->client_wa_number)) {
                $model->client_wa_number = '-';
            }
        });
    }
}
