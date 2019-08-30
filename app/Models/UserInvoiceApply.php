<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvoiceApply extends Model
{
    protected $fillable = [
        'user_id', 'info','fast_number', 'status'
    ];
    const STATUS = [
        '未处理', '已开具', '已快递', '已退回', '已报废'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatus()
    {
        $result=[];
        foreach (self::STATUS as $key=> $value)
        {
            $result[]=[
                'value'=>$key,
                'label'=>$value,
            ];
        }
        return $result;
    }
}
