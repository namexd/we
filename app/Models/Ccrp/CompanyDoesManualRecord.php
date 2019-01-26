<?php

namespace App\Models\Ccrp;

class CompanyDoesManualRecord extends Coldchain2Model
{

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

    public function isDone()
    {
        return $this->hasMany(StatManualRecord::class, 'company_id', 'company_id')
            ->where('year', date('Y'))
            ->where('month', date('m'))
            ->where('day', date('d'))
            ->where('sign_time_a', date('A'));
    }
}
