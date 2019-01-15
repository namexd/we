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


    public function HasRoles()
    {
        return $this->hasMany(RoleHasMenu::class);
    }

    public function WithRoles($role_ids)
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
}
