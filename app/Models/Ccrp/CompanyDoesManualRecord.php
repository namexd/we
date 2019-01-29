<?php

namespace App\Models\Ccrp;

class CompanyDoesManualRecord extends Coldchain2Model
{

    const 签名间隔小时 = 6;
    const 设备类型 =
        [
            Cooler::设备类型_冷藏冰箱,
            Cooler::设备类型_冷冻冰箱,
            Cooler::设备类型_普通冰箱,
            Cooler::设备类型_冷藏冷库,
            Cooler::设备类型_冷冻冷库
        ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function needRecord()
    {
        if ((bool)$this->hasRecords()->count()) {
            return ['status' => false, 'tips' => '已经签过了'];
        } else {
            if (date('A') == 'PM') {
                $am_record = $this->hasRecords(null, null, null, 'AM')->first();
                if (time() - $am_record->sign_time > (self::签名间隔小时 * 3600)) {
                    return ['status' => false, 'tips' => '距离上午记录还没有超过6小时，预计' . (date('H:i', $am_record->sign_time + 6 * 3600) . '可以记录')];
                }
            }
            return ['status' => true, 'tips' => '疫苗储存和运输管理规范（2017年版），第十一条规定："每天上午和下午至少各进行一次人工温度记录（间隔不少于6小时）"； 建议上午8~9:00，下午16~17:00进行记录'];
        }
    }

    //记录关系表
    public function records()
    {
        return $this->hasMany(StatManualRecord::class, 'company_id', 'company_id');
    }

    //指定时间查询
    public function hasRecords($year = null, $month = null, $day = null, $session = null)
    {
        return $this->records
            ->where('year', $year ?? date('Y'))
            ->where('month', $month ?? date('m'))
            ->where('day', $day ?? date('d'))
            ->where('sign_time_a', $session ?? date('A'));
    }

    public function isDone()
    {
        $needs = $this->needRecord();
        return $needs['status'];
    }

}
