<?php

namespace App\Models\Ccrp;

use function foo\func;
use Illuminate\Support\Facades\DB;

class User extends Coldchain2Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'usertype', 'userlevel', 'username', 'company', 'company_id', 'company_type', 'email', 'mobile', 'password', 'sex', 'age', 'birthday', 'realname', 'login', 'last_login_time', 'last_login_ip', 'reg_ip', 'reg_type', 'ctime', 'utime', 'status', 'cooler_category', 'binding_vehicle', 'binding_printer', 'menu_setting'];

    const STATUSES = ['0' => '禁用', '1' => '正常'];

    public function userCompany()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->where('status', 1);
    }

    //验证用户名是否正确
    public function checkUsername($username)
    {
        return $this->where('username', $username)->where('status', 1)->count()?true:false;
    }

    //验证密码是否正确
    public function checkPassword($username, $password)
    {
        return $this->where('username', $username)->where('password',$this->user_md5($password))->where('status', 1)->select('id', 'username',  DB::raw('company_id as unitid'))->first()??false;
    }

    //通过手机号验证是否实名认证
    public function checkPhone($username, $phone, $ucenter_user=null)
    {
        $user =  $this->where('username', $username)->where('status', 1)->first();
        $isContact = Contact::where('phone',$phone)->where('status',1)->where('company_id',$user->company_id)->count()>0?true:false;
        if($isContact == false){
            //check warninger
            $inWarninger = Warninger::where('company_id',$user->company_id)->whereIn('warninger_type',[1,4])->where('bind_times','>',0)->where(function ($query) use ($phone){
                $query->where('warninger_body', 'like', '%'.$phone.'%')
                    ->orWhere('warninger_body_level2', 'like', '%'.$phone.'%')
                    ->orWhere('warninger_body_level3', 'like', '%'.$phone.'%');
            })->count();
            if($inWarninger)
            {
                $hasContact = Contact::where('phone',$phone)->where('status',0)->where('company_id',$user->company_id)->first();
                if(!$hasContact) {
                    $contact = new Contact();
                    $contact->phone = $phone;
                    $contact->name = $ucenter_user->realname ?? $ucenter_user->name;
                    $contact->company_id = $user->company_id;
                    $contact->create_time = time();
                    $contact->status = 1;
                    $contact->note = "";
                    $contact->create_uid = 0;
                    $contact->save();
                }else{
                    $hasContact->status=1;
                    $hasContact->save();
                }
                return true;
            }else{
                if(Contact::where('phone',$phone)->where('status',0)->where('company_id',$user->company_id)->count()==0)
                {
                    $contact = new Contact();
                    $contact->phone = $phone;
                    $contact->name = $ucenter_user->realname??$ucenter_user->name;
                    $contact->company_id = $user->company_id;
                    $contact->create_time = time();
                    $contact->status = 0;
                    $contact->note = "";
                    $contact->create_uid = 0;
                    $contact->save();
                }
            }
            return false;
        }else{
            return true;
        }
    }

    public function getByUsername($username)
    {
       return $this->where('username', $username)->where('status', 1)->select('id', 'username',  DB::raw('company_id as unitid'))->first();
    }

    private function user_md5($str, $auth_key = null)
    {
        if (!$auth_key) {
            $auth_key = 'PVHnDaiaS!wm>DopYhkMT:Mn^)UK]w#Kc}xr>vh-"z/#MMktgAf_NKx!%XPc*STF';
        }
        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }

    public function avatarImage()
    {
        return $this->hasOne(PublicUpload::class,'id','avatar');
    }
}
