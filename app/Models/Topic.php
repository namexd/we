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
        return $value ? config('filesystems.disks.admin.url') . '/' . $value : '';
    }

    // 获取 上一篇 的 ID
    public function next()
    {
        $next_id = self::where('id', '>', $this->id)->where('category_id', $this->category_id)->where('status',1)->min('id');
        return $next_id ? self::find($next_id, ['id', 'title']) : null;
    }

    // 同理，获取 下一篇 的 ID
    public function previous()
    {
        $previous_id = self::where('id', '<', $this->id)->where('category_id', $this->category_id)->where('status',1)->max('id');
        return $previous_id ? self::find($previous_id, ['id', 'title']) : null;
    }


}
