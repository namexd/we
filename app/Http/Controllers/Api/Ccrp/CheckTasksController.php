<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\CheckTask;
use App\Models\CheckTaskResult;
use App\Transformers\Ccrp\CheckTaskTransformer;
use Illuminate\Http\Request;

class CheckTasksController extends Controller
{
    protected $model;

    public function __construct(CheckTask $checkTask)
    {
        parent::__construct();
        $this->model = $checkTask;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->check();
        $company_ids = $this->company_ids;
        $data = $this->model->whereIn('company_id', $company_ids)->orderBy('id', 'desc')->paginate($request->pagesize ?? $this->pagesize);
        return $this->response->paginator($data, new CheckTaskTransformer());
    }
}
