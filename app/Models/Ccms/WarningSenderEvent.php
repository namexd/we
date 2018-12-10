<?php

namespace App\Models\Ccms;
class WarningSenderEvent extends Coldchain2Model
{

    function sender(){
        return $this->belongsTo(Sender::class,'sender_id','sender_id');
    }
}
