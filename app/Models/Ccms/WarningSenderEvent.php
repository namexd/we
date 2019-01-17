<?php

namespace App\Models\Ccms;
class WarningSenderEvent extends Coldchain2Model
{
    protected $table = 'warning_sender_event';
    protected $primaryKey = 'logid';

    function sender()
    {
        return $this->belongsTo(Sender::class, 'sender_id', 'sender_id')->where('company_id', $this->company_id);
    }

    function options()
    {
        //断电报警类型：5
        $this->warning_type = 5;
        return $this->hasMany(WarningEventOption::class, 'warning_type', 'warning_type');
    }
}
