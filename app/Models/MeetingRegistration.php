<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingRegistration extends Model
{
    public $fillable = [
        'user_id',
        'user_name',
        'phone',
        'meeting_id'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
