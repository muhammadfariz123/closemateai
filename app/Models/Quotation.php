<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'items' => 'array',
        'termins' => 'array',
        'event_date' => 'date',
        'valid_until' => 'date',
    ];
}
