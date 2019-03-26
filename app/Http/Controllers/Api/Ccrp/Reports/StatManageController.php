<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;


use App\Models\Ccrp\Reports\StatMange;
use App\Transformers\Ccrp\Reports\StatManageTransformer;

class StatManageController extends Controller
{
    public function statistic()
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
}
