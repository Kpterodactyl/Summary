<?php

namespace Gini\Controller\CGI\AJAX;

class Summary extends \Gini\Controller\CGI {
    public function actionOverview(){
    		$today = date("Y-m-d",time());
	    	$yday1 = date("Y-m-d",time()-86400);
	    	$yday2 = date("Y-m-d",time()-86400*1);
	    	$yday3 = date("Y-m-d",time()-86400*2);
            $yday4 = date("Y-m-d",time()-86400*3);
            $yday5 = date("Y-m-d",time()-86400*4);
            $yday6 = date("Y-m-d",time()-86400*5);
            $yday7 = date("Y-m-d",time()-86400*6);

	    	$today_d = $this->every_day_visit($today);
	    	$yday1_d = $this->every_day_visit($yday1);
            $yday2_d = $this->every_day_visit($yday2);
            $yday3_d = $this->every_day_visit($yday3);
            $yday4_d = $this->every_day_visit($yday4);
            $yday5_d = $this->every_day_visit($yday5);
            $yday6_d = $this->every_day_visit($yday6);
            $yday7_d = $this->every_day_visit($yday7);
	    	$data = [
    			'today'=>$today_d,
    			'yday1'=>$yday1_d,
    			'yday2'=>$yday2_d,
    			'yday3'=>$yday3_d,
    			'yday4'=>$yday4_d,
    			'yday5'=>$yday5_d,
    			'yday6'=>$yday6_d,
    			'yday7'=>$yday7_d,
    		];
    		return \Gini\IoC::construct('\Gini\CGI\Response\JSON', json_encode($data));

	}
	  //每天的访问量
    private function every_day_visit($datetime){
    	$db = \Gini\Database::db();
    	$dd = $db->query("select day(dtstart) as day, count(*) as count from base_point where DATE_FORMAT(dtstart,'%Y-%m-%d') = '".$datetime."'  group by day(dtstart) ");
    	$data=$dd->count;
    	return $data;

	}


}
