<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $fillable=[
        'name',
        'slug',
        'note',
        'status',
        ];

    public function HasUser()
    {
        return $this->hasMany(UserHasApp::class);
    }

}
