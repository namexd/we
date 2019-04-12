<?php

namespace App\Models;

use App\Traits\ModelFields;
use DB;
use Illuminate\Database\Eloquent\Model;

class Piqianfa extends Model
{
    use ModelFields;

    public function vaccine_company()
    {
        return $this->belongsTo(VaccineCompany::class, 'vaccine_company_id', 'id');
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function getList($filter = [], $pagesize = 10)
    {
        $result = [];
        $sql = '';
        if ($vaccine_id = array_get($filter, 'vaccine_id')) {
            $sql .= ' and vaccine_id=' . $vaccine_id;
        }
        if ($vaccine_company_id = array_get($filter, 'vaccine_company_id')) {
            $sql .= ' and vaccine_company_id=' . $vaccine_company_id;
        }
        if ($code = array_get($filter, 'code')) {
            $sql .= ' and substring_index(chanpinbianma,"/",1)=' . $code;
        }
        if ($date = array_get($filter, 'date')) {
            $sql .= ' and year(qianfariqi)=' . $date;
        }
        $lists =self::selectRaw(' year(qianfariqi) as "year" ,vaccine_id, group_concat(distinct vaccine_company_id) as company_ids,sum( piqianfashuliang) as total,concat(year( qianfariqi),vaccine_id) as title')
            ->whereRaw('1=1' . $sql)
            ->groupBy(DB::raw('concat(year( qianfariqi),vaccine_id)'))
            ->orderByRaw('year( qianfariqi) desc')
            ->paginate($pagesize);
        foreach ($lists as $key => $list) {
            $list->company = $this->whereRaw('year(qianfariqi)=' . $list->year . ' and vaccine_id=' . $list->vaccine_id . ' and vaccine_company_id in (' . $list->company_ids . ') ' . $sql)->selectRaw('vaccine_company_id,sum(piqianfashuliang) as total')->groupBy('vaccine_company_id')->get();
            $result[$key]['title'] = $list->year . $list->vaccine->name;
            $result[$key]['total'] = $list->total;
            foreach ($list->company as $k => &$value) {
                $result[$key]['company'][$k]['name'] = $value->vaccine_company->name;
                $result[$key]['company'][$k]['total'] = $value->total;
                $result[$key]['company'][$k]['product'] = $this->whereRaw('year(qianfariqi)=' . $list->year . ' and vaccine_id=' . $list->vaccine_id . ' and vaccine_company_id=' . $value->vaccine_company_id . $sql)->selectRaw('substring_index(chanpinbianma,"/",1) as code,substring_index(chanpinbianma,"/",-1) as shortname,sum(piqianfashuliang) as total')->groupBy('chanpinbianma')->get()->toArray();
            }
        }

        $meta= [
            'pagination' => [
                'total' => $lists->total(),
                'count' => $lists->count(),
                'per_page' => $lists->perPage(),
                'current_page' => $lists->currentPage(),
                'total_pages' => ceil($lists->total()/$lists->perPage()),
                'links' => [
                    'previous' => $lists->previousPageUrl(),
                    'next' => $lists->nextPageUrl()
                ]
            ]

        ];
        $array['data']=$result;
        $array['meta']=$meta;
        return $array;
    }

    public function getMonthListByYearAndCode($year, $code)
    {
        return $this->selectRaw('
        substring_index(chanpinbianma,"/",-1) as name, 
        count(1) as count,
        sum(piqianfashuliang) as total,
        month(qianfariqi) as "month"
        ')->whereRaw('year(qianfariqi)=' . $year . ' and substring_index(chanpinbianma,"/",1)=' . $code)
            ->groupBy(DB::raw('month(qianfariqi)'))->get();
    }

    public function getDetailByMonth($year, $month, $code)
    {
        return $this->whereRaw('year(qianfariqi)=' . $year . ' and month(qianfariqi)=' . $month . ' and substring_index(chanpinbianma,"/",1)=' . $code);
    }

    static public function fieldTitles()
    {
        return [
            'dangqixuhao' => '中检网公示序号',
            'pihao' => '批号',
            'piqianfashuliang' => '批签发数量',
            'youxiaoqizhi' => '有效期至',
            'piqianfazhenghao' => '批签发证号',
            'qianfariqi' => '签发日期',
            'qianfajielun' => '批签发结论',
        ];
    }
}
