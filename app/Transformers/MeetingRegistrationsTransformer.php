<?php

namespace App\Transformers;

use App\Models\Manual;
use App\Models\MeetingRegistration;
use League\Fractal\TransformerAbstract;

class MeetingRegistrationsTransformer extends TransformerAbstract
{
    public function transform(MeetingRegistration $meetingRegistration)
    {
        $rs = [
            'id' => $meetingRegistration->id,
            'title' => $meetingRegistration->meeting->title,
            'user_name' => $meetingRegistration->user_name,
            'phone' => $meetingRegistration->phone,
            'created_at' => $meetingRegistration->created_at->toDateTimeString(),
            'updated_at' => $meetingRegistration->updated_at->toDateTimeString(),
        ];
        return  $rs;
    }
//2019/3/14 13:24
//2019/3/14 13:26
}