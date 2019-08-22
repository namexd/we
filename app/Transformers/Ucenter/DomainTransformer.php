<?php

namespace App\Transformers\Ucenter;

use App\Models\Ucenter\Domain;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class DomainTransformer extends TransformerAbstract
{
    protected $availableIncludes=['config'];
    public function transform(Domain $domain)
    {
        $rs= [
            'id' => $domain->id,
            'domain' => $domain->domain,
            'name' => $domain->name,
            'slug' => $domain->slug,
            'description' => $domain->description ,
            'created_at' => $domain->created_at->toDateTimeString(),
            'updated_at' => $domain->updated_at->toDateTimeString(),
        ];
        return $rs;
    }

    public function includeConfig(Domain $domain)
    {
        $arrays=$domain->config()->get()->pluck('pivot.value','slug')->toArray();
        return new Item($arrays,function ($array){
            return $array;
        });
    }
}