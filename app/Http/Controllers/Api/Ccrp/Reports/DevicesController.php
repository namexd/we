<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\Ccrp\Company;
use App\Transformers\Ccrp\Reports\DevicesStatisticTransformer;
use App\Models\Ccrp\Cooler;
use App\Transformers\Ccrp\CoolerTransformer;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use App\Models\Ccrp\Reports\StatMange;
use App\Transformers\Ccrp\Reports\StatManageTransformer;
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

    /**
     * note：冷链管理评估表
     * author: xiaodi
     * date: 2019/3/26 13:43
     */
    public function statManage()
    {
        $this->check($this->company_id);
        $date=request()->get('date')??date('Y-m');
        $dateArr=explode('-',$date);
        $year = $dateArr[0];
        $month = $dateArr[1];
        $stat_manages = StatMange::whereIn('company_id', $this->company_ids)
            ->where('year', $year)
            ->where('month', $month)
            ->get();
        $transfer = new StatManageTransformer();
        return $this->response->collection($stat_manages, $transfer)
            ->addMeta('columns', $transfer->columns());
    }

    /**
     * note：冷链设备评估表
     * author: xiaodi
     * date: 2019/3/26 13:59
     */
    public function statCooler()
    {
        $this->check($this->company_id);
        $date = request()->get('date')??date('Y-m', strtotime('-1 Month'));
        $month_first = date('Y-m-01 00:00:00', strtotime($date));
        $month_last = date('Y-m-d H:i:s', strtotime(date('Y-m-01', strtotime($month_first)) . ' +1 month') - 1);;
        $month_start = strtotime($month_first);
        $month_end = strtotime($month_last);
        $stat_manages = (new Cooler())->getListByCompanyIdsAndMonth($this->company_ids,$month_start,$month_end);
        return $this->response->collection($stat_manages,new CoolerTransformer())
            ->addMeta('columns',( new StatCoolerTransformer)->columns());
    }

}
