<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisPayroll extends Model
{
    use HasFactory;

    protected $table = 'hris_payrolls';

    protected $fillable = [
        'user_id',
        'employee_id',
        'period_month',
        'base_salary',
        'allowance',
        'overtime_pay',
        'bonus',
        'deductions',
        'late_deductions',
        'late_deductions_count',
        'unexcused_deductions',
        'other_deductions',
        'net_salary',
        'total_present',
        'total_late',
        'total_leave',
        'total_overtime_hours',
        'status',
        'published_at',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'float',
        'allowance' => 'float',
        'overtime_pay' => 'float',
        'bonus' => 'float',
        'deductions' => 'float',
        'late_deductions' => 'float',
        'net_salary' => 'float',
        'total_present' => 'integer',
        'total_late' => 'integer',
        'total_leave' => 'integer',
        'total_overtime_hours' => 'float',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(HrisEmployee::class, 'employee_id');
    }
}

