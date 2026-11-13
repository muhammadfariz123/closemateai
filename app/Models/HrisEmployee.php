<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisEmployee extends Model
{
    use HasFactory;

    protected $table = 'hris_employees';

    protected $fillable = [
        'user_id',
        'auth_user_id',
        'employee_code',
        'name',
        'email',
        'phone',
        'division',
        'position',
        'role',
        'base_salary',
        'allowance',
        'leave_balance',
        'status',
        'avatar_url',
    ];

    protected $casts = [
        'base_salary' => 'float',
        'allowance' => 'float',
        'leave_balance' => 'integer',
    ];

    public function attendances()
    {
        return $this->hasMany(HrisAttendance::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(HrisLeave::class, 'employee_id');
    }

    public function overtimes()
    {
        return $this->hasMany(HrisOvertime::class, 'employee_id');
    }

    public function payrolls()
    {
        return $this->hasMany(HrisPayroll::class, 'employee_id');
    }

    public function isMarketing(): bool
    {
        return strtolower($this->division ?? '') === 'marketing';
    }

    public function canManage(): bool
    {
        return in_array(strtolower($this->role ?? ''), ['owner', 'admin', 'hr', 'supervisor']);
    }
}

