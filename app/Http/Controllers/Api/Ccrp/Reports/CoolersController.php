<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\Ccrp\Reports\CoolerLog;
use App\Transformers\Ccrp\Reports\CoolerLogTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Illuminate\Support\Facades\Input;


/**
 * 冷链操作日志表
 * Class CoolersController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class CoolersController extends Controller
{
    public function logs(CoolerLog $coolerLog)
    {
        $this->check($this->company_id);
        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        $result=$coolerLog->getListByDate($this->company_ids,$start,$end)->paginate($this->pagesize);
        $transformer=new CoolerLogTransformer();
        return $this->response->paginator($result,$transformer)->addMeta('colums',$transformer->columns());
    }
}
