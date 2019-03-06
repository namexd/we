<?php

namespace App\Transformers;

use App\Models\App;
use League\Fractal\TransformerAbstract;

class AppTransformer extends TransformerAbstract
{
    public function transform(App $app)
    {
        return [
            'id' => $app->id,
            'name' => $app->name,
            'slug' => $app->slug,
            'image' => $app->image ?? config('api.defaults.image.logo.default'),
            'note' => $app->note,
            'status' => $app->status,
            'created_at' => $app->created_at->toDateTimeString(),
            'updated_at' => $app->updated_at->toDateTimeString(),
        ];
    }
}