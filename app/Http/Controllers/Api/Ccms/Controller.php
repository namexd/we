<?php

namespace App\Http\Controllers\Api\Ccms;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\User;
use App\Models\UserHasApp;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{

    public $appid = 1;
    use Helpers;

    public $user;
    public $company;
    public $company_ids;

    public function __construct()
    {
    }

    public function check($company_id=null)
    {
        $ucenter_user = $this->user();
        $user_app = UserHasApp::where('user_id',$ucenter_user->id)->first();

        if($user_app ==null)
        {
            return $this->response->error('系统账号绑定错误', 403);
        }
        $user = User::where('id',$user_app->app_userid)->first();

        if ($user->status == 0) {
            return $this->response->error('系统账号验证错误', 403);
        } else {

            if($company_id == null)
            {
                $company_id = $user->company_id;
            }else{
                $user_company = $user->user_company;
                $user_company_ids = $user_company->ids();
                if(!in_array($company_id,$user_company_ids))
                {
                    $company_id = $user->company_id;
                }
            }


            $this->user = $user;
            $this->company = Company::where('id', $company_id)->first();

            $ids = $this->company ? $this->company->ids() : [];
            $this->company_ids = $ids;
        }
    }
}
