<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserHasApp;
use League\Fractal\TransformerAbstract;

class UserHasAppTransformer extends TransformerAbstract
{
    public function transform(UserHasApp $hasApp)
    {
        return [
            'user_id' => $hasApp->user_id,
            'app_id' => $hasApp->app_id,
            'app_name' => $hasApp->app->name,
            'app_slug' => $hasApp->app->slug,
            'app_username' => $hasApp->app_username,
            'app_userid' => $hasApp->app_userid,
            'app_unitid' => $hasApp->app_unitid,
            'created_at' => $hasApp->created_at->toDateTimeString(),
            'updated_at' => $hasApp->updated_at->toDateTimeString(),
        ];
    }
}