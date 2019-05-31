<?php

namespace App\Http\Controllers\Api;


use App\Models\Topic;
use App\Traits\ControllerCrud;
use App\Transformers\TopicListTransformer;
use App\Transformers\TopicTransformer;
use function App\Utils\form_fields_trans;
use Request;

class TestController extends Controller
{
    use ControllerCrud;
    public function crudModel()
    {
        return Topic::class;
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

    public function list(Request $request)
    {
        $model = Topic::where('status', 1)->orderBy('created_at', 'desc');
        $topics = $model->paginate($request->pagesize ?? $this->pagesize);
        $return = $this->response->paginator($topics, new TopicListTransformer());
        return $this->display($return);
    }

    public function detail(Topic $topic)
    {
        $next = $topic->next();
        $previous = $topic->previous();
        $meta['next'] = $next;
        $meta['previous'] = $previous;
        $return = $this->response->item($topic, new TopicTransformer());
        return $this->display($return);
    }
    public function index(Request $request)
    {
        $model = Topic::where('status', 1)->orderBy('created_at', 'desc');

        $topics = $model->paginate($request->pagesize ?? $this->pagesize);
        $return = $this->response->paginator($topics, new TopicListTransformer());
        return $this->display($return);
    }

    public function form2(FormBuilder $formBuilder)
    {

        $model = Topic::orderBy('id', 'desc');
        $topics = $model->paginate($request->pagesize ?? $this->pagesize);

        $form = $formBuilder->create('App\Forms\TopicForm');
        $form_fields = $form->getFields();
        $fields = form_fields_trans($form_fields);
        return $this->response->paginator($topics, new TopicListTransformer())->addMeta('filter', $fields);

    }

    public function formPreview(FormBuilder $formBuilder)
    {


        $model = Topic::orderBy('id', 'desc');
        $topics = $model->paginate($request->pagesize ?? $this->pagesize);

        $form = $formBuilder->create('App\Forms\TopicForm');
        echo '
<!DOCTYPE html><html lang=zh-CN><head><meta charset=utf-8><meta http-equiv=X-UA-Compatible content="IE=edge"><meta name=viewport content="width=device-width,initial-scale=1"></head><body style="padding:40px;">';
        echo '<script src="https://cdn.bootcss.com/jquery/3.1.1/jquery.js"></script>';
        echo '<link href="https://cdn.bootcss.com/datepicker/0.6.5/datepicker.css" rel="stylesheet">';
        echo '<script src="https://cdn.bootcss.com/datepicker/0.6.5/datepicker.js"></script>';
        echo '<link href="https://cdn.bootcss.com/select2/2.1.0/select2.css" rel="stylesheet">';
        echo '<script src="https://cdn.bootcss.com/select2/2.1.0/select2.js"></script>';
        echo $form->renderForm();
        echo '</body></html>';

    }

    public function ajax()
    {

        return [
            ['id' => 1, 'name' => '标题1'],
            ['id' => 2, 'name' => '标题2'],
            ['id' => 3, 'name' => '标题3'],
            ['id' => 4, 'name' => '标题4'],
            ['id' => 5, 'name' => '标题5'],
        ];


    }
}
