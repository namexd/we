<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Transformers\TopicListTransformer;
use App\Transformers\TopicTransformer;
use Illuminate\Http\Request;
class TopicsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Topic::orderBy('id','desc');
        if($request->category_id)
        {
            $model->where('category_id',$request->category_id);
        }
        $topics = $model->paginate($request->pagesize??$this->pagesize);
        return $this->response->paginator($topics, new TopicListTransformer());
    }

    public function show(Topic $topic)
    {
        return $this->response->item($topic, new TopicTransformer());
    }

}
