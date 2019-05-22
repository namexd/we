<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const FROM_TYPE=[
        'system',
        'user',
        'app',
        'user_group',
    ];

    const MESSAGE_TYPE=[
        '1'=>'话题回顾提醒',
        '2'=>'设备预警通知',
        '3'=>'工单处理通知',
        '4'=>'报警信息提醒',
        '5'=>'工单进度通知',
        '6'=>'开通成功通知',
    ];

}
