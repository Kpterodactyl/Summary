<?php

namespace Gini\Controller\CGI\AJAX;

class Summary extends \Gini\Controller\CGI {
        const WIN_OS = 1;
        const MAC = 2;
        const LINUX = 3;
        const UNIX = 4;
        const ANDROID = 5;
        const iOS =  6;
        const IPAD = 7;
        const WIN_PHONE = 8;
        
        
        static $OS = [
            self::WIN_OS => 'Windows',
            self::MAC => 'Mac',
            self::LINUX => 'Linux',
            self::UNIX => 'Unix',
            self::ANDROID => 'Android',
            self::iOS => 'iOS',
            self::IPAD => 'iPad',
            self::WIN_PHONE => 'Windows Phone',
        ];
        const people = 1;
        const labs = 2;
        const equipments = 3;
        const roles = 4;
        const gismon = 5;
        const achievements =  6;
        const nfs_share = 7;
        const messages = 8;
        const announces = 9;
        const billing = 10;
        const cers = 11;
        const entrance = 12;
        const envmon = 13;
        const orders = 14;
        const vendor =  15;
        const eq_charge = 16;
        const eq_sample = 17;
        const eq_reserv = 18;
        const nfs = 19;
        const wechat = 20;

        static $MODULE = [
            self::people =>'people',
            self::labs =>'labs',
            self::equipments =>'equipments',
            self::roles =>'roles',
            self::gismon =>'gismon',
            self::achievements =>'achievements',
            self::nfs_share =>'nfs_share',
            self::messages =>'messages',
            self::announces =>'announces',
            self::billing =>'billing',
            self::cers =>'cers',
            self::entrance =>'entrance',
            self::envmon =>'envmon',
            self::orders =>'orders',
            self::vendor =>'vendor',
            self::eq_charge =>'eq_charge',
            self::eq_sample =>'eq_sample',
            self::eq_reserv =>'eq_reserv',
            self::nfs =>'nfs',
            self::wechat =>'wechat'
        ];
    public function actionOverview () {
        $days=array();
        for($i=0;$i<=30;$i++ ){
        $days[]=date("Y-m-d",strtotime('-'.$i.'day'));

        }    
        $db = \Gini\Database::db();
        $j = 0;
        foreach (array_reverse($days) as $value) {
            $visit_count = $db->value("select count(*) as count from base_point where DATE_FORMAT(dtstart,'%Y-%m-%d') = '".$value."'  group by day(dtstart) ");
            if(is_null($visit_count)) {
                $visit_count = 0;
            }
            $data[$j] = [$value, $visit_count];
            $j++;
        }   
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));       
    }
    public function actionLine2Option() {
        for($i=1;$i<=20;$i++){
            $action[$i] = those('base_action')->whose('module')->is(self::$MODULE[$i])->totalCount();
        }
    
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($action));
    }
    public function actionPage($page = 1) {
        $page_info = $this->page($page,10); 
        $login_info = those('base_point')->orderBy('dtstart','DESC')->limit($page_info['start'],$page_info['end']);
        //登录信息
        foreach ($login_info as $value) {
            
            $lo['source_name'] = $value->source_name; 
            $lo['province'] = $value->province;
            $lo['city'] = $value->city;
            $lo['address'] = $value->address;
            $lo['browser'] = $value->browser;
            $lo['b_version'] = $value->version;
            $lo['OS_type'] = $value->os;
            $lo['dtstart'] = $value->dtstart;
            $los[] = $lo;
            
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($los));
    }
    public function actionBrowserOption() {  
        for($i=1;$i<=8;$i++){
            $os[$i] = those('base_point')->whose('os')->is(self::$OS[$i])->totalCount();
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($os));
    }
    public function actionCountAction() {
        $total = those('base_action')->totalCount();
        $db = \Gini\Database::db();
        $action = $db->query("select distinct action from base_action")->rows();
        $actionarray = [];
        foreach ($action as $value) {
            $value = (array)$value;
            $actionarray[] = $value['action'];            
        }
        $i = 0;
        foreach ($actionarray as $value) {
            $count[$i] = those('base_action')->whose('action')->is($value)->totalCount();
            $i++;    
        }
        $data['name'] = $actionarray;
        $data['count'] = $count;    
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));
    }
    private function page($page,$num) {
        if (is_numeric($num)) {
            $start = $page*10-10;
            $end = $page*10;
            //每页显示的数量
            $total = those('base_point')->totalCount();
            $total_page = ceil($total / $num);
            
            return [
                'start' => $start,
                'end' => $end,
                'total_page' => $total_page
            ];

                
        }
    }

}
