<?php

namespace App\Http\Controllers\Api;

use App\Transformers\MessageDistributionTransformer;
use Illuminate\Http\Request;
use App\Models\Ccrp\User;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
use App\Models\MessageDistribution;
use App\Models\UserHasApp;
use Auth;
use Cache;

class MessagesController extends Controller
{
    public function count($type = 'new')
    {
        if (Auth::guard('api')->check()) {
            $user = $this->user();
            Cache::forget('meta_message_' . $user->id . '_' . $type);
            $message = Cache::remember('meta_message_' . $user->id . '_' . $type, 1, function () use ($user) {
                $total = 0;
                $apps = $user->apps;
                if ($apps) {
                    foreach ($apps as $app) {
                        switch ($app->slug) {
                            case  'ccrp':
                                $user_app = UserHasApp::where('user_id', $user->id)->where('app_id', $app->id)->first();
                                if ($user_app) {
                                    $ccrp_user = User::where('id', $user_app->app_userid)->where('status', 1)->first();
                                    $ccrp_company = $ccrp_user->userCompany;
                                    if ($ccrp_company) {
                                        $company_ids = $ccrp_company->ids();
                                        $total += $message['ccrp']['warning_events']['overtemp'] = WarningEvent::lists($company_ids,WarningEvent::未处理)->count();
                                        $total += $message['ccrp']['warning_events']['poweroff'] = WarningSenderEvent::lists( $company_ids,WarningSenderEvent::未处理)->count();
                                        if ($ccrp_company->needManualRecords()) {
                                            $manual_records = $ccrp_company->doesManualRecords;
                                            $message['ccrp']['needs']['stat_manual_records'] = $manual_records->needManualRecord();
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
                $message['total'] = $total;
                $message['message']['unread'] = $this->message_unread();
                return $message;
            });
        }
        return $this->response->array($message);
    }

    public function message_unread()
    {
        $user=$this->user();
        $count=MessageDistribution::where('user_id',$user->id)->where('read_status',0)->count();
        return $count;
    }
    public function index(Request $request)
    {
        $user=$this->user();
        $model=new MessageDistribution();
        if ($request->has('read_status')&&$read_status=$request->read_status)
        {
            $model=$model->where('read_status',$read_status);
        }
        $message_distributions=$model->where('user_id',$user->id)->paginate($request->pagisize??$this->pagesize);
        return $this->response->paginator($message_distributions,new MessageDistributionTransformer());
    }

    public function show($id)
    {
        $message_distribution=MessageDistribution::findOrFail($id);
        if ($message_distribution->read_status==0)
        {
            $message_distribution->read_status=1;
            $message_distribution->read_at=time();
            $message_distribution->save();
        }
       return $this->response->item($message_distribution,new MessageDistributionTransformer());
    }
}
