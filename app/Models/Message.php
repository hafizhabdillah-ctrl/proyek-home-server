<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    protected $guarded = [];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
