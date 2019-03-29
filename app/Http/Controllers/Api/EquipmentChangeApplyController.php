<?php

namespace App\Http\Controllers\Api;

use App\Models\EquipmentChangeApply;
use App\Transformers\EquipmentChangeApplyTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EquipmentChangeApplyController extends Controller
{
    protected $model;

    public function __construct(EquipmentChangeApply $equipmentChangeApply)
    {
        $this->model = $equipmentChangeApply;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $this->model->with(['detail', 'new'])->paginate($request->pagesize??$this->pagesize);
        return $this->response->paginator($data, new EquipmentChangeApplyTransformer());
    }

    public function getChangeType()
    {
        return $this->response->array(['data' => $this->model->getChangeType()]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = $this->model->add($request->all());
        if ($result instanceof Model)
            return $this->response->item($result,new EquipmentChangeApplyTransformer())->statusCode(201);
        else
            $this->response->errorInternal($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $equipment_change_apply = $this->model->findOrFail($id);
        return $this->response->item($equipment_change_apply, new EquipmentChangeApplyTransformer());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
