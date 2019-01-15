<?php

namespace App\Transformers;

use Encore\Admin\Auth\Database\Administrator;
use League\Fractal\TransformerAbstract;

class AdminTransformer extends TransformerAbstract
{
    public function transform(Administrator $admin)
    {
        $rs = [
            'id' => $admin->id,
            'name' => $admin->name,
        ];
        return  $rs;
    }
}