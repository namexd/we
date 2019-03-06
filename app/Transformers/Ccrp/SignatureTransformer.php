<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Sender;
use App\Models\Ccrp\Signature;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SignatureTransformer extends TransformerAbstract
{
    public function transform(Signature $row)
    {
        if ($row->file_uniqid) {
            $url = route('uploads.show', ['uniqid' => $row->file_uniqid]);
        } elseif ($row->img_name) {
            $url = env('SIGNATURE_IMAGE_NAME_URL') . $row->img_name;
        } else {
            $url = env('SIGNATURE_ID_URL') . $row->id;
        }
        return [
            'id' => $row->id,
            'deliverorder' => $row->deliverorder,
            'company_id' => $row->company_id,
            'sign_time' => $row->sign_time ? Carbon::createFromTimestamp($row->sign_time)->toDateTimeString() : '',
            'img_name' => $row->img_name,
            'file_uniqid' => $row->file_uniqid,
            'url' => $url,
        ];
    }
}