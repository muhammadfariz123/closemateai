<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisAttendance extends Model
{
    use HasFactory;

    protected $table = 'hris_attendances';

    protected $fillable = [
        'user_id',
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'clock_in_photo',
        'clock_out_photo',
        'clock_in_lat',
        'clock_in_lng',
        'clock_out_lat',
        'clock_out_lng',
        'distance_meters',
        'status',
        'late_minutes',
        'is_marketing_bypass',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'clock_in_lat' => 'float',
        'clock_in_lng' => 'float',
        'clock_out_lat' => 'float',
        'clock_out_lng' => 'float',
        'distance_meters' => 'float',
        'late_minutes' => 'integer',
        'is_marketing_bypass' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(HrisEmployee::class, 'employee_id');
    }
}
