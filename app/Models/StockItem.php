<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'category',
        'unit',
        'stock_system',
        'min_stock',
        'price',
        'notes',
    ];

    protected $casts = [
        'stock_system' => 'integer',
        'min_stock' => 'integer',
        'price' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opnames()
    {
        return $this->hasMany(StockOpname::class, 'stock_item_id');
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'stock_item_id');
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_system <= 0) {
            return 'Habis';
        }
        if ($this->stock_system <= $this->min_stock) {
            return 'Menipis';
        }
        return 'Tersedia';
    }
}
