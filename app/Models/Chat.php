<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'is_pinned',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
