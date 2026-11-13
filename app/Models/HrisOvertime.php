<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisOvertime extends Model
{
    use HasFactory;

    protected $table = 'hris_overtimes';

    protected $fillable = [
        'user_id',
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'duration_hours',
        'project_name',
        'description',
        'estimated_pay',
        'status',
        'approved_by',
        'admin_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'duration_hours' => 'float',
        'estimated_pay' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(HrisEmployee::class, 'employee_id');
    }
}

