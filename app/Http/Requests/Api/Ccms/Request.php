<?php

namespace App\Http\Requests\Api\Ccms;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

}
