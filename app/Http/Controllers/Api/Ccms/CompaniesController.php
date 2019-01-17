<?php

namespace App\Http\Controllers\Api\Ccms;
use App\Http\Requests\Api\Ccms\CompanyRequest;
use App\Models\Ccms\Company;
use App\Models\Ccms\WarningEvent;
use App\Models\Ccms\WarningSenderEvent;
use App\Transformers\Ccms\CompanyInfoTransformer;
use App\Transformers\Ccms\CompanyTransformer;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{

    public function index(CompanyRequest $request)
    {
        $this->check();
        $companies = Company::whereIn('id',$this->company_ids)->where('status',1)
            ->orderBy('pid','asc')->orderBy('title','asc')->get();

        return $this->response->collection($companies, new CompanyTransformer());
    }

    public function current($id=null)
    {
        $this->check($id);
//        Cache::forget('companies.'.$this->company->id.'.current');
        $company = Cache::remember('companies.'.$this->company->id.'.current', 10, function () use ($id) {
            return $this->refresh($id);
        });
        return $this->response->item($company, new CompanyInfoTransformer());
    }

    private function refresh($id=null)
    {
        $this->check($id);
        $today = strtotime(date('Y-m-d 00:00:00'));
        $company = Company::where('id',$this->company->id)->first();
        $company->alerms_new =
            WarningEvent::whereIn('company_id',$this->company_ids)->where('handled',0)->count()
            + WarningSenderEvent::whereIn('company_id',$this->company_ids)->where('handled',0)->count();
        $company->alerms_all =
            WarningEvent::whereIn('company_id',$this->company_ids)->count()
            + WarningSenderEvent::whereIn('company_id',$this->company_ids)->count();
        $company->alerms_today =
            WarningEvent::whereIn('company_id',$this->company_ids)->where('warning_event_time','>',$today)->count()
            + WarningSenderEvent::whereIn('company_id',$this->company_ids)->where('sensor_event_time','>',$today)->count();
        $company->save();
        return $company;
    }

    public function tree($id=null)
    {
        $this->check($id);
        $company = Company::whereIn('id',$this->company_ids)->select('id','pid','title','short_title')->get();
        $menus =(new Company())->toTree($company->toArray(),$this->company->id);
        return $this->response->array($menus);
    }

    public function stat_manage($id=null, $month = null)
    {
        $this->check($id);
        if($id == null){
            $id = $this->company->id;
        }
        if($month == null)
        {
            $lat_month =  Carbon::now()->subMonth()->firstOfMonth();
            $year = $lat_month->year;
            $month = $lat_month->month;
        }else{
            $month = explode('-',$month);
            $year = $month[0];
            $month =  $month[1];
        }
//        Cache::forget('stat_manage_'.$id.'_'.$year.'_'.$month);
        $value = Cache::remember('stat_manage_'.$id.'_'.$year.'_'.$month, 60*24*30, function () use ($year,$month) {
            $companies = $this->company->children();
            if(count($companies))
            {
                foreach($companies as $company)
                {
                    $data[] = [
                        'id'=>$company->id,
                        'name'=>$company->title,
                        'value'=>$company->statManageAvg($year,$month)
                    ];
                }
            }else{
                $data[] = [
                    'id'=> $this->company->id,
                    'name'=>$this->company->title,
                    'value'=>$this->company->statManageAvg($year,$month)
                ];
            }
            return $data;
        });
        $data['data'] = $value;
        return $this->response->array($data);

    }
}
