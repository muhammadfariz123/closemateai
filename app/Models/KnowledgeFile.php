<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeFile extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'extracted_text'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
