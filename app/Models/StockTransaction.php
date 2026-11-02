<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stock_item_id',
        'item_name',
        'type', // 'in', 'out', 'purchase', 'opname_adjustment'
        'qty',
        'unit_price',
        'total_price',
        'receipt_file',
        'notes',
        'status', // 'pending', 'approved', 'rejected'
        'created_by',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
        'verified_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

