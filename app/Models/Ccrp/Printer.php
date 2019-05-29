<?php

namespace App\Models\Ccrp;

use App\Extensions\Feie\PrinterAPI;
use function App\Utils\is_com_diy;
use function App\Utils\vehicle_temp2;
use function App\Utils\vehicle_time2;

class Printer extends Coldchain2Model
{
    protected $table = 'printer';
    protected $primaryKey = 'printer_id';
    const CONFIG=[
        'PRINTER_IP'=>'pr01.coldyun.com',//现在连不上。
        'PRINTER_PORT'=>80,
        'PRINTER_HOSTNAME'=>'/WPServer',
    ];
    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->field('id,title,short_title');
    }

    public function printer_print_array($id,$title, $content,$user_id,$subtitle = null, $from = null)
    {
        $dayinji =$this->find($id);
        if ($dayinji) {
            $printer = new PrinterAPI();
            $printer->PRINTER_SN = $dayinji['printer_sn'];
            $printer->KEY = $dayinji['printer_key'];
            $printer->IP = self::CONFIG['PRINTER_IP'];
            $printer->PORT = self::CONFIG['PRINTER_PORT'];
            $printer->HOSTNAME = self::CONFIG['PRINTER_HOSTNAME'];
            if($dayinji['printer_sn']=='00000000')
            {
                $rs = virtualPrint();
            } else{
                $rs = $printer->wp_print($dayinji['printer_sn'], $dayinji['printer_key'], $content);
            }
            $rs = json_decode($rs,true);
            if ($rs['responseCode'] == 0) {
                if ($from) {
                    $data = $from;
                }
                $data['printer_id'] = $dayinji['printer_id'];
                $data['title'] = $title;
                $data['subtitle'] = $subtitle;
                $data['content'] = $content;
                $data['print_time'] = time();
                $data['company_id'] = $this->company_id;
                $data['uid'] =$user_id;
                $data['server_state'] = $rs['msg'];
                $data['orderindex'] = $rs['orderindex'];
//                $this->printer_logs()->insert($data);
            }
            return $rs;
            //$this->success($rs->msg,'/user/printer');

            //{"responseCode":0,"msg":"服务器接收订单成功","orderindex":"1461323673156926118661"}
        }
    }
    public function printer_logs()
    {
        return $this->hasMany(PrinterLog::class,'printer_id','printer_id');
    }
    //打印机模板
    public function vehicle_print_data_format($title,$datas,$company_id){
        $orderInfo = '<CB>'.$title.'</CB><BR>';
        $orderInfo .= '时间|温度1|温度2|温度3|温度4<BR>';
        $orderInfo .= '--------------------------------<BR>';
        foreach($datas as $vo) {
            $orderInfo .= ''.vehicle_time2($vo['RcvDT']).'　'.vehicle_temp2($vo['Temperature']).','.vehicle_temp2($vo['Temperature2']).','.vehicle_temp2($vo['Temperature3']).','.vehicle_temp2($vo['Temperature4']).' <BR>';
        }
        $orderInfo .= '--------------------------------<BR>';
        if(!is_com_diy($company_id,'printer_no_time'))
        {
            $orderInfo .= '打印时间：' . date('Y-m-d H:i:s') . '<BR>';
        }
        $orderInfo .= '<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= '签字________________________<BR>';
        $orderInfo .= '<BR>';
        $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
        $orderInfo .= '　上海冷王智能科技有限公司';
        return $orderInfo;
    }

    //打印机模板
    public function vehicle_print_data_format_array($title,$datas,$company_id){
        $orderInfoArr=array();
        $orderInfo = '<CB>'.$title.'</CB><BR>';
        $orderInfo .= '时间|温度1|温度2|温度3|温度4<BR>';
        $orderInfo .= '--------------------------------<BR>';
        foreach($datas as $vo) {
            $orderInfo .= ''.vehicle_time2($vo['RcvDT']).'　'.vehicle_temp2($vo['Temperature']).','.vehicle_temp2($vo['Temperature2']).','.vehicle_temp2($vo['Temperature3']).','.vehicle_temp2($vo['Temperature4']).' <BR>';
            if( strlen($orderInfo)>=250)
            {
                $orderInfoArr[]=$orderInfo;
                $orderInfo='';
            }
        }
        if(count($orderInfoArr)>0){
            $orderInfo .= '--------------------------------<BR>';
            if(!is_com_diy($company_id,'printer_no_time'))
            {
                $orderInfo .= '打印时间：' . date('Y-m-d H:i:s') . '<BR>';
            }
            $orderInfo .= '<BR>';
            $orderInfo .= '<BR>';
            $orderInfo .= '签字________________________<BR>';
            $orderInfo .= '<BR>';
            $orderInfo .= ' 技术支持电话：400-681-5218<BR>';
            $orderInfo .= '　上海冷王智能科技有限公司';
            $orderInfoArr[]=$orderInfo;
            return $orderInfoArr;
        }else{
            return NULL;
        }

    }

}
//虚拟打印，返回成功。
function virtualPrint()
{
    $data['msg'] = '虚拟打印成功';
    $data['responseCode'] = 0;
    $data['orderindex'] = 0;
    return json_encode($data);
}