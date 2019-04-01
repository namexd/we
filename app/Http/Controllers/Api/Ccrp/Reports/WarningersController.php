<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\Ccrp\Warninger;
use App\Transformers\Ccrp\CompanyTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Illuminate\Support\Facades\Input;


/**
 * 预警历史统计表
 * Class WarningersController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class WarningersController extends Controller
{
    public function statistics(Warninger $warninger)
    {
        $type=Input::get('type')??1;
        $todayTime= mktime(0,0,0,date('m'),date('d'),date('Y'))+24*3600-1;
        if(Input::get('start'))
            $start = strtotime(str_replace('+',' ',Input::get('start')));
        else
            $start=mktime(0,0,0,date('m'),'1',date('Y'));//1号

        if(Input::get('end'))
            $end = strtotime(str_replace('+',' ',Input::get('end')));
        else
            $end=$todayTime;

        $this->check($this->company_id);
        $result =$warninger->getHistoryList($this->company_ids,$start,$end,$type)->paginate($this->pagesize);
        return $this->response->paginator($result, new CompanyTransformer())->addMeta('columns',$warninger->colums($type));
    }
}
