<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OaSalesReport extends Model
{
    const PROGRAM_PROCESSES = [
        '方案',
        '合同',
        '实施',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_address',
        'contact',
        'phone',
        'program_type',
        'program_process',
        'program_goods',
        'program_amount',
        'process_note',
        'need_help',
        'status',
    ];


    public function programtypes() :BelongsToMany
    {
        return $this->belongsToMany(GoodsType::class,'oa_sales_report_has_goods_types','oa_sales_report_id','goods_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
