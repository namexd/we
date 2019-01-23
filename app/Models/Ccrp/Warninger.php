<?php

namespace App\Models\Ccrp;

class Warninger extends Coldchain2Model
{
    protected $table = 'warninger';
    protected $primaryKey = 'warninger_id';
    protected $fillable = ['warninger_id','warninger_name','warninger_type','warninger_type_level2','warninger_type_level3','warninger_body','warninger_body_pluswx','warninger_body_level2','warninger_body_level2_pluswx','warninger_body_level3','warninger_body_level3_pluswx','using_sensor_num','set_time','set_uid','bind_times','category_id','company_id'];
    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
