<?php

namespace App\Forms;

use LaravelFormBuilder\Form;

class TopicForm extends Form
{
    public static function filter()
    {
        //input组件
        $items['name'] = Form::input('name', '用户名');
        $items['password'] = Form::password('password', '密码');
        //日期区间选择组件
        $items['date_range'] = Form::dateRange(
            'date_range',
            '区间日期',
            strtotime('- 10 day'),
            time()
        );
        $checkbox_options = [
            ['value' => '1', 'label' => '好用', 'disabled' => false],
            ['value' => '2', 'label' => '方便', 'disabled' => true]
        ];
        $items['check_box'] = Form::checkbox('label', '表单', [])->options($checkbox_options)->col(Form::col(12));

////省市二级联动组件
//        $cityArea = Form::city('address', '收货地址', [
//            '陕西省', '西安市'
//        ]);


//        $tree = Form::treeChecked('tree', '权限', [])->data([
//            Form::treeData(11, 'leaf 1-1-1')->children([Form::treeData(13, '131313'), Form::treeData(14, '141414')]),
//            Form::treeData(12, 'leaf 1-1-2')
//        ])->col(Form::col(12)->xs(12));

//创建form
        $form = Form::create('/save.php', $items);
        $api = $form
            ->setTitle('编辑商品')
            ->formRow(Form::row(10))
            ->formApi();
        return $api;
    }

    //choices 多选
    public function form1()
    {
        //text, email, url, tel, search, password, hidden, number, date, file, image, color, datetime-local, month, range, time, week, select, textarea, button, submit, reset, radio, checkbox, choice, form, entity, collection, repeated, static, button, radio, checkbox, text, email, upload, number, select, textarea, tinymce, tag, choice, form, choice_area, address_picker, choice_ajax, datepicker, folder_chooser, matrix, choice_area_ajax
        $this->add('text_name', 'text', ['label' => '文本--text',]);
        $this->add('password_name', 'password', ['label' => '密码--password',]);
        $this->add('email_name', 'email', ['label' => '邮件',]);
        $this->add('file_name', 'file', ['label' => '文件',]);
        $this->add('checkbox_name', 'checkbox', ['label' => '勾选', 'choices' => ['1' => '男', '2' => '女', '3' => '其他']]);
        $this->add('radio_name', 'radio', ['label' => '单选',
            'choices' => ['monthly' => 'Monthly', 'yearly' => 'Yearly'],
            'selected' => 'monthly',
            'expanded' => true
        ]);
        $this->add('number_name', 'number', ['label' => '数字']);
        $this->add('date_name', 'date', ['label' => '日期']);
        $this->add('time_name', 'time', ['label' => '时间']);
        $this->add('datetime_local_name', 'datetime-local', ['label' => '日期+时间']);
        $this->add('week_name', 'week', ['label' => 'week周']);
        $this->add('month_name', 'month', ['label' => '月份']);
        $this->add('datepicker_name', 'datepicker', ['label' => 'datepicker']);
        $this->add('select_name', 'select', ['label' => '下拉', 'choices' => ['1' => '男', '2' => '女', '3' => '其他']]);
        $this->add('search_name', 'search', ['label' => '搜索框']);
        $this->add('textarea_name', 'textarea', ['label' => '文本区域']);
        $this->add('tag', 'tag', ['label' => 'tag']);
        $this->add('user_id', 'choice_ajax', [
            'action' => 'http://coldchain_we.test/api/test/ajax',
            'validation' => 'required',
            'formatter' => [
                'id' => 'id',
                'libelle' => 'name',
            ],
            'label' => 'ajax下拉'
        ]);
        $this->add('button_name', 'button', ['label' => '普通按钮']);
        $this->add('button_reset_name', 'reset', ['label' => 'reset按钮']);
        $this->add('button_submit_name', 'submit', ['label' => 'submit按钮']);

    }
}
