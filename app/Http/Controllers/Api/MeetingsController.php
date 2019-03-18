<?php

namespace App\Http\Controllers\Api;

use App\Models\Meeting;
use App\Models\MeetingRegistration;
use App\Models\User;
use App\Transformers\MeetingRegistrationsTransformer;
use App\Transformers\MeetingsTransformer;
use Illuminate\Http\Request;

class MeetingsController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::paginate($request->pageSize??$this->pagesize);
        return $this->response->paginator($meetings, new MeetingsTransformer());
    }

    public function meetingRegistrations(Meeting $meeting, Request $request)
    {
        $meeting_registrations = $meeting->registrations()->paginate($request->pagesize??$this->pagesize);
        return $this->response->paginator($meeting_registrations, new MeetingRegistrationsTransformer());
    }

    public function postRegistration(Request $request)
    {
        $meeting_id = $request->meeting_id;
        $decrypt = json_decode(\App\Utils\decrypt($request->code, 'qrcode'));
        if (!$decrypt) {
            $data['message'] = '该用户不存在';
        } else {
            $user_id = $decrypt->id;
            if (!Meeting::find($meeting_id)) {
                $data['message'] = '该活动不存在';
            } else {
                if ($user = User::find($user_id)) {
                    if (MeetingRegistration::where(['user_id' => $user_id, 'meeting_id' => $meeting_id])->first()) {
                        $data['message'] = '该用户已报名';
                    } else {
                        $registration_attributes = [
                            'user_id' => $user->id,
                            'user_name' => $user->realname,
                            'phone' => $user->phone,
                            'meeting_id' => $meeting_id,
                        ];
                        if (MeetingRegistration::create($registration_attributes)) {
                            $data['message'] = '领取成功';
                        } else {
                            $data['message'] = '领取失败';
                        }
                    }
                } else {
                    $data['message'] = '该用户不存在';
                }
            }

        }
        return $this->response->array($data);
    }
}
