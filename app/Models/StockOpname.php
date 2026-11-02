<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'inspector_id',
        'stock_item_id',
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
        'status', // 'pending', 'approved', 'rejected'
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'system_stock' => 'integer',
        'physical_stock' => 'integer',
        'difference' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

