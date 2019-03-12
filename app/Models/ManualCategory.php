<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class ManualCategory extends Model
{
    use ModelTree, AdminBuilder;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setOrderColumn('sort');
    }

    protected $fillable = [
        'parent_id', 'sort', 'title', 'status', 'manual_slug'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_manual_categories', 'manual_category_id', 'role_id');
    }


    public function hasRoles()
    {
        return $this->hasMany(RoleHasManualCategory::class);
    }

    public function withRoles($role_ids)
    {
        $rs = $this->whereHas('hasRoles', function ($query) use ($role_ids) {
            $query->whereIn('role_id', $role_ids);
        });
        return $rs;
    }

    public function listTree($user, $slug)
    {
        $categories = $this->withRoles($user->roles->pluck('id'))->where('manual_slug', $slug)->get();
        $categories = $this->generateTree($categories->toArray());
        return $categories;

    }

    /**
     * note：书写一个调用无线分类的方法(引用)
     * author: xiaodi
     * date: 2019/3/12 13:50
     * @param $array
     * @return array
     */
    function generateTree($array)
    {
        //第一步 构造数据
        $items = array();
        foreach ($array as $value) {
            $items[$value['id']] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = array();
        foreach ($items as $key => $item) {
            if (isset($items[$item['parent_id']])) {
                $items[$item['parent_id']]['son'][] = &$items[$key];
            } else {
                $tree[] = &$items[$key];
            }
        }
        return $tree;
    }

}
