<?php

namespace App\Http\Controllers\Api;

use App\Forms\TopicForm;
use App\Models\Topic;
use App\Models\TopicCategory;
use App\Transformers\TopicListTransformer;
use function App\Utils\form_fields_trans;
use LaravelFormBuilder\Form;

class TestController extends Controller
{
    public function index()
    {
        $model = Topic::orderBy('id', 'desc');
        $filter = Topic::filter();

        $topics = $model->paginate($request->pagesize ?? $this->pagesize);

        return $this->response->paginator($topics, new TopicListTransformer())->addMeta('filter', $filter);
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
