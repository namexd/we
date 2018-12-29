<?php

namespace App\Transformers;

use App\Models\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    public function transform(Menu $menu)
    {
        return [
            'id' => $menu->id,
            'parent_id' => $menu->parent_id,
            'title' => $menu->title,
            'slug' => $menu->slug,
            'icon' => $menu->icon,
            'icon_img' => $menu->icon_img,
            'uri' => $menu->uri,
        ];
    }
}