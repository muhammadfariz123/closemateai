<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'addons' => 'array',
        'operational_costs' => 'array',
        'team_members' => 'array',
        'event_date' => 'date',
        'payment_date' => 'date',
    ];
}
