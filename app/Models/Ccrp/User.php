<?php
namespace App\Models\Ccrp;
use Illuminate\Support\Facades\DB;

class User extends Coldchain2Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $fillable = ['id','usertype','userlevel','username','company','company_id','company_type','email','mobile','password','sex','age','birthday','realname','login','last_login_time','last_login_ip','reg_ip','reg_type','ctime','utime','status','cooler_category','binding_vehicle','binding_printer','menu_setting'];

    public function user_company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    //验证用户名是否正确
    public function checkUsername($username)
    {
        return $this->where('username',$username)->where('status',1)->select('id','username','password',DB::raw('company_id as unitid'))->first();
    }
    //验证密码是否正确
    public function checkPassword($user,$password)
    {
        return $this->user_md5($password)==$user->password;
    }
    private function user_md5($str, $auth_key=null){
        if(!$auth_key){
            $auth_key = 'PVHnDaiaS!wm>DopYhkMT:Mn^)UK]w#Kc}xr>vh-"z/#MMktgAf_NKx!%XPc*STF';
        }
        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }
}
