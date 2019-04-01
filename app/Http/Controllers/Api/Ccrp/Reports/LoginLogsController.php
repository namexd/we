<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\Ccrp\Reports\LoginLog;
use App\Transformers\Ccrp\Reports\LoginLogTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Illuminate\Support\Facades\Input;


/**
 * 登录日志统计表
 * Class LoginLogsController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class LoginLogsController extends Controller
{
    public function statistics(LoginLog $loginLog)
    {
        $this->check($this->company_id);
        $date =Input::get('date');
        $login_logs['data']= $loginLog->getReportByMonth($this->company_ids,$date);
        return $this->response->array($login_logs);
    }

    public function list(LoginLog $loginLog)
    {
        $this->check($this->company_id);
        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        $lists=$loginLog->getDetailByDate($this->company_ids,$start,$end)->paginate($this->pagesize);
        $transformer=new LoginLogTransformer();
        return $this->response->paginator($lists,$transformer)->addMeta('colums',$transformer->columns());
    }
}
