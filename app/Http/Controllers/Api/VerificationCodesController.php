<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use function App\Utils\send_vcode;
use Illuminate\Http\Request;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request)
    {
        $phone = $request->phone;

        // 生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try {
            $result = send_vcode($phone,$code);
        } catch (\Exception $exception) {
            $message = '短信发送异常';
            return $this->response->errorInternal($message ?: '短信发送异常');
        }
        $key = 'verificationCode_'.str_random(15);

        $expiredAt = now()->addMinutes(10);
        // 缓存验证码 10分钟过期。
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}