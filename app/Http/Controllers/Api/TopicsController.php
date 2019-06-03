<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\TopicCategory;
use App\Traits\ControllerCrud;
use App\Transformers\TopicCategoryTransformer;
use App\Transformers\TopicListTransformer;
use App\Transformers\TopicTransformer;
use Illuminate\Http\Request;

class TopicsController extends Controller
{

    public function crudModel()
    {
        $this->setCrudModel(Topic::class);
    }

    public function toolbarButtons()
    {
        $btns[] = $this->toolbarAddButton('add');
        $btns[] = $this->toolbarAddButton('print');
        $btns[] = $this->toolbarAddButton('excel','http://139.196.212.133:81/Download/201905/00000388/%E7%BD%97%E6%B3%BE%E7%A4%BE%E5%8C%BA%E5%8D%AB%E7%94%9F%E6%9C%8D%E5%8A%A1%E4%B8%AD%E5%BF%83_%E7%BD%97%E6%B3%BE1%E5%86%B0%E7%AE%B13101131701-05-0004_2019%E5%B9%B404%E6%9C%88%E6%95%B0%E6%8D%AE%E4%B8%80%E8%A7%88%E8%A1%A8_20190506152636279.xls');
        $btns[] = $this->toolbarAddButton('pdf');
        return $btns;
    }

    public function tableSortable()
    {
        return ['view_count'];
    }
    public function tableButtons()
    {
        $btns[] = $this->toolbarAddButton('edit');
        $btns[] = $this->toolbarAddButton('link');
        return $btns;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = Topic::where('status', 1)->orderBy('created_at', 'desc');
        if ($request->category_id) {
            $model->where('category_id', $request->category_id);
        }
        $topics = $model->paginate($request->pagesize ?? $this->pagesize);
        $return = $this->response->paginator($topics, new TopicListTransformer());
        return $this->display($return);

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

    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'content'=>'required',
        ]);
        $data=$request->all();
        $data['status']=0;
        Topic::create($data);
        return $this->response->created();
    }

    public function testStore(Request $request)
    {
        $this->validate($request,[
            'title'=>'required',
            'content'=>'required',
        ]);
        if('lengwang'==$request->access)
        {
            $data=$request->except('access');
            $data['status']=$request->has('status')?$request->status:0;
            Topic::create($data);
            return $this->response->created();
        }else
        {
            return $this->response->errorUnauthorized('认证错误');
        }

    }
    public function category()
    {
        $categories = TopicCategory::where('status', 1)->get();
        return $this->response->collection($categories, new TopicCategoryTransformer());
    }
}
