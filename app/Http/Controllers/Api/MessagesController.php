<?php

namespace App\Http\Controllers\Api;

use App\Models\Ccrp\User;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
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
                                    $ccrp_company = $ccrp_user->user_company;
                                    if ($ccrp_company) {
                                        $company_ids = $ccrp_company->ids();
                                        $total += $message['ccrp']['warningevent_overtemp'] = WarningEvent::whereIn('company_id', $company_ids)->where('handled', 0)->count();
                                        $total += $message['ccrp']['warningevent_poweroff'] = WarningSenderEvent::whereIn('company_id', $company_ids)->where('handled', 0)->count();
                                        if ($ccrp_company->cdc_admin == 0 and $manual_records = $ccrp_company->doesManualRecords) {
                                            $message['ccrp']['need_temp_record'] = !(bool)$manual_records->isDone->count();
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
                $message['total'] = $total;
                return $message;
            });
        }
        return $this->response->array($message);
    }
}
