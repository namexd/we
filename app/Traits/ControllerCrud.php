<?php

namespace App\Traits;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;

trait ControllerCrud
{
    /**
     * 绑定模型
     * @return null
     */
    public function crudModel()
    {
        return null;
    }

    /**
     * @param $function string filter,columns
     * @return bool
     */
    public function withMeta($function)
    {
        $with = request()->get('with');
        if ($with) {
            $withs = explode(',', $with);
            if (in_array($function, $withs)) {
                return true;
            }
        }
        return false;
    }

    public function output($rs)
    {
        if ($this->crudModel() == null) {
            return $rs;
        }
        if ($this->withMeta('filter')) {
            $rs->addMeta('filter', $this->filter());
        }
        if ($this->withMeta('columns')) {
            $rs->addMeta('columns', $this->columns());
        }
        if ($this->withMeta('toolbar')) {
            $rs->addMeta('toolbar', $this->toolbar());
        }
        return $rs;
    }

    /**
     * @param string $function
     * @param string $innerText
     * @param string $type
     * @param string $size
     * @param string $icon
     * @return array
     */
    public function toolBarAddButton($function = "submit", $innerText = null, $type = null, $size = "small", $icon = '')
    {
        switch ($function)
        {
            case 'submit':
                $innerText = $innerText??"提交";
                $type = 'primary';
                break;
            case 'print':
                $innerText = $innerText??"打印";
                $type = 'success';
                break;
            case 'excel':
                $innerText = $innerText??"导出Excel";
                $type = 'danger';
                break;
            case 'pdf':
                $innerText = $innerText??"导出Pdf";
                $type = 'danger';
                break;
            default:
                $innerText = $innerText??"确认";
                $type = 'primary';
        }
        return $button = [
            //自定义
            'function' => $function,
            //按钮类型，可选值为primary、ghost、dashed、text、info、success、warning、error或者不设置
            'type' => $type,
            //按钮大小，可选值为large、small、default或者不设置
            'size' => $size,
            //按钮文字提示
            'innerText' => $innerText,
            //设置按钮的图标类型
            'icon' => $icon,
            //设置按钮为加载中状态
            'loading' => 'false',
            //按钮形状，可选值为circle或者不设置
            'shape' => 'undefined',
            //设置button原生的type，可选值为button、submit、reset
            'htmlType' => "button",
            //开启后，按钮的长度为 100%
            'long' => 'false',
            //设置按钮为禁用状态
            'disabled' => 'false',
        ];
    }

    public function toolbarButtons()
    {
        return [];
    }

    public function filter()
    {
        return $this->crudModel() ? $this->crudModel()::filter() : null;
    }

    public function columns()
    {
        return $this->crudModel() ? $this->crudModel()::columns() : null;
    }

    public function toolbar()
    {
        return [
            'buttons' => $this->toolbarButtons()
        ];
    }
}
