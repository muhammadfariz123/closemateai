<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrisAnnouncement extends Model
{
    use HasFactory;

    protected $table = 'hris_announcements';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];
}
