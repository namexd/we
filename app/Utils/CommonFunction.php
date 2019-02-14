<?php

namespace App\Utils;

function array_trim($arr, $trim = true)
{
    if (!is_array($arr)) return $arr;
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            array_trim($arr[$key]);
        } else {
            $value = ($trim == true) ? trim($value) : $value;
            if ($value == "") {
                unset($arr[$key]);
            } else {
                $arr[$key] = $value;
            }
        }
    }
    return $arr;
}

function format_value($value, $suffix = '')
{
    if ($value !== '' and $value !== NULL and $value <> -999) return sprintf("%.1f", round($value, 3)) . $suffix;
    else return '-';
}

/*
 * 阿里云短信-发送验证码
 */
function send_vcode($phone, $code, $product = '冷链资源管理系统')
{
    $smsService = \App::make(\Curder\LaravelAliyunSms\AliyunSms::class);
    $tplId = env('ALIYUN_SMS_CODE_VCODE');
    $params = [
        'code' => $code,
        'product' => $product,
    ];
    $rs = $smsService->send(strval($phone), $tplId, $params);
    return $rs;
}


/**
 * 最近X个月的月份(不含当前月）：201608，201609...
 * @param int $length 几个月
 * @param null $year_month 初始月 201608
 * @param string $format
 * @return array
 */
function get_last_months($length = 6, $year_month = null, $format = "Ym")
{
    if ($year_month == null) $year_month = date("Ym");
    $year = substr($year_month, 0, 4);
    $month = substr($year_month, -2);
    $data = $year . '-' . $month . '-01 00:00:00';
    $arr = [];
    for ($i = $length; $i > 0; $i--) {
        $arr[] = date($format, strtotime($data . ' -' . $i . ' month'));
    }
    return $arr;
}

function get_month_first($date = null)
{
    if ($date == null) {
        $date = date('Y-m-01');
    } elseif ($date == 'this') {
        return date('Y-m-01');
    }
    $timestamp = strtotime($date);
    $firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1) . '-01'));
    return $firstday;
}

function get_month_last($date = null)
{
    if ($date == null) {
        $date = date('Y-m-01');
    } elseif ($date == 'this') {
        $firstday = date('Y-m-01');
        return date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    }
    $timestamp = strtotime($date);
    $firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) - 1) . '-01'));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    return $lastday;
}

/**
 * 往url里添加参数
 * @param $url
 * @param $key
 * @param $value
 * @return string
 */
function add_query_param($url, $key, $value)
{
    $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
    $url = substr($url, 0, -1);
    if (strpos($url, '?') === false) {
        return ($url . '?' . $key . '=' . $value);
    } else {
        return ($url . '&' . $key . '=' . $value);
    }
}
