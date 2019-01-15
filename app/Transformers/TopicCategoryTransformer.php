<?php

namespace App\Transformers;

use App\Models\TopicCategory;
use League\Fractal\TransformerAbstract;

class TopicCategoryTransformer extends TransformerAbstract
{
    public function transform(TopicCategory $category)
    {
        $rs = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
        ];
        return  $rs;
    }
}