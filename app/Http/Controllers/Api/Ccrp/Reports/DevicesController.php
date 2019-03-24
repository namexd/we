<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\Ccrp\Company;
use App\Transformers\Ccrp\Reports\DevicesStatisticTransformer;

/**
 * 设备报表
 * Class DevicesController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class DevicesController extends Controller
{
    /**
     * 冷链设备一览表
     * @return \Dingo\Api\Http\Response
     */
    public function statistic()
    {
        $this->check($this->company_id);//支持URL中的?company_id=xxx的子单位的查询
        $companies = Company::whereIn('id', $this->company_ids)
            ->where('status', 1) //状态开启
            ->where('cdc_admin', 0) //使用的单位，非管理单位
            ->get();
        $transfer = new DevicesStatisticTransformer();
        return $this->response->collection($companies, $transfer)
            ->addMeta('columns',$transfer->columns());
    }

}
