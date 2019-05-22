<?php

namespace App\Transformers;

use App\Models\Menu;
use App\Models\Message;
use App\Models\MessageDistribution;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform(Message $message)
    {
        return [
            'id' => $message->id,
            'subject' => $message->subject,
            'content' => $message->content,
            'send_time' => Carbon::createFromTimestamp($message->send_time)->diffForHumans(),
            'message_type' => Message::MESSAGE_TYPE[$message->message_type],
            'from_type' => Message::FROM_TYPE[$message->from_type],
        ];
    }

}