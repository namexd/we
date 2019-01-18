<?php

namespace App\Http\Requests\Api;


use Dingo\Api\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

}
