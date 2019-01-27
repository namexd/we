<?php

namespace App\Models\Ccrp;

class StatManualRecord extends Coldchain2Model
{
    protected $table = 'stat_monthly';

    public function signature()
    {
        return $this->hasOne(Signature::class, 'sign_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cooler()
    {
        return $this->belongsTo(Cooler::class);
    }
}
