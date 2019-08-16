<?php

namespace App\Http\Controllers\Api;

use App\Models\OaAccountantInvoice;
use App\Transformers\OaAccountantInvoiceTransformer;
use Illuminate\Http\Request;

class OaAccountantInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param OaAccountantInvoice $oaAccountantInvoice
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,OaAccountantInvoice $oaAccountantInvoice)
    {
        $pagesize=$request->pagesize??$this->pagesize;
        return $this->response->paginator($oaAccountantInvoice->paginate($pagesize),new OaAccountantInvoiceTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $result=OaAccountantInvoice::create($data);
        if ($result)
        {
            return $this->response->created();
        }else
        {
            return $this->response->errorInternal('添加失败');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->response->item(OaAccountantInvoice::find($id),new OaAccountantInvoiceTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data=$request->all();
        $result=OaAccountantInvoice::where('id',$id)->update($data);
        if ($result)
        {
            return $this->response->item($result);
        }else
        {
            return $this->response->errorInternal();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
