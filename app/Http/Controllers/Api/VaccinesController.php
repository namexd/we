<?php

namespace App\Http\Controllers\Api;

use App\Models\Vaccine;
use Illuminate\Http\Request;

class VaccinesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vaccines=Vaccine::all();
        return $this->response->collection($vaccines, new TopicListTransformer());
    }

}
