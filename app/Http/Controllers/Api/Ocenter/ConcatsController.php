<?php

namespace App\Http\Controllers\Api\Ocenter;

use App\Models\Ccrp\Contact;
use App\Transformers\Ccrp\ContactTransformer;
use App\Http\Controllers\Api\Controller as BaseController;

class ConcatsController extends  BaseController
{

    public function hasPhone($company_id,$phone)
    {
        $concat = Contact::where('company_id',$company_id)->where('status',1)->where('phone',$phone)->first();
        return $concat?$this->response->item($concat, new ContactTransformer()):$this->response->noContent();

    }

}
