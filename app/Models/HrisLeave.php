<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisLeave extends Model
{
    use HasFactory;

    protected $table = 'hris_leaves';

    protected $fillable = [
        'user_id',
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'admin_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(HrisEmployee::class, 'employee_id');
    }
}

