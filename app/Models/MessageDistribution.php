<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageDistribution extends Model
{
    public function message()
    {
        return $this->belongsTo(Message::class,'message_id');
    }
}
