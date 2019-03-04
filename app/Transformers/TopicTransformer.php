<?php

namespace App\Transformers;

use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class TopicTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','category'];
    public function transform(Topic $topic)
    {
        $rs = [
            'id' => $topic->id,
            'title' => $topic->title,
            'author' => $topic->author,
            'image' => $topic->image,
            'excerpt' => $topic->excerpt,
            'content' => $topic->content ,
            'category_id' => $topic->category_id ,
            'category'=>$topic->category->name,
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