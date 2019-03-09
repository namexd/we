<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weapp extends Model
{
    const 智慧冷链公众号 = 1;
    const 智慧冷链小程序 = 2;
    const 智慧冷链用户中心 = 3;
    const 防疫小助手小程序 = 4;
    const miniProgram = [
        'default' => self::智慧冷链小程序,
        'fangyi' => self::防疫小助手小程序,
    ];
    protected $fillable = [
        'type',
        'name',
        'app_id',
        'secret',
        'token',
        'aes_key',
        'other'
    ];

    public function hasWeuser()
    {
        return $this->hasOne(WeappHasWeuser::class);
    }
}
