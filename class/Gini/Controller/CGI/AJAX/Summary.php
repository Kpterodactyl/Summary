<?php

namespace Gini\Controller\CGI\AJAX;

class Summary extends \Gini\Controller\CGI {

    const PEOPLE = 'people';
    const LABS = 'labs';
    const EQUIPMENTS = 'equipments';
    const ROLES = 'roles';
    const GISMON = 'gismon';
    const ACHIEVEMENTS =  'achievements';
    const NFS_SHARE = 'nfs_share';
    const MESSAGES = 'messages';
    const ANNOUNCES = 'announces';
    const BILLING = 'billing';
    const CERS = 'cers';
    const ENTRANCE = 'entrance';
    const ENVMON = 'envmon';
    const ORDERS = 'orders';
    const VENDOR =  'vendor';
    const EQ_CHARGE = 'eq_charge';
    const EQ_SAMPLE = 'eq_sample';
    const EQ_RESERV = 'eq_reserv';
    const NFS = 'nfs';
    const WECHAT = 'wechat';

    static $MODULE = [
        self::PEOPLE => '成员目录',
        self::LABS => '课题组',
        self::EQUIPMENTS => '仪器目录',
        self::ROLES => '权限管理',
        self::GISMON => '地理监控',
        self::ACHIEVEMENTS =>'成果管理',
        self::NFS_SHARE =>'文件系统',
        self::MESSAGES =>'消息中心',
        self::ANNOUNCES =>'系统公告',
        self::BILLING =>'财务中心',
        self::CERS =>'CERS',
        self::ENTRANCE =>'门禁管理',
        self::ENVMON =>'环境监测',
        self::ORDERS =>'订单管理',
        self::VENDOR =>'供应商管理',
        self::EQ_CHARGE =>'仪器收费',
        self::EQ_SAMPLE =>'送样模块',
        self::EQ_RESERV =>'预约模块',
        self::NFS =>'nfs',
        self::WECHAT =>'微信'
    ];

    public function actionCountModule() {
        $data = [];
        foreach (self::$MODULE as $key => $value) {
            $action = those('base_action')->whose('module')->is($key)->totalCount();
            $data['module'][] = $value;
            $data['count'][] = $action;
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));
    }
    public function actionSearchAction() {
        $form = $this->form();
        $select_module = $form['module'];  
        $db = \Gini\Database::db();
        $action = $db->query("select distinct action from base_action where module ='".$select_module."' group by action")->rows();
        $actionarray = [];
        foreach ($action as $value) {
        $value = (array)$value;
        $actionarray[] = $value['action'];            
        }
        
        foreach ($actionarray as $key=>$value) {
            $count[$key] = those('base_action')->whose('action')->is($value)->whose('module')->is($select_module)->totalCount();
            $count[$key] = $db->value("select count(action) FROM base_action where module ='".$select_module."' and action='".$value."' ");   
        }
        $data['name'] = $actionarray;
        $data['count'] = $count; 
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));
    }
    public function actionSearch () {
        $form = $this->form();
        $dtstart = $form['startpicker'];
        $dtend = $form['endpicker'];

        if(is_null($dtstart)||is_null($dtend)){
            $days=array();
            for($i=0;$i<=30;$i++ ){
            $days[]=date("Y-m-d",strtotime('-'.$i.'day'));
            }    
            $db = \Gini\Database::db();
            foreach (array_reverse($days) as $key => $value) {
                $visit_count = $db->value("select count(*) as count from base_point where DATE_FORMAT(dtstart,'%Y-%m-%d') = '".$value."'  group by day(dtstart) ");
                if(is_null($visit_count)) {
                    $visit_count = 0;
                }
                $result[$key] = [$value, $visit_count];
            }
        }else{
            $db = \Gini\Database::db();
            $result = $db->query("select DATE_FORMAT(dtstart,'%Y-%m-%d') as day ,count(*) as count from base_point where DATE_FORMAT(dtstart,'%Y-%m-%d %H:%M') between '".$dtstart."' and '".$dtend."' group by day(dtstart) ")->rows();
          
        }
        
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($result));           
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
            $lo['device_type'] = $value->device_type;
            $lo['dtstart'] = $value->dtstart;
            $los[] = $lo;
            
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($los));
    }
    public function actionBrowserOption() {  
        $total = those('base_point')->totalCount();
        $db = \Gini\Database::db();
        $browser = $db->query("select distinct browser from base_point")->rows();
        $browserarray = [];
        foreach ($browser as $value) {
            $value = (array)$value;
            $browserarray[] = $value['browser'];            
        }
        $i = 0;
        foreach ($browserarray as $value) {
            $count[$i] = those('base_point')->whose('browser')->is($value)->totalCount();
            $i++;    
        }
        $data['name'] = $browserarray;
        $data['count'] = $count;    
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));
    }
    public function actionDeviceType() {  
        $total = those('base_point')->totalCount();
        $db = \Gini\Database::db();
        $device_type = $db->query("select distinct device_type from base_point")->rows();
        $devicearray = [];
        foreach ($device_type as $value) {
            $value = (array)$value;
            $devicearray[] = $value['device_type'];            
        }
        foreach ($devicearray as $key => $value) {
            $count = those('base_point')->whose('device_type')->is($value)->totalCount();
            $data[$key] = ['value'=>$count, 'name'=>$value];
        } 
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));
    }
    public function actionMapVisit() {  
        
        $db = \Gini\Database::db();
        $city = $db->query("select distinct city from base_point")->rows();
        $cityarray = [];
        foreach ($city as $value) {
            $value = (array)$value;
            $cityarray[] = $value['city'];            
        }
        foreach ($cityarray as $key => $value) {
            $count = those('base_point')->whose('city')->is($value)->totalCount();
            $data[$key] = ['value'=>$count, 'name'=>$value];
        } 
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));
    }


    public function actionCountAction() {
        
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





