<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBalanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // 'topup', 'purchase', 'adjustment'
        'amount',
        'balance_after',
        'notes',
        'created_by',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
