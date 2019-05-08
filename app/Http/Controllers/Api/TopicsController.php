<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\TopicCategory;
use App\Transformers\TopicCategoryTransformer;
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
        $model = Topic::where('status', 1)->orderBy('created_at', 'desc');
        if ($request->category_id) {
            $model->where('category_id', $request->category_id);
        }
        $topics = $model->paginate($request->pagesize ?? $this->pagesize);
        $filter = Topic::filter();
        $columns = Topic::columns();
        return $this->response->paginator($topics, new TopicListTransformer())
            ->addMeta('filter',$filter)
            ->addMeta('columns',$columns)
            ;
    }

    public function show(Topic $topic)
    {
        $topic->view_count++;
        $topic->save();
        $next = $topic->next();
        $previous = $topic->previous();
        $meta['next'] = $next;
        $meta['previous'] = $previous;
        return $this->response->item($topic, new TopicTransformer())->addMeta('more', $meta);
    }

}
