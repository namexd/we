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

    private $topid=[
        'mobile'=>1,
        'web'=>8,
        'web.ccms'=>9,
        'web.bpms'=>0,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'title','types','slug', 'icon','icon_img', 'uri', 'permission'];


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
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_menus', 'menu_id', 'role_id');
    }

    /**
     * @return array
     */
    public function allNodes() : array
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);

        $byOrder = $orderColumn.' = 0,'.$orderColumn;

        return static::with('roles')->orderByRaw($byOrder)->get()->toArray();
    }

    /**
     * determine if enable menu bind permission.
     *
     * @return bool
     */
    public function withPermission()
    {
        return (bool) true;
    }


    public function hasRoles()
    {
        return $this->hasMany(RoleHasMenu::class);
    }

    public function withRoles($role_ids)
    {
        return $this->whereHas('hasRoles', function ($query) use ($role_ids) {
            $query->whereIn('role_id',$role_ids);
        });
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

    public function listTree($user,$is_mobile)
    {
        $menus = $this->withRoles($user->roles->pluck('id'))->where('types', $is_mobile ? 'mobile' : 'web')->get();
        if($is_mobile)
        {
            $pid = $this->topid['mobile'];
        }else{
            $pid = $this->topid['web.ccms'];
        }
        $menus = $this->toTree($menus->toArray(),$pid);
        return $menus;

    }
}
