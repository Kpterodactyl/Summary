<?php
 
namespace Gini\Controller\CGI;

class Summary extends \Gini\Controller\CGI\Layout {
	public function  __index() {
		$db = \Gini\Database::db();
		$login_count = those('base_point')->totalCount();
		$today = date("Y-m-d",time());
		$yesterday = date("Y-m-d",time()-86400);
		$tomorrow = date("Y-m-d",time()+86400);	
		$today_visit = those('base_point')->whose('dtstart')->isBetween($today,$tomorrow)->totalCount();
		$yd_visit = those('base_point')->whose('dtstart')->isBetween($yesterday,$today)->totalCount();	
		$today_user = $db->value("select count(distinct source_id) from base_point where dtstart between '".$today."' and '".$tomorrow."'");
		$yd_user = $db->value("select count(distinct source_id) from base_point where dtstart between '".$yesterday."' and '".$today."'");	
        $this->view->body = V('index', array(
        	'today_visit'=>$today_visit, 
        	'yd_visit' => $yd_visit,
        	'today_user' => $today_user,
        	'yd_user' => $yd_user));
        $this->view->title = '行为统计平台';	
	}
}

