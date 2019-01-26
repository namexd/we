<?php

namespace App\Models\Ccrp;

use App\Traits\ModelTree;
use Carbon\Carbon;
use Encore\Admin\Traits\AdminBuilder;

class Company extends Coldchain2Model
{

    use AdminBuilder, ModelTree {
        ModelTree::boot as treeBoot;
    }

    protected $connection = 'dbyingyong';
    protected $table = 'user_company';
    protected $pk = 'id';
    protected $fillable = ['id', 'title', 'short_title', 'office_title', 'custome_code', 'company_group', 'ctime', 'status', 'list_not_show', 'map_level', 'manager', 'email', 'phone', 'tel', 'address', 'map_title', 'address_lat', 'address_lon', 'username', 'password', 'pid', 'cdc_admin', 'cdc_level', 'cdc_map_level', 'area_level1_id', 'area_level2_id', 'area_level3_id', 'area_level4_id', 'company_type', 'sub_count', 'category_count', 'category_count_has_cooler', 'shebei_install', 'shebei_install_type1', 'shebei_install_type2', 'shebei_install_type3', 'shebei_install_type4', 'shebei_install_type5', 'shebei_install_type6', 'shebei_install_type7', 'shebei_install_type8', 'shebei_install_type100', 'shebei_install_type101', 'shebei_actived', 'shebei_actived_type1', 'shebei_actived_type2', 'shebei_actived_type3', 'shebei_actived_type4', 'shebei_actived_type5', 'shebei_actived_type6', 'shebei_actived_type7', 'shebei_actived_type8', 'shebei_actived_type100', 'shebei_actived_type101', 'shebei_vehicle', 'alerms_all', 'alerms_today', 'alerms_new', 'sort', 'region_code', 'region_name'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setParentColumn('pid');
        $this->setOrderColumn('sort');
    }

    const COMPANY_TYPE = array(
        0 => '未分类',
        1 => '综合医院',
        2 => '专科医院',
        3 => '社区门诊',
        4 => '产院',
        5 => '特需门诊',
        6 => '犬伤门诊',
        7 => '大专学院',
        8 => '科研机构',
        9 => '疾控中心',
        10 => '动物疫控',
        11 => '医药企业',
        12 => '物流公司',
    );

    const UC_UNIT_CATEGORIES = [
        4 => '产科',
        5 => '特需门诊',
        6 => '犬伤门诊',
    ];

    public static function getbyUnitId($unit_id)
    {
        return self::get(['uc_unit_id' => $unit_id]);
    }

    public function getCompanyTypeAttr($value)
    {
        return self::$company_type[$value];
    }

