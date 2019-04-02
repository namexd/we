<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Requests\Api\Ccrp\Report\DateRangeRequest;
use App\Models\Ccrp\Reports\CoolerLog;
use App\Transformers\Ccrp\Reports\CoolerLogTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Input;


/**
 * 冷链操作日志表
 * Class CoolersController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class CoolersController extends Controller
{

    /**
     * note：冷链操作日志表
     * author: xiaodi
     * date: 2019/3/26 15:43
     * @param DateRangeRequest $request
     * @param CoolerLog $coolerLog
     * @return Response
     */
    public function logs(DateRangeRequest $request,CoolerLog $coolerLog)
    {
        $this->check($this->company_id);
        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        $result=$coolerLog->getListByDate($this->company_ids,$start,$end)->paginate($this->pagesize);
        $transformer=new CoolerLogTransformer();
        return $this->response->paginator($result,$transformer)->addMeta('colums',$transformer->columns());
    }
}