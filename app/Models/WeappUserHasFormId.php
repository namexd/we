<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WeappUserHasFormId extends Model
{
    protected $fillable=['weapp_user_id','form_id','expire_at'];

    public function add($data)
    {
        $data['expire_at']=Carbon::now()->addDays(7)->timestamp;
        $result=self::updateOrCreate(array_only($data,'weapp_user_id'),$data);
        return $result;
    }
}