    public function coolers()
    {
        return $this->hasMany(Cooler::class, 'company_id', 'id')->where('status','!=',4);
    }
    public function coolersUninstalled()
    {
        return $this->hasMany(Cooler::class, 'company_id', 'id')->where('status','=',4);
    }
    public function coolersOnline()
    {
        return $this->hasMany(Cooler::class, 'company_id', 'id')->where('status','!=',3)->where('status','!=',4)->where('collector_num','>',0)->orderBy('category_id','asc')->orderBy('cooler_name','asc');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'company_id', 'id')->where([
            'status' => ['eq', 1],
        ]);
    }

    public function collectors()
    {
        return $this->hasMany(Collector::class, 'company_id', 'id')->where('status', 1)->orderBy('collector_name');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'company_id', 'id')->field('id,title,company_id,cooler_count')->where(['cooler_count' => ['gt', 0]]);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    /**
     * 联动下拉框数据
     */
    public static function lists($company_ids = null, $pid = 0)
    {
        if ($company_ids === null and session('cc2.user')['userCompany']) {
            $company_ids = session('cc2.user')['userCompany']->ids ?? session('cc2.user')['company_id'];
        }
        if ($pid === 0) {
            $pid = session('cc2.user')['userCompany']['id'];
        }
        $companys = Company::all(['id' => $company_ids]);
        $list_company = [];
        foreach ($companys as $company) {
            $list_company[] = [
                'name' => $company->title,
                'value' => (string)$company->id,
                'parent' => (string)(($company->id == $pid) ? 0 : $company->pid),
            ];
        }
        return $list_company;
    }

    public static function lists_options($config = ['pid' => 0])
    {
        if ($config['pid'] === 0) {
            $config['pid'] = session('cc2.user')['userCompany']['id'];
        }
        return [
            'columns' => 3,
            'list' => self::lists(),
            'default' => (string)$config['pid']
        ];
    }

    public function getTree($where = array(), $refresh = 0, $field = 'id,pid,title,short_title,sort,shebei_install,shebei_actived,sub_count,category_count,cdc_admin,cdc_category_menu,cdc_level')
    {
        $id = 0;
        $level = 0;
        $categoryObjs = array();
        $tree = array();
        $childrenNodes = array();
        $categorys = $this->field($field)->where($where)->orderBy('cdc_level asc,pid asc,sort desc,company_type asc,username asc,id asc')->select();

        /*echo '<!--';
        echo  M()->getlastsql();
        echo '-->';*/

        $vo_subcats = array();
        foreach ($categorys as &$vo) {
            $subs = array();
            //如果打开cdc_category_menu，强行显示分类下设备
            if ($vo['cdc_category_menu'] == 1) {
                $vo_subcats[] = $subs = D('Cooler_category')->field('(company_id*100000000+id) as id,company_id as pid,title,sort,cooler_count as shebei_install,cooler_count as shebei_actived,0 as sub_count,1 as category_count,0 as cdc_admin,0 as cdc_category_menu,id as category_id')->where(array('company_id' => $vo['id'], 'pid' => 0))->select();
                //print_r($vo_subcats);
                $vo['shebei_install'] = count($subs);
            }
        }

        if ($vo_subcats) {
            foreach ($vo_subcats as $vvo)
                $categorys = array_merge($categorys, $vvo);
        }
        $map['status'] = 1;
        foreach ($categorys as $key => $cate) {
            $obj = new \stdClass();
            $cate['children_count'] = 0;
            $obj->root = $cate;
            $id = $cate['id'];
            //pid 不为0的时候，修正下echo $key;
            if ($key == 0)
                $level = 0;
            else
                $level = $cate['pid'];
            //$obj->children = array();
            $categoryObjs[$id] = $obj;
            if ($level) {
                $childrenNodes[] = $obj;
            } else {
                $tree[] = $obj;
            }

        }
        foreach ($childrenNodes as $node) {
            $cate = $node->root;
            $id = $cate['id'];
            $level = $cate['pid'];
            $categoryObjs[$level]->children[] = $node;
            $categoryObjs[$level]->root['children_count'] = count($categoryObjs[$level]->children);
        }
        return $tree;
    }

    public function children($pid = 0)
    {
        $company = $this;
        if (!$pid) {
            $pid = $company['id'];
        }
        return $this->where('pid', $pid)->get();
    }

    public function ids($cdc_admin = NULL, $show = '', $pid = 0, $id_not_in = NULL, $company_type_check = true)
    {

        $company = $this;
        if (!$pid) {
            $pid = $company['id'];
        }
        if ($company['cdc_admin'] == 0) {
            $this_company_id = $pid;
            $short_title[$pid] = $company['short_title'];
            $title[$pid] = $company['title'];
            $this_company_id = [$pid];
        } else {

            $subwhere['cdc_admin'] = $cdc_admin;
            $subwhere['pid'] = $pid;
            $subwhere['id_not_in'] = $id_not_in;
            $subwhere['company_type_check'] = $company_type_check;

            $company_ids = $this->subCompanies($subwhere)->toArray();

            for ($i = 0; $i < count($company_ids); $i++) {
                $aa[$i] = $company_ids[$i]['id'];
                if ($show == 'title')
                    $title[$company_ids[$i]['id']] = $company_ids[$i]['title'];
                elseif ($show == 'short_title') {
                    if ($company_ids[$i]['short_title'])
                        $short_title[$company_ids[$i]['id']] = $company_ids[$i]['short_title'];
                    else
                        $short_title[$company_ids[$i]['id']] = $company_ids[$i]['title'];

                }
            }
            if (isset($aa))
                $this_company_id = $aa;
            else
                $this_company_id = [];
        }

        if ($show == '')
            return $this_company_id;
        elseif ($show == 'title')
            return $title ?? null;
        elseif ($show == 'short_title')
            return $short_title ?? null;
        elseif ($show == 'array')
            return $aa ?? [];
    }

    public function subCompanies($where = [], $maper = [])
    {
        if (!isset($where['pid']) or $where['pid'] == 0) {
            $company = $this;
        } else {
            $company = $this->find($where['pid']);
        }
        if ($company['area_level' . $company['cdc_level'] . '_id'] > 0) {
            $maper['area_level' . $company['cdc_level'] . '_id'] = $company['area_level' . $company['cdc_level'] . '_id'];
        } else {
            $maper['pid'] = $company['id']; //父分类ID
        }

        if (isset($where['cdc_admin']) and $where['cdc_admin'] !== NULL) {
            $maper['cdc_admin'] = $where['cdc_admin'];
        }
        $maper['status'] = 1;
        $maper['company_group'] = $this->company_group;
        if (isset($where['id_not_in']) and $where['id_not_in']) {
            $maper['id'] = array('not in', $where['id_not_in']);
        }

        //check user's company_type setting
        if (isset($where['company_type_check']) and $where['company_type_check'] = true and $company['company_type'] > 0) {
            $maper['_string'] = '( (cdc_admin =0 and company_type=' . $company['company_type'] . ') or cdc_admin =1)';
        }

        //check user's company_type setting

        $companies = $this->where($maper)->get();
        return $companies;
    }

    /**
     * 监控的单位数量
     * @return int
     */
    public function subCompaniesCount()
    {
        return $this->subCompanies(['cdc_admin' => 0])->count();
    }

    /**
     * 已监控单位数量
     * @return int
     */
    public function subCompaniesActiveCount()
    {
        return $this->subCompanies(['cdc_admin' => 0], ['shebei_actived' => ['gt', 0]])->count();
    }

    /**
     * 已有冷链设备
     */
    public function subCompaniesCoolersCount()
    {
        $where['company_id'] = $this->ids();
        $where['status'] = ['neq', 4];
        return Cooler::where($where)->count();
    }

    /**
     * 监控中的冷链设备
     */
    public function subCompaniesCoolersActiveCount()
    {
        $where['company_id'] = $this->ids();
        $where['status'] = ['neq', 4];
        $where['collector_num'] = ['gt', 0];
        return Cooler::where($where)->count();
    }

    /**
     * 今日总计报警
     */
    public function subCompaniesAlarmTotayCount()
    {
        $where['company_id'] = $this->ids();
        $where['warning_event_time'] = ['gt', strtotime('today')];
        $event = WarningEvent::where($where)->count();
        $where = [];
        $where['company_id'] = $this->ids();
        $where['sensor_event_time'] = ['gt', strtotime('today')];
        $where['warning_type'] = 0;
        $sender_event = WarningSenderEvent::where($where)->count();
        return $event + $sender_event;
    }

    /**
     * 本月累计预警
     */
    public function subCompaniesAlarmTotalMonthCount()
    {
        $where['warning_event_time'] = ['gt', strtotime(date('Y-m', time()) . '-01 00:00:00')];
        $where['company_id'] = $this->ids();
        $event = WarningEvent::where($where)->count();
        $where = [];
        $where['sensor_event_time'] = ['gt', strtotime(date('Y-m', time()) . '-01 00:00:00')];
        $where['company_id'] = $this->ids();
        $where['warning_type'] = 0;
        $sender_event = WarningSenderEvent::where($where)->count();
        return $event + $sender_event;
    }

    /**
     * 未处理报警
     */
    public function subCompaniesAlarmUnhandledCount()
    {
        $where['company_id'] = $this->ids();
        $where['handled'] = 0;
        $event = WarningEvent::where($where)->count();
        $where = [];
        $where['company_id'] = $this->ids();
        $where['warning_type'] = 0;
        $where['handled'] = 0;
        $sender_event = WarningSenderEvent::where($where)->count();
        return $event + $sender_event;
    }

    /**
     * 冰箱类型统计
     */
    public function subCompaniesCoolerTypesCount()
    {
        $where['company_id'] = $this->ids();
        $where['status'] = ['neq', 4];
        $where['collector_num'] = ['gt', 0];
        $types = Cooler::where($where)->field('cooler_type,count(1) as value')->group('cooler_type')->select();
        foreach ($types as &$type) {
            $type['name'] = $type['cooler_type'];
            unset($type['cooler_type']);
        }
        return $types;
    }


    /**
     * 报警月度统计
     */
    public function subCompaniesAlarmMonthCount()
    {
        $where = [];
        $where['company_id'] = $this->ids();
        $where['warning_event_time'] = ['gt', strtotime(date('Y-m', strtotime('last year')))];
        $where['warning_type'] = 1;
        $event = WarningEvent::where($where)->field('FROM_UNIXTIME(warning_event_time,"%Y%m") as name,count(1) as value')->group('FROM_UNIXTIME(warning_event_time,"%Y%m")')->limit('12')->select()->toArray();
        $event_arr = array_combine(array_column($event, 'name'), array_column($event, 'value'));
        $where['warning_type'] = 2;
        $event2 = WarningEvent::where($where)->field('FROM_UNIXTIME(warning_event_time,"%Y%m") as name,count(1) as value')->group('FROM_UNIXTIME(warning_event_time,"%Y%m")')->limit('12')->select()->toArray();
        $event2_arr = array_combine(array_column($event2, 'name'), array_column($event2, 'value'));

        $where = [];
        $where['company_id'] = $this->ids();
        $where['warning_type'] = 0;
        $where['sensor_event_time'] = ['gt', strtotime(date('Y-m', strtotime('last year')))];

        $sender_event = WarningSenderEvent::where($where)->field('FROM_UNIXTIME(sensor_event_time,"%Y%m") as name,count(1) as value')->group('FROM_UNIXTIME(sensor_event_time,"%Y%m")')->limit('12')->select()->toArray();

        $sender_event_arr = array_combine(array_column($sender_event, 'name'), array_column($sender_event, 'value'));

        $combie = [];

        for ($i = 0; $i < 12; $i++) {
            $key = date('Ym', strtotime('last year + ' . $i . ' month'));
            $sensor_high[] = ['name' => $key, 'value' => ($event_arr[$key] ?? 0)];
            $sensor_low[] = ['name' => $key, 'value' => ($event2_arr[$key] ?? 0)];
            $sender[] = ['name' => $key, 'value' => ($sender_event_arr[$key] ?? 0)];
            $combie[] = ['name' => $key, 'value' => ($event_arr[$key] ?? 0) + ($event2_arr[$key] ?? 0) + ($sender_event_arr[$key] ?? 0)];
        }

        $json = [
            'sensor_high' => $sensor_high,
            'sensor_low' => $sensor_low,
            'sender' => $sender,
            'combie' => $combie,
        ];

        return $json;
    }


    /**
     * 登录统计
     */
    public function subCompaniesLoginCount()
    {
        $where['company_id'] = $this->ids();
        $where['login_time'] = ['gt', strtotime(date('Y-m', strtotime('last year')))];
        $event = UserLoginLog::where($where)->field('FROM_UNIXTIME(login_time,"%Y%m") as name,count(1) as value')->group('FROM_UNIXTIME(login_time,"%Y%m")')->select()->toArray();
        $event_arr = array_combine(array_column($event, 'name'), array_column($event, 'value'));

        $where['company_id'] = $this->ids();
        $where['type'] = 3;
        $where['login_time'] = ['gt', strtotime(date('Y-m', strtotime('last year')))];
        $wx_event = UserLoginLog::where($where)->field('FROM_UNIXTIME(login_time,"%Y%m") as name,count(1) as value')->group('FROM_UNIXTIME(login_time,"%Y%m")')->select()->toArray();
        $wx_event_arr = array_combine(array_column($wx_event, 'name'), array_column($wx_event, 'value'));

        $combie = $weixin = [];
        for ($i = 0; $i < 12; $i++) {
            $key = date('Ym', strtotime('last year + ' . $i . ' month'));
            $weixin[] = ['name' => $key, 'value' => ($wx_event_arr[$key] ?? 0)];
            $combie[] = ['name' => $key, 'value' => ($event_arr[$key] ?? 0)];
        }
        $json = [
            'weixin' => $weixin,
            'combie' => $combie,
        ];
        return $json;
    }

    /**
     * 医用冰箱统计
     */
    public function subCompaniesCoolerMedicalCount()
    {
        $where['company_id'] = $this->ids();
        $combie = [];
        for ($i = 0; $i < 12; $i++) {
            $key = date('Ym', strtotime('last year + ' . $i . ' month'));
            $start = strtotime(date('Y-m-01', strtotime('last year + ' . $i . ' month')));
            $end = strtotime(date('Y-m-01', strtotime('last year + ' . ($i + 1) . ' month')));

            $where['uninstall_time'] = [['eq', 0], ['gt', $start], 'or'];
            $where['install_time'] = [['eq', 0], ['lt', $end], ['exp', ' is NULL'], 'or'];

//        $map['_string'] = '(uninstall_time = 0 or uninstall_time >' . $start . ') and (install_time is NULL or install_time=0 or  install_time <' . $end . ')';
            $combie[] = Cooler::where($where)->field('' . $key . ' name, count(1) as total,sum(if( is_medical="2","1","0")) as medical ')->find()->toArray();
        }
        return $combie;
    }

    public function statManageAvg($year,$month)
    {
        $avg = StatMange::whereIn('company_id',$this->ids())->where('year',$year)->where('month',$month)->avg('grade');
        return round($avg,2);
    }
    public function statWarningsCount($start,$end)
    {
        return  WarningEvent::whereIn('company_id',$this->ids())->whereBetween('warning_event_time',[strtotime($start),strtotime($end)])->count();
    }
    public function doesManualRecords()
    {
        return  $this->hasOne(CompanyDoesManualRecord::class);
    }



}
