<?php

namespace App\Transformers;

use App\Models\Piqianfa;
use League\Fractal\TransformerAbstract;

class PiqianfaTransformer extends TransformerAbstract
{
    protected $availableIncludes=['vaccine','vaccine_company'];
    private $colums=[
        'dangqixuhao',
        'pihao' ,
        'piqianfashuliang' ,
        'youxiaoqizhi',
        'piqianfazhenghao',
        'qianfariqi',
        'qianfajielun',
    ];
    public function colums()
    {
        return Piqianfa::getFieldsTitles($this->colums);
    }
    public function transform(Piqianfa $piqianfa)
    {
        $rs=[];
        foreach ($this->colums as $colum)
        {
            $rs[$colum]=$piqianfa->{$colum}??'';
        }
        return  $rs;
    }

    public function includeVaccine(Piqianfa $piqianfa)
    {
        return $this->item($piqianfa->vaccine,new VaccineTransformer());
    }

    public function includeVaccineCompany(Piqianfa $piqianfa)
    {
        return $this->item($piqianfa->vaccine_company,new VaccineCompanyTransformer());
    }
}