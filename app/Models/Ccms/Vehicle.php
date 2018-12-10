<?php

namespace App\Models\Ccms;
class Vehicle extends Coldchain2Model
{


    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id')->field('id,title,short_title');
    }



}
