<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserInvoiceInfoRequest;
use App\Models\OaAccountantInvoice;
use App\Models\UserInvoiceApply;
use App\Transformers\UserInvoiceApplyTransformer;
use Illuminate\Http\Request;

class UserInvoiceApplyController extends Controller
{
    protected $model;

    public function __construct(UserInvoiceApply $apply)
    {
        $this->model = $apply;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param OaAccountantInvoice $oaAccountantInvoice
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $applies = $this->model->where('user_id', $this->user()->id);
        if ($request->has('status'))
        {
            $applies=$applies->where('status',$request->status);
        }
        return $this->response->paginator($applies->orderBy('id','desc')->paginate($request->get('pagesize',$this->pagesize)), new UserInvoiceApplyTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserInvoiceInfoRequest $request)
    {
        $data = $request->all();
        $attribute['user_id'] = $this->user()->id;
        $attribute['info']=json_encode($data,JSON_UNESCAPED_UNICODE);
        $result = $this->model->create($attribute);
        if ($result) {
            return $this->response->created();
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->response->item($this->model->findOrFail($id), new UserInvoiceApplyTransformer());
    }
    public function getStatus()
    {
        return $this->response->array($this->model->getStatus());
    }

}
