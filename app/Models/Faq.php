<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'user_id',
        'question',
        'answer',
        'category',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
