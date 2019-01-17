<?php

namespace App\Transformers\Ccms;

use App\Models\Ccms\Collector;
use App\Models\Ccms\WarningEvent;
use App\Models\Ccms\WarningEventOption;
use App\Models\Ccms\WarningSetting;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningEventOptionTransformer extends TransformerAbstract
{

    public function transform(WarningEventOption $option)
    {
        $result = [
            'id' => $option->id,
            'warning_type' => $option->warning_type,
            'title' => $option->title,
        ];
        return $result;
    }

}