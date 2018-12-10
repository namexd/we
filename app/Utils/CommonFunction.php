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

function format_value($value,$suffix='')
{
    if ($value !== '' and $value !== NULL and $value <> -999) return sprintf("%.1f", round($value, 3)) . $suffix;
    else return '-';
}