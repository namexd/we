<?php

namespace App\Http\Controllers\Api;

use App\Models\OaSalesReport;
use App\Transformers\OaSalesReportTransformer;
use Illuminate\Http\Request;

class OaSalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param OaSalesReport $oaSalesReport
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,OaSalesReport $oaSalesReport)
    {
        $pagesize=$request->pagesize??$this->pagesize;
        return $this->response->paginator($oaSalesReport->paginate($pagesize),new OaSalesReportTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->only(['user_id',
            'customer_name',
            'customer_address',
            'contact',
            'phone',
            'program_type',
            'program_process',
            'program_goods',
            'program_amount',
            'process_note',
            'need_help',
            'status',]);
        $result=OaSalesReport::create($data);
        if ($request)
        {
            return $this->response->item($result)->setStatusCode(201);
        }else
        {
            return $this->response->errorInternal();
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
        return $this->response->item(OaSalesReport::find($id),new OaSalesReportTransformer());
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
        $data=$request->only(['user_id',
            'customer_name',
            'customer_address',
            'contact',
            'phone',
            'program_type',
            'program_process',
            'program_goods',
            'program_amount',
            'process_note',
            'need_help',
            'status',]);
        $result=OaSalesReport::where('id',$id)->update($data);
        if ($request)
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
