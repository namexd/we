<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OaAccountantInvoice extends Model
{
    const INVOICETYPE=[
        1=>'普票',
        2=>'专票',
    ];
    const PRODUCTORSERVICE=[
        1=>'冷库验证服务',
        2=>'平台服务',
        3=>'生物制品',
        4=>'探头校准',
        5=>'无线温湿度探头',
        6=>'验证服务',
        7=>'作废',
    ];
    protected $fillable=[
        'type','date','company_name','number','invoice_number','invoice_amount','tax_rate',
        'tax_amount','price_tax_count','product','count','tax_price','express_number','manager',
        'collect_date','collect_amount','primary'
    ];

}
