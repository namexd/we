<?php

namespace App\Models\Ccms;
class WarningSendlog extends Coldchain2Model
{

    function collector()
    {
        return $this->belongsTo('collector');
    }

    function getSetTimeAttr($value){
        return toDate($value);
    }

    function getTempWarningAttr($value){
        return is_is($value,'开启','关闭');
    }
    function getTempAreaAttr(){
        return  $this->temp_low.'~'.$this->temp_high.'℃';
    }
}
