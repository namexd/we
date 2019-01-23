<?php

namespace App\Http\Middleware;

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
        $data['params'] =  json_encode(Input::get());
        $data['route_name'] = $request->route()->getName();
        $data['user_agent'] = $request->userAgent();
        $data['ip'] = $request->ip();
        try{
            \App\Models\Apilog::create($data);
        }catch (\Exception $exception)
        {

        }
    }
}
