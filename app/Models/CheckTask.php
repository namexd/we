<?php

namespace App\Models;

use App\Models\Ccrp\Company;
use function app\Utils\dateFormatByType;
use Illuminate\Database\Eloquent\Model;

class CheckTask extends Model
{

    protected $fillable = [
        'company_id', 'template_id', 'start','end', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function template()
    {
        return $this->belongsTo(CheckTemplate::class, 'template_id');
    }

}
