<?php

namespace App\Http\Middleware;

use App\Models\ApilogUserAgent;
use function App\Utils\is_mobile;
use Closure;
use Illuminate\Support\Facades\Input;

class ApiLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 执行动作 之前
        $response = $next($request);
        // 执行动作 之后
        $this->apilog($request);
        return $response;
    }

    private function apilog($request)
    {
        $data['user_id'] = (int)$request->user()->id;
        $data['method'] = $request->method();
        $data['uri'] = $uri = $request->route()->uri;
        $segments = $request->segments();

        if (strpos($uri, '/') === 0) {
            $uri = substr($uri, 1);
        }
        $uri_array = explode('/', $uri);
        $diff = array_diff_assoc($segments, $uri_array);
        if ($diff == []) {
            $data['query'] = '';
        } else {

            $diff2 = array_diff_assoc($uri_array, $segments);
            $diffrent = [];
            foreach ($diff2 as $key => $item) {
                if (isset($diff[$key])) {
                    $diffrent[str_replace(['{', '}', '?'], '', $item)] = $diff[$key];
                };
            }
            $data['query'] = json_encode($diffrent);
        }
        $data['params'] =   ($request->query() and current($request->query()))?json_encode($request->query()):'';
        $data['route_name'] = $request->route()->getName();
        $user_agent = $request->userAgent();
        $user_agents = ApilogUserAgent::where('user_agent',$user_agent)->first();
        if(!$user_agents)
        {
            $agent['user_agent'] = $user_agent;
            $agent['is_mobile'] = is_mobile();
            $user_agents = ApilogUserAgent::create($agent);
        }
        $data['user_agent_id'] = $user_agents->id;
        $data['ip'] = $request->ip();
        try{
            if($data['method']=='GET')
            {
                $rs = \App\Models\Apigetlog::create($data);
            }else{
                \App\Models\Apilog::create($data);
            }
        }catch (\Exception $exception)
        {

        }
    }
}
