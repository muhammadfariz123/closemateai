<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisSetting extends Model
{
    use HasFactory;

    protected $table = 'hris_settings';

    protected $fillable = [
        'user_id',
        'studio_name',
        'studio_address',
        'studio_lat',
        'studio_lng',
        'radius_meters',
        'work_start_time',
        'work_end_time',
        'late_tolerance_minutes',
        'overtime_rate_per_hour',
        'late_penalty_per_hour',
    ];

    protected $casts = [
        'studio_lat' => 'float',
        'studio_lng' => 'float',
        'radius_meters' => 'integer',
        'late_tolerance_minutes' => 'integer',
        'overtime_rate_per_hour' => 'float',
        'late_penalty_per_hour' => 'float',
    ];
}
