<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use App\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class Menu extends Model
{
    use AdminBuilder, ModelTree {
        ModelTree::boot as treeBoot;
    }

    const 移动端 = 1;
    const 网页端 = 8;
    const 网页端冷链监测 = 9;
    const 网页端生物制品 = 0;
    const 网页端办公系统 = 28;
    const 用户中心 = 30;
    const SYTEMS = [
        'web_oa' => self::网页端办公系统,
        'web_ccrp' => self::网页端冷链监测,
        'web_bpms' => self::网页端生物制品,
        'mobile' => self::移动端,
        'ucenter' => self::用户中心,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'title', 'types', 'slug', 'icon', 'icon_img', 'uri', 'permission'];


    public function getTypesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTypesAttribute($value)
    {
        $this->attributes['types'] = implode(',', $value);
    }

    /**
     * A Menu belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_menus', 'menu_id', 'role_id');
    }

    /**
     * @return array
     */
    public function allNodes(): array
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);

        $byOrder = $orderColumn . ' = 0,' . $orderColumn;

        return static::with('roles')->orderByRaw($byOrder)->get()->toArray();
    }

    /**
     * determine if enable menu bind permission.
     *
     * @return bool
     */
    public function withPermission()
    {
        return (bool)true;
    }


    public function hasRoles()
    {
        return $this->hasMany(RoleHasMenu::class);
    }

    public function withRoles($role_ids)
    {
        $rs = $this->whereHas('hasRoles', function ($query) use ($role_ids) {
            $query->whereIn('role_id', $role_ids);
        });
        return $rs;
    }


    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();

        static::deleting(function ($model) {
            $model->roles()->detach();
        });
    }

    public function listTree($user, $is_mobile, $topid = null)
    {
        $menus = $this->withRoles($user->roles->pluck('id'))->where('types', $is_mobile ? 'mobile' : 'web')->orderBy('order','asc')->get();
        if(in_array(Role::测试用户,$user->roles->pluck('slug')->toArray()))
        {
            foreach ($menus as &$menu)
            {
                $menu->uri = '';
            }
        }
        if ($topid) {
            $pid = $topid;
        } else {
            if ($is_mobile) {
                $pid = self::移动端;
            } else {
                $pid = self::网页端冷链监测;
            }
        }
        $menus = $this->toTree($menus->toArray(), $pid);
        return $menus;

    }
}
