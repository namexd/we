<?php

namespace App\Transformers;

use App\Models\OaAccountantInvoice;
use League\Fractal\TransformerAbstract;

class OaAccountantInvoiceTransformer extends TransformerAbstract
{
    public function transform(OaAccountantInvoice $oaAccountantInvoice )
    {
        $rs = [
            'id' => $oaAccountantInvoice->id,
            'type' => $oaAccountantInvoice::INVOICETYPE[$oaAccountantInvoice->type],
            'company_name' => $oaAccountantInvoice->company_name,
            'number' => $oaAccountantInvoice->number,
            'invoice_number' => $oaAccountantInvoice->invoice_number,
            'invoice_amount' => $oaAccountantInvoice->invoice_amount,
            'tax_rate' => $oaAccountantInvoice->tax_rate,
            'tax_amount' => $oaAccountantInvoice->tax_amount,
            'price_tax_count' => $oaAccountantInvoice->price_tax_count,
            'product' => $oaAccountantInvoice::PRODUCTORSERVICE[$oaAccountantInvoice->product],
            'count' => $oaAccountantInvoice->count,
            'tax_price' => $oaAccountantInvoice->tax_price,
            'express_number' => $oaAccountantInvoice->express_number,
            'manager' => $oaAccountantInvoice->manager,
            'collect_date' => $oaAccountantInvoice->collect_date,
            'collect_amount' => $oaAccountantInvoice->collect_amount,
            'primary' => $oaAccountantInvoice->primary,
            'created_at' => $oaAccountantInvoice->created_at->toDateTimeString(),
            'updated_at' => $oaAccountantInvoice->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }
}