<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Contact;
use App\Transformers\Ccrp\ContactTransformer;

class ConcatsController extends Controller
{
    public function index()
    {
        $this->check();
        $concats = Contact::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc')->paginate($this->pagesize);

        return $this->response->paginator($concats, new ContactTransformer());
    }

}
