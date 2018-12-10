<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\CompanyRequest;
use App\Models\Ccms\Company;
use App\Transformers\CompanyInfoTransformer;
use App\Transformers\CompanyTransformer;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function index(CompanyRequest $request)
    {
        $this->check($this->user());
        $companies = Company::whereIn('id',$this->company_ids)->where('status',1)
            ->orderBy('pid','asc')->orderBy('title','asc')->get();
        return $this->response->collection($companies, new CompanyTransformer());
    }

    public function current()
    {
        $this->check($this->user());
        $company = Company::where('id',$this->user->company_id)->first();
        return $this->response->item($company, new CompanyInfoTransformer());

    }
}
