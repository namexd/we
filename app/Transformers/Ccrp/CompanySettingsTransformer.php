<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanySettingsTransformer extends TransformerAbstract
{
    public function transform(Company $company)
    {
        $info= [
            'id' => $company->id,
            'pid' => $company->pid,
            'title' => $company->title,
            'short' => $company->short_title,
            'address' => $company->address,
            'address_lat' => $company->address_lat,
            'address_lon' => $company->address_lon,
            "category_count" => $company->category_count,
            "category_count_has_cooler" => $company->category_count_has_cooler,
            "cdc_admin" => $company->cdc_admin,
            "shebei_install" => $company->shebei_install,
            "shebei_actived" => $company->shebei_actived,

        ];
        return $info;
    }
}