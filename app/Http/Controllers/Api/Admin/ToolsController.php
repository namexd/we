<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\App;
use App\Models\User;
use App\Transformers\UserHidePhoneTransformer;

class ToolsController extends Controller
{
    public function infomation()
    {
        $info['data'][] = [
            "title" => '查看用户注册与激活情况',
            'meta' => [
                "header" => '用户情况',
                "detail_data" => '/pages/ucenter/operational/index',
            ]
        ];
        return $this->response->array($info);
    }

    public function infomationDetail($slug)
    {
        $this->check();
        switch ($slug) {
            case 'company':
                $this->setCrudModel(Company::class);
                $return = $this->response->item($this->company, new CompanySettingsTransformer());
                return $this->display($return,'columns');
                break;
            case 'warningers':
                $this->setCrudModel(Warninger::class);
                $warningers = Warninger::where('company_id',$this->company->id)->get();
                return $this->display($this->response->collection($warningers,new WarningerTransformer()),'columns');
            case 'concats':
                $this->setCrudModel(Contact::class);
                $users =Contact::where('company_id', $this->company->id)->get();
                return $this->display($this->response->collection($users,new ContactTransformer()),'columns');
                break;
            case 'users':
                $this->setCrudModel(User::class);
                $app = App::where('slug',App::冷链监测系统)->first();
                $users =User::whereIn('id', $app->hasUser->where('app_id',$app->id)->where('app_unitid',$this->company->id)->pluck('user_id'))->get();
                return $this->display($this->response->collection($users,new UserHidePhoneTransformer()),'columns');
                break;

        }
    }
}
