<?php
namespace App\Models\Ccms;
class User extends Coldchain2Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $fillable = ['id','usertype','userlevel','username','company','company_id','company_type','email','mobile','password','sex','age','birthday','realname','login','last_login_time','last_login_ip','reg_ip','reg_type','ctime','utime','status','cooler_category','binding_vehicle','binding_printer','menu_setting'];

    public function user_company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
