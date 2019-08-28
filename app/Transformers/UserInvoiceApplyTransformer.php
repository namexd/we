<?php

namespace App\Transformers;

use App\Models\OaAccountantInvoice;
use App\Models\UserInvoiceApply;
use App\Models\UserInvoiceInfo;
use League\Fractal\TransformerAbstract;

class UserInvoiceApplyTransformer extends TransformerAbstract
{
    protected $availableIncludes=['user'];
    public function transform(UserInvoiceApply $userInvoiceApply)
    {
        $rs = [
            'id' => $userInvoiceApply->id,
            'user_id' => $userInvoiceApply->user_id,
            'info' => json_decode($userInvoiceApply->info,true),
            'status' => $userInvoiceApply->status,
            'status_name' => UserInvoiceApply::STATUS[$userInvoiceApply->status],
            'created_at' => $userInvoiceApply->created_at->toDateTimeString(),
            'updated_at' => $userInvoiceApply->updated_at->toDateTimeString(),
        ];
        return $rs;
    }
}