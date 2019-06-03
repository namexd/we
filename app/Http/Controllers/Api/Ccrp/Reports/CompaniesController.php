<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Transformers\Ccrp\CompanySettingsTransformer;
use App\Transformers\Ccrp\CompanyTransformer;
use function App\Utils\hidePhone;
use Route;

class CompaniesController extends Controller
{
    public function infomation()
    {
        $info['data'][] = [
            "title" => '单位名称、地址、负责人、电话等信息',
            'meta' => [
                "header" => '单位信息',
                "detail" => '/api/ccrp/reports/companies/infomation/company',
            ]
        ];
        $info['data'][] = [
            "title" => '一、二、三级超温预警短信接收人员',
            'meta' => [
                "header" => '预警通道设置',
                "detail" => '/api/ccrp/reports/companies/infomation/company',
            ]
        ];
        $info['data'][] = [
            "title" => '可绑定小程序进入系统的人员',
            'meta' => [
                "header" => '预警联系人',
                "detail" => '/api/ccrp/reports/companies/infomation/company',
            ]
        ];
        $info['data'][] = [
            "title" => '查看本单位已经绑定的人员',
            'meta' => [
                "header" => '小程序绑定人员',
                "detail" => '/api/ccrp/reports/companies/infomation/company',
            ]
        ];
        $info["meta"]["columns"] = [
            [
                "label" => "",
                "value" => "title"
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

        }
    }
}
