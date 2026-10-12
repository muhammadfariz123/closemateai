<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePackage extends Model
{
    use HasFactory;

    protected $table = 'invoice_packages';

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'discount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
