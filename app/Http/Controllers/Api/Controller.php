<?php

namespace App\Http\Controllers\Api;

use App\Models\Ccms\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;

    public $user;
    public $company;
    public $company_ids;

    public function check($user)
    {
        if ($user->status == 0) {
            return $this->response->error('账号验证错误', 403)->setStatusCode('403');
        } else {
            $this->user = $user;
            $this->company = Company::where('id', $user->company_id)->first();
            $ids = $this->company ? $this->company->ids() : [];

            if ($user->company_ids != null) {
                $ids_arr = explode(',', $user->company_ids);
                foreach ($ids_arr as $item) {
                    $item_company = Company::where('id', $item)->first();
                    $ids = array_merge($ids, $item_company->ids());
                }
            }
            if ($user->plus_company_id != null) {
                if (is_int($user->plus_company_id)) {
                    $ids = array_merge($ids, [$user->plus_company_id]);
                } else {
                    $ids_arr = explode(',', $user->plus_company_id);
                    foreach ($ids_arr as $item) {
                        $item_company = Company::where('id', $item)->first();
                        $ids = array_merge($ids, $item_company->ids());
                    }
                }
            }
            if (request()->company_id and in_array(request()->company_id, $ids)) {
                $ids = [request()->company_id];
            }
            $this->company_ids = $ids;
        }
    }
}