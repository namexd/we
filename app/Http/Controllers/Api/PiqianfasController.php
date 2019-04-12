<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ProductRequest;
use App\Http\Requests\Api\VaccineDetailRequest;
use App\Http\Requests\Api\VaccineMonthRequest;
use App\Http\Requests\Api\VaccineRequest;
use App\Models\Piqianfa;
use App\Models\Vaccine;
use App\Models\VaccineCompany;
use App\Transformers\PiqianfaTransformer;
use App\Transformers\VaccineCompanyTransformer;
use App\Transformers\VaccineTransformer;
use Illuminate\Http\Request;

class PiqianfasController extends Controller
{
    private $piqianfa;

    public function __construct(Piqianfa $piqianfa)
    {
        $this->piqianfa = $piqianfa;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function vaccines()
    {
        $vaccines = Vaccine::all();
        return $this->response->collection($vaccines, new VaccineTransformer());
    }

    public function vaccine_companies(VaccineRequest $request)
    {
        $vaccine_id = $request->get('vaccine_id');
        $ids = $this->piqianfa->where('vaccine_id', $vaccine_id)->groupBy('vaccine_company_id')->pluck('vaccine_company_id');
        $vaccines = VaccineCompany::whereIn('id', $ids)->get();
        return $this->response->collection($vaccines, new VaccineCompanyTransformer());
    }

    public function product(ProductRequest $request)
    {
        $vaccine_id = $request->get('vaccine_id');
        $vaccine_company_id = $request->get('vaccine_company_id');
        $data['data'] = $this->piqianfa->selectRaw('substring_index(chanpinbianma,"/",1) as code,substring_index(chanpinbianma,"/",-1) as product')->where('vaccine_id', $vaccine_id)->where('vaccine_company_id', $vaccine_company_id)->groupBy('chanpinbianma')->get();
        return $this->response->array($data);
    }

    public function list(Request $request)
    {
        $filter = $request->all();
        $result = $this->piqianfa->getList($filter,$this->pagesize);
        return $this->response->array($result);
    }

    public function monthList(VaccineMonthRequest $request)
    {
        $array = [];
        $results = $this->piqianfa->getMonthListByYearAndCode($request->year, $request->code);
        $list['meta']['name'] = $results->first()->name;
        $list['meta']['total'] = array_sum($results->pluck('total')->toArray());
        $list['meta']['year'] = $request->year;
        $list['meta']['code'] = $request->code;
        for ($i = 0; $i < 12; $i++) {
            foreach ($results as $key => $value) {
                $array[$i]['month'] = $i+1;
                $array[$i]['count'] = 0;
                $array[$i]['total'] = 0;
                if ($value->month == $i+1) {
                    $array[$i]['count'] = $value->count;
                    $array[$i]['total'] = $value->total;
                    break;
                }
            }
        }
        $list['data'] = $array;
        return $this->response->array($list);
    }

    public function detail(VaccineDetailRequest $request)
    {
          $result=$this->piqianfa->getDetailByMonth($request->year,$request->month,$request->code)->paginate($this->pagesize);
          return $this->response->paginator($result,new PiqianfaTransformer())
              ->addMeta('product',$result->first()->chanpinbianma)
              ->addMeta('company',$result->first()->vaccine_company->name);
    }
}
