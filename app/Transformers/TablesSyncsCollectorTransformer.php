<?php

namespace App\Transformers;

use App\Models\Ccms\Collector;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class TablesSyncsCollectorTransformer extends TransformerAbstract
{
    public function transform(Collector $collector)
    {
        return $collector->toArray();

    }
}