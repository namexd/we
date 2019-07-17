<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ApiLoginLog;
use App\Models\App;
use App\Models\User;
use App\Transformers\UserHidePhoneTransformer;
use Carbon\Carbon;

class ToolsController extends Controller
{
    public function infomation()
    {
        $info['data'][] = [
            "title" => '查看用户注册与激活情况',
            'meta' => [
                "header" => '用户情况',
                "detail_data" => '',
                "detail_template" => '/pages/ucenter/operational/index',
            ]
        ];
        $info['data'][] = [
            "title" => '查看用户登录情况',
            'meta' => [
                "header" => '登录统计',
                "detail_data" => '/api/admin/tools/information/loginlog',
                "detail_template" => 'list'
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
        switch ($slug) {
            case 'loginlog':
                $this->setCrudModel(ApiLoginLog::class);
                $data = ApiLoginLog::where('created_at', '>', Carbon::now()->addDays(-7)->toDateTimeString())->groupBy(\DB::raw("days"))->select(\DB::raw("date_part('day', created_at) as days,count(*) as cnt"))->orderBy('days', 'asc')->get();
                $info = [];
                foreach ($data as $row) {
                    $info['data'][] = [
                        "date" => Carbon::now()->addDay($row->days - Carbon::now()->day )->toDateString(),
                        "login_times" => $row->cnt,
                    ];
                }

                $info["meta"]["columns"] = [
                    [
                        "label" => "日期",
                        "value" => "date"
                    ],
                    [
                        "label" => "登录次数",
                        "value" => "login_times"
                    ]
                ];

                return $info;
                break;

        }
    }
}
