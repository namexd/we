<?php

namespace App\Models;

use App\Models\Ccrp\Company;
use DB;
use Illuminate\Database\Eloquent\Model;
use Mockery\CountValidator\Exception;

class EquipmentChangeApply extends Model
{
    protected $fillable = [
        'company_id',
        'phone',
        'apply_time',
        'user_id',
        'user_name',
        'user_sign',
        'check_unit',
        'check_user',
        'check_commnet',
        'check_time',
        'handler',
        'end_time',
        'comment',
        'status',
    ];
    const CHANGE_TYPE = [
        1 => '信报警关闭',
        2 => '信报警重新开通',
        3 => '冰箱参数修改',
        4 => '冰箱备用',
        5 => '冰箱报废',
        6 => '警联系人变更',
        7 => '冰箱更换(报废 / 备用)',
        8 => '冰箱启用',
        9 => '改温度区间',
        10 => '取消探头',
        11 => '新增冰箱',
        12 => '门诊注销，停止监测',
        13 => '警延迟时间修改'

    ];
  const STATUS=[
      '未变更',
      '已变更'
  ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function detail()
    {
        return $this->hasMany(EquipmentChangeDetail::class, 'apply_id');
    }

    public function new()
    {
        return $this->hasMany(EquipmentChangeNew::class, 'apply_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'handler');
    }

    public function add($data)
    {
       try
        {
            $apply= DB::transaction(function () use ($data){
                $attributes=array_only($data,$this->fillable);
                if ($apply=self::create($attributes)){
                    if (!is_null(json_decode($data['details'])))
                    {
                       $details=json_decode($data['details'],true);
                       $apply->detail()->createMany($details);

                    }
                    if (!is_null(json_decode($data['news'])))
                    {
                        $news=json_decode($data['news'],true);
                        $apply->new()->createMany($news);
                    }
                    return $apply;
                }

            },5);
          return $apply;
        }catch (Exception $exception)
       {
           return $exception->getMessage();
       }

    }
    public function getChangeType()
    {
        $k=0;
        foreach (self::CHANGE_TYPE as $key=> $value)
        {
            $result[$k]['key']=$key;
            $result[$k]['value']=$value;
            $k++;
        }
        return $result;
    }
}
