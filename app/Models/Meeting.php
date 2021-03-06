<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    public function registrations()
    {
        return $this->hasMany(MeetingRegistration::class,'meeting_id','id');
    }
}
