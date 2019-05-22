<?php

namespace App\Transformers;

use App\Models\MessageDistribution;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class MessageDistributionTransformer extends TransformerAbstract
{
    public $availableIncludes=['message'];
    public function transform(MessageDistribution $messageDistribution)
    {
        return [
            'id' => $messageDistribution->id,
            'read_status' => $messageDistribution->read_status,
            'read_at' => Carbon::createFromTimestamp($messageDistribution->read_at)->diffForHumans(),
            'created_at' => $messageDistribution->created_at->toDateTimeString(),
            'updated_at' => $messageDistribution->updated_at->toDateTimeString(),
        ];
    }

    public function includeMessage(MessageDistribution $messageDistribution)
    {
        return $this->item($messageDistribution->message,new  MessageTransformer());
    }
}