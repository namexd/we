<?php

namespace App\Models\Ccrp;

use Carbon\Carbon;
use DB;

class StatManualRecord extends Coldchain2Model
{
    protected $table = 'stat_monthly';

    public function signature()
    {
        return $this->hasOne(Signature::class, 'sign_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cooler()
    {
        return $this->belongsTo(Cooler::class);
    }

    public static function getListByMonth($company_id, $year = null, $month = null)
    {
        if ($month == null) {
            $year = date('Y');
            $month = date('m');
        }else{
            $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        }
        $reports = self::where('company_id', $company_id)->where('year', $year)->where('month', $month)->select("year", "month", "day", "sign_time_a", "company_id", DB::raw("count(1) as cnt"))->groupBy('company_id', 'year', 'month', 'day', 'sign_time_a')->get();

        $data = array();
        if (!$reports) {
            return false;
        } else {
            $last_day = Carbon::now()->setDateTime($year, $month, 1, 0, 0, 0)->endOfMonth()->day;
            for ($i = 1; $i <= $last_day; $i++) {
                $row['year'] = $year;
                $row['month'] = $month;
                $row['day'] = str_pad($i, 2, "0", STR_PAD_LEFT);
                $row['AM'] = 0;
                $row['PM'] = 0;
                $data[$i] = $row;
            }
            foreach ($reports as $re) {
                $data[$re->day][$re->sign_time_a] = $re->cnt;
            }
            $data = array_values($data);
        }
        return $data;

    }

    public static function getByDay($company_id, $year = null, $month = null, $day)
    {
        if ($month == null) {
            $year = date('Y');
            $month = date('m');
        }
        $reports = self::where('company_id', $company_id)->where('year', $year)->where('month', $month)->groupBy('company_id', 'year', 'month', 'day', 'sign_time_a')->get();

        $data = array();
        if (!$reports) {
            return false;
        } else {
            for ($i = 1; $i <= date('d', time()); $i++) {
                $row['year'] = $year;
                $row['month'] = $month;
                $row['day'] = str_pad($i, 2, "0", STR_PAD_LEFT);
                $row['AM'] = 0;
                $row['PM'] = 0;
                $data[$i] = $row;
            }
            foreach ($reports as $re) {
                $data[$re->day][$re->sign_time_a] = $re->cnt;
            }
            $data = array_values($data);
        }
        return $data;

    }
}
