<?php


namespace App\Models\Ccrp;

use App\Models\UserHasApp;

class UserLoginLog extends Coldchain2Model
{
    protected $table = 'user_login_log';
    public static function addCcrpLoginLog($user)
    {
        $apps = $user->apps;
        if ($apps) {
            foreach ($apps as $app) {
                switch ($app->slug) {
                    case  'ccrp':
                        $user_app = UserHasApp::where('user_id', $user->id)->where('app_id', $app->id)->first();
                        if ($user_app) {
                            $ccrp_user = User::where('id', $user_app->app_userid)->where('status', 1)->first();

                            self::addLog($ccrp_user->id, $ccrp_user->username ,$ccrp_user->userlevel ,$ccrp_user->company_id,5,time(), request()->ip());
                            self::addLogPg($ccrp_user->id, $ccrp_user->username ,$ccrp_user->userlevel ,$ccrp_user->company_id,5,time(), request()->ip());
                        }
                        break;
                }
            }
        }

    }
    private static function addLog($uid,$username,$userlevel,$company_id,$type,$login_time,$ip)
    {
        $log = new self;
        $log->uid = $uid;
        $log->username = $username;
        $log->userlevel = $userlevel;
        $log->company_id = $company_id;
        $log->type = $type;
        $log->login_time = $login_time;
        $log->ip = $ip;
        $log->save();
    }
    private static function addLogPg($uid,$username,$userlevel,$company_id,$type,$login_time,$ip)
    {
        $log = new UserLoginLogPg();
        $log->uid = $uid;
        $log->username = $username;
        $log->userlevel = $userlevel;
        $log->company_id = $company_id;
        $log->type = $type;
        $log->login_time = $login_time;
        $log->ip = $ip;
        $log->save();
    }

}
