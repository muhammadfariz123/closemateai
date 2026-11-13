<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeWorkEvaluation extends Model
{
    use HasFactory;

    protected $table = 'office_work_evaluations';

    protected $fillable = [
        'user_id',
        'booking_id',
        'editor_name',
        'production_staff_name',
        'fast_edit_type',
        'edit_status',
        'edit_completion_hours',
        'print_deadline_date',
        'ready_to_pickup_date',
        'print_status',
        'quality_score',
        'office_notes',
    ];

    protected $casts = [
        'edit_completion_hours' => 'float',
        'print_deadline_date' => 'date',
        'ready_to_pickup_date' => 'date',
        'quality_score' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}

