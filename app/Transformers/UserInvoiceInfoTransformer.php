<?php

namespace App\Transformers;

use App\Models\OaAccountantInvoice;
use App\Models\UserInvoiceInfo;
use League\Fractal\TransformerAbstract;

class UserInvoiceInfoTransformer extends TransformerAbstract
{
    protected $availableIncludes=['user'];
    public function transform(UserInvoiceInfo $userInvoiceInfo)
    {
        $rs = [
            'id' => $userInvoiceInfo->id,
            'user_id' => $userInvoiceInfo->user_id,
            'invoice_type' => $userInvoiceInfo->invoice_type,
            'invoice_type_name' =>UserInvoiceInfo::INVOICETYPE[$userInvoiceInfo->invoice_type],
            'company_name' => $userInvoiceInfo->company_name,
            'tax_number' => $userInvoiceInfo->tax_number,
            'bank_name' => $userInvoiceInfo->bank_name,
            'bank_number' => $userInvoiceInfo->bank_number,
            'telephone' => $userInvoiceInfo->telephone,
            'register_address' => $userInvoiceInfo->register_address,
            'addressee' => $userInvoiceInfo->addressee,
            'mobile' => $userInvoiceInfo->mobile,
            'address' => $userInvoiceInfo->address,
            'created_at' => $userInvoiceInfo->created_at->toDateTimeString(),
            'updated_at' => $userInvoiceInfo->updated_at->toDateTimeString(),
        ];
        return $rs;
    }
}