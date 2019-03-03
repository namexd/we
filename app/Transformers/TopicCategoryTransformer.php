<?php

namespace App\Transformers;

use App\Models\TopicCategory;
use League\Fractal\TransformerAbstract;

class TopicCategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['topics'];

    public function transform(TopicCategory $category)
    {
        $rs = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'image' => $category->image,
            'description' => $category->description,
            'count' => $category->topics()->count()
        ];
        return $rs;
    }
//
//    public function includeTopics(TopicCategory $category)
//    {
//        $topics = $category->lastTopics;
//        return $this->collection($topics, new TopicListTransformer());
//    }
}