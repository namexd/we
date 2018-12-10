<?php


namespace App\Models\Ccms;

class WarningEvent extends Coldchain2Model
{
    protected $table = 'warning_event';
    protected $primaryKey = 'id';
    protected $fillable = ['id','collector_id','temp_warning','humi_warning','volt_warning','temp_event','humi_event','volt_event','temp_last','humi_last','volt_last','sensor_collect_time','warning_event_time','warning_last_time','warning_level','warning_type','temp_high','temp_low','humi_high','humi_low','volt_high','volt_low','handled','handled_time','handler','handler_note','handler_same_do','handler_uid','handler_type','category_id','company_id','system_type'];

    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    function collector()
    {
        return $this->belongsTo(Collector::class,'collector_id','collector_id');
    }
}
