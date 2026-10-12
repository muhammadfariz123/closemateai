<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'extra_packages' => 'array',
        'addons' => 'array',
        'terms_and_conditions' => 'array',
        'payments' => 'array',
        'invoice_date' => 'date',
        'event_date' => 'date',
        'due_date' => 'date',
        'package_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal_package' => 'decimal:2',
        'total_addon' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'dp1_amount' => 'decimal:2',
        'dp2_amount' => 'decimal:2',
        'total_dp' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
