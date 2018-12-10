<?php

namespace App\Http\Controllers\Api;

use App\Models\Ccms\Contact;
use App\Transformers\ContactTransformer;

class ConcatsController extends Controller
{
    public function index()
    {
        $this->check($this->user());
        $concats = Contact::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc')->get();

        return $this->response->collection($concats, new ContactTransformer());
    }

}
