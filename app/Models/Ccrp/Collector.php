<?php

namespace App\Models\Ccrp;


use function App\Utils\format_value;

/**
 * Class Collector
 * @package App\Models
 */
class Collector extends Coldchain2Model
{
    protected $table = 'collector';
    protected $primaryKey = 'collector_id';

    protected $fillable = ['collector_id', 'collector_name', 'cooler_id', 'cooler_name', 'supplier_product_model', 'supplier_collector_id', 'category_id', 'company_id', 'temp_warning', 'humi_warning', 'volt_warning', 'temp', 'humi', 'volt', 'rssi', 'update_time', 'install_time', 'uninstall_time','status'];


    public static $status = [
        '0'=>'禁用',
        '1'=>'正常',
        '2'=>'报废',
    ];

    public static $warning_type = [
        '0'=>'正常',//正常
        '1'=>'高温',
        '2'=>'低温',
//        '3'=>'高湿',
//        '4'=>'低湿',
//        '5'=>'断电',
//        '6'=>'电压低',
//        '7'=>'电压高',
    ];

    function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }
    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }


    function warningSetting(){
        return $this->hasOne(WarningSetting::class,'collector_id','collector_id')->where('status',1);
    }
    function warningEvent(){
        return $this->hasMany(WarningEvent::class);
    }

    public function getTempAttribute($value)
    {
        return format_value($value);
    }
    public function getHumiAttribute($value)
    {
        return format_value($value);
    }
    public function getVoltAttribute($value)
    {
        return format_value($value);
    }
    public function getWarningTypeAttr($value)
    {
        return self::$warning_type[$value];
    }
    public function getStatusAttr($value)
    {
        return self::$status[$value];
    }

    /**
     * @param null $start_time
     * @param null $end_time
     * @return DataHistory
     */
    function history($start_time=null,$end_time=null)
    {
        if($start_time !== null and $end_time === null){
            $end_time = $end_time??strtotime(date('Y-m-d 23:59:59',$start_time));
        }elseif($start_time === null and $end_time === null){
            $start_time = $start_time??strtotime(date('Y-m-d 00:00:00',time()));
            $end_time = $end_time??strtotime(date('Y-m-d 23:59:59',time()));
        }
        $history = new DataHistory();
        $sn = str_replace('-','',$this->supplier_collector_id);
        $history->tableName($sn);

        return $history->setTable('sensor.' . $sn . '')->whereBetween('sensor_collect_time',[$start_time,$end_time])->select(['data_id', 'temp', 'humi', 'sensor_collect_time as collect_time', 'system_time'])->limit(100)->get();
    }
    public static function lists_warning_type()
    {
        return [
            'list' => array2list(self::$warning_type),
            'default' => array2keys(self::$warning_type) //所有状态
        ];
    }
}
