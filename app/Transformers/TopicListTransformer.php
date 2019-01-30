<?php

namespace App\Transformers;

use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class TopicListTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','category'];
    public function transform(Topic $topic)
    {
        $rs = [
            'id' => $topic->id,
            'title' => $topic->title,
            'image' => $topic->image,
            'excerpt' => $topic->excerpt,
            'category_id' => $topic->category_id ,
            'view_count'=>$topic->view_count,
            'reply_count'=>$topic->reply_count,
            'created_at' => $topic->created_at->toDateTimeString(),
            'updated_at' => $topic->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }
    public function includeUser(Topic $topic)
    {
        return $this->item($topic->admin, new AdminTransformer());
    }

    public function includeCategory(Topic $topic)
    {
        return $this->item($topic->category, new TopicCategoryTransformer());
    }
}