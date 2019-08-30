<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserInvoiceInfoRequest;
use App\Models\OaAccountantInvoice;
use App\Models\UserInvoiceInfo;
use App\Transformers\OaAccountantInvoiceTransformer;
use App\Transformers\UserInvoiceInfoTransformer;
use Illuminate\Http\Request;

class UserInvoiceInfoController extends Controller
{
    protected $model;

    public function __construct(UserInvoiceInfo $userInvoiceInfo)
    {
        $this->model = $userInvoiceInfo;
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
        $infos = $this->model->where('user_id', $this->user()->id);
        if ($request->has('invoice_type'))
        {
            $infos=$infos->where('invoice_type',$request->invoice_type);
        }
        return $this->response->collection($infos->orderBy('id','desc')->get(), new UserInvoiceInfoTransformer());
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
        $data['user_id'] = $this->user()->id;
        $result = $this->model->create($data);
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
        return $this->response->item($this->model->findOrFail($id), new UserInvoiceInfoTransformer())
            ->addMeta('invoice_type', $this->model->getInvoiceType());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $result = $this->model->where('id', $id)->update($data);
        if ($request) {
            return $this->response->item($result);
        } else {
            return $this->response->errorInternal();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getInvoiceType()
    {
        return $this->response->array($this->model->getInvoiceType());
    }
}
