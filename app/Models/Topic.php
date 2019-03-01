<?php

namespace App\Models;

use App\Traits\ModelFields;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use LaravelFormBuilder\Form;

class Topic extends Model
{
    use ModelFields;
    protected $fillable = ['title', 'body', 'user_id', 'category_id', 'reply_count', 'view_count', 'last_reply_user_id', 'order', 'excerpt', 'slug', 'status'];

    protected static function filterFields()
    {
        return [
            'title' => Form::TEXT,
            'name' => ['text', 'options' => ['label' => 'xxx']],
            'content' => ['text', 'options' => []],
        ];
    }

    protected static function fieldTitles()
    {
        return [
            'title' => '测试标题',
        ];
    }

    public function category()
    {
        return $this->belongsTo(TopicCategory::class);
    }

    public function admin()
    {
        return $this->belongsTo(Administrator::class, 'user_id');
    }

    public function replayUser()
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    public function getImageAttribute($value)
    {
        return $value?config('filesystems.disks.admin.url') . '/' . $value:'';
    }
}
