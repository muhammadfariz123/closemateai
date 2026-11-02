<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialMasterSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'division',
        'name',
        'position',
        'qty',
        'price',
        'total',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'float',
        'total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
