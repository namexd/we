<?php

namespace App\Transformers;

use App\Models\Manual;
use App\Models\Meeting;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class MeetingsTransformer extends TransformerAbstract
{
    public function transform(Meeting $meeting)
    {
        $rs = [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'date' => Carbon::parse($meeting->date)->toDateString(),
            'created_at' => $meeting->created_at->toDateTimeString(),
            'updated_at' => $meeting->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }

}