<?php

namespace App\Http\Controllers\Api;

use App\Models\Meeting;
use App\Transformers\MeetingsTransformer;
use Illuminate\Http\Request;

class MeetingsController extends Controller
{
    public function index(Request $request)
    {
        $meetings=Meeting::paginate($request->pageSize??$this->pagesize);
        return $this->response->paginator($meetings,new MeetingsTransformer());
    }

    public function meetingRegistrations()
    {

    }
}
