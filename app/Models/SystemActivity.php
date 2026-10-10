<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemActivity extends Model
{
    protected $fillable = [
        'user_id', 'type', 'actor', 'title', 'desc', 'icon', 'color'
    ];
}
