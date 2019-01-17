<?php

namespace App\Http\Controllers\Api\Ccms;
use App\Http\Requests\Api\Ccms\CompanyRequest;
use App\Models\Ccms\Company;
use App\Models\Ccms\WarningEvent;
use App\Models\Ccms\WarningSenderEvent;
use App\Transformers\Ccms\CompanyInfoTransformer;
use App\Transformers\Ccms\CompanyTransformer;
use Cache;
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

    public function current()
    {
        $this->check();
        $company = Cache::remember('companies.'.$this->user->company_id.'.current', 10, function () {
            return $this->refresh();
        });
        return $this->response->item($company, new CompanyInfoTransformer());
    }

    private function refresh()
    {
        $this->check();
        $today = strtotime(date('Y-m-d 00:00:00'));
        $company = Company::where('id',$this->user->company_id)->first();
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
}
