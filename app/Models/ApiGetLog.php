<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ApiGetLog extends Authenticatable
{
    use Notifiable;

    protected $connection = 'pgccsc';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','method','uri','query','params','route_name','user_agent_id','user_agent','ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
