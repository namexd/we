<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvoiceInfo extends Model
{
    protected $fillable=[
        'id' ,
        'user_id' ,
        'invoice_type' ,
        'company_name' ,
        'tax_number' ,
        'bank_name' ,
        'bank_number' ,
        'telephone' ,
        'register_address' ,
        'addressee' ,
        'mobile' ,
        'address' ,
    ];

    const INVOICETYPE=[
        1=>'电子发票（普通发票）',
        2=>'纸质发票（普通发票）',
        3=>'纸质发票（专用发票）',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getInvoiceType()
    {
        $result=[];
        foreach (self::INVOICETYPE as $key=> $value)
        {
            $result[]=[
                'value'=>$key,
                'label'=>$value,
            ];
        }
        return $result;
    }
}
