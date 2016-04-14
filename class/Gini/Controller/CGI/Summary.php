<?php
 
namespace Gini\Controller\CGI;

class Summary extends \Gini\Controller\CGI\Layout {
	const MANUAL = 0;  //手动退出
	const AUTO = 1;    //自动登出

	static $WAY = [
		self::MANUAL => '手动登出',
		self::AUTO => '自动登出',
	]; 
	public function  __index($page_id=1) {
		$db = \Gini\Database::db();
		$login_count = those('base_point')->totalCount();
		$today = date("Y-m-d",time());
		$yesterday = date("Y-m-d",time()-86400);
		$tomorrow = date("Y-m-d",time()+86400);	
		$aweekago = date("Y-m-d",time()-604800);
		//今日访问量
		$today_visit = those('base_point')->whose('dtstart')->isBetween($today,$tomorrow)->totalCount();
		//昨日访问量
		$yday_visit = those('base_point')->whose('dtstart')->isBetween($yesterday,$today)->totalCount();
		//一周访问量
		$week_visit = those('base_point')->whose('dtstart')->isBetween($aweekago,$today)->totalCount();		
		//今日访客数
		$today_user = $db->value("select count(distinct source_id) from base_point where dtstart between '".$today."' and '".$tomorrow."'");
		//昨日访客数
		$yday_user = $db->value("select count(distinct source_id) from base_point where dtstart between '".$yesterday."' and '".$today."'");
		//一周访客数
		$week_user = $db->value("select count(distinct source_id) from base_point where dtstart between '".$aweekago."' and '".$today."'");
		//今日IP数
		$today_ip = $db->value("select count(distinct address) from base_point where dtstart between '".$today."' and '".$tomorrow."'");
		//昨日IP数
		$yday_ip =  $db->value("select count(distinct address) from base_point where dtstart between '".$yesterday."' and '".$today."'");	
		//一周IP数
		$week_ip =  $db->value("select count(distinct address) from base_point where dtstart between '".$aweekago."' and '".$today."'");	
		//今日平均访问时长
        $today_avg_time = gmstrftime("%M:%S",$this->avgTime($today,$tomorrow)); 
        //昨日平均访问时长
        $yday_avg_time = gmstrftime("%M:%S",$this->avgTime($yesterday,$today));
        //一周平均访问时长
        $week_avg_time = gmstrftime("%M:%S",$this->avgTime($aweekago,$today));
        //登录用户统计
        $base_login = a('base_point');
        foreach ($base_login as $value) {
				
			$login->source_name = $value['source_name'];
			$login->province = $value['province'];
			$login->city = $value['city'];
			$login->member_type = $value['member_type'];
			$login->address = $value['address'];
			$login->browser = $value['browser'];
			$login->version = $value['version'];
			$login->OS_type = $value['OS_type'];
			$login->dtstart = date("Y-m-d H:i:s",$value['dtstart']);
			$logins[] = $login;
		}
        //登出方式统计
		for($i=0;$i<=1;$i++){
			$logout = array();
			$logout['name'] = Summary::$WAY[$i];
			$logout['times'] = those('base_point')->whose('way')->is($i)->totalCount();
			$logout['percentage'] = round(($logout['times']/$login_count)*100).'%';
			$logout['time'] = gmstrftime("%M:%S",$this->accessdurationByWay($i));
			if ($logout['time'] == 0) {
				$logout['time'] = "--";
			}
		
			$logouts[] = $logout;
		}
		//用户身份
		$student_num = those('base_point')->whose('member_type')->isBetween(0,10)->totalCount();	
		$teacher_num = those('base_point')->whose('member_type')->isBetween(10,20)->totalCount();
		$other_num = those('base_point')->whose('member_type')->isGreaterThanOrEqual(20)->totalCount();
        
        $student_s =  round($student_num/$login_count,2);
        $teacher_s = round($teacher_num/$login_count,2);
        $other_s = round($other_num/$login_count,2);
       
        $student_scale =  round($student_s*100).'%';
        $teacher_scale = round($teacher_s*100).'%';
        $other_scale = round($other_s*100).'%';
        
		$data = array($stu_num,$tec_num,$else_num);
        $page1 = $this->page($page_id,10,$login_count); 

        $this->view->body = V('index', array(
        	'today_visit'=>$today_visit, 
        	'yday_visit' => $yday_visit,
        	'week_visit' =>$week_visit,
			'today_user' => $today_user,
        	'yday_user' => $yday_user,
        	'week_user' =>$week_user,
        	'today_ip' => $today_ip,
        	'yday_ip' => $yday_ip,
        	'week_ip' =>$week_ip,
        	'today_avg_time' => $today_avg_time,
        	'yday_avg_time' => $yday_avg_time,
        	'week_avg_time' =>$week_avg_time,
        	'student_num' =>$student_num,
        	'teacher_num' =>$teacher_num,
        	'other_num' =>$other_num,
        	'student_scale' =>$student_scale,
        	'teacher_scale' =>$teacher_scale,
        	'other_scale' =>$other_scale,
        	'total_page' => $page1['total_page'],
        	'login' => $logins,
        	'logout' => $logouts
        	));
        $this->view->title = '行为统计平台';	
	}
	private function avgTime($sday,$eday) {
		global $db;
		$login = those('base_point')->whose('dtstart')->isBetween($sday,$eday)->andWhose('way')->is('0');
		$login_times = those('base_point')->totalCount();

		$total_Dtime = "--";			
		foreach($login as $value){
			$Dtime = $value->keeptime;
			$total_Dtime+=$Dtime;
		}
		$averagetime = ceil($total_Dtime/$login_times);
		return 	$averagetime;
		
	}
		//通过退出方式访问时长
	private function accessdurationByWay($way){
		
		$login = those('base_point')->whose('way')->is($way);
		$login_times = those('base_point')->whose('way')->is($way)->totalCount();
		$total_Dtime = 0;
		if($way == Summary::AUTO){
			return 0;
		}else{
			foreach($login as $value){
			$Dtime = $value->keeptime;
			$total_Dtime+=$Dtime;
			}
		
			$averagetime = ceil($total_Dtime/$login_times);

			return $averagetime;
		}
		
	}
	private function page($page,$num,$total) {
		if (is_numeric($num)) {
			$start = $page*10-10;
			$end = $page*10;
			//每页显示的数量
			$total_page = ceil($total / $num);
			return [
				'start' => $start,
				'end' => $end,
				'total_page' => $total_page
			];

			
		}
		
	}

}

