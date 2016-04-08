<?php

namespace Gini\Controller\API;

class Summary extends \Gini\Controller\API{

	public function actionPoint($point_data){
		error_log("+++++++++++++++++");
		if(!is_array($point_data)){
			throw \Gini\IoC::construct('\Gini\API\Exception', '异常参数传入', 1001);
		}
		$sessionid = $point_data['sid'];
		$point = a('base_point', ['sid' => $sessionid]);
		if($point->id){
			if (strtotime($value['dtend']) != $point->dtend) {
				$point->dtend = date("Y-m-d H:i:s",$value['dtend']);
				$point->save();
			}		 
		}
		else {
            $point = a('base_point');
		    $point->user = $point_data['user'];
		    $point->bid = $point_data['bid'];
		    $point->sid = $point_data['sid'];
			$point->source_name = $point_data['source_name'];
			$point->source_id = $point_data['source_id'];
			$point->sessionid = $point_data['sid'];
			$point->gapper_id = $point_data['gapper_id'];
			$point->member_type = $point_data['member_type'];
			$point->uid = $point_data['uid'];
			$point->address = $point_data['address'];
			$point->province = $point_data['province'];
			$point->city = $point_data['city'];
			$point->browser = $point_data['browser'];
			$point->version = $point_data['version'];
			$point->signout_way = $point_data['signout_way'];
			$point->OS_type = $point_data['OS_type'];
			$point->dtstart = date("Y-m-d H:i:s",$point_data['dtstart']);
			$point->dtend = date("Y-m-d H:i:s",$point_data['dtend']);
			$point->keeptime = $point_data['keeptime'];
			$point->way = $point_data['way'];
			$ret = $point->save();	
			error_log($ret ? 'O' : 'x');
		}
					 
	}

	public function actionAction($action_data) {
		error_log("=========================");
		error_log(print_r($action_data,true));
		if(!array($action_data)){
			throw \Gini\IoC::construct('\Gini\API\Exception', '异常参数传入', 1001);
		}
		$source_id = $action_data['source_id'];
		$user_id = $action_data['uid'];
		$ctime = $action_data['ctime'];
		//$action = a('base_action', ['source_id' => $source_id], ['uid' => $user_id], ['ctime' => $ctime]);
		$action = a('base_action', [
			    'source_id' => $source_id,
			    'uid' => $user_id,
			    'ctime' => $ctime,
			]);
		if ($action->id) {
            error_log("data already exist!!");
			return;
		}
		else {
			$action = a('base_action');
			$action->source_id = $action_data['source_id'];
			$action->action = $action_data['action'];
			$action->module = $action_data['module'];
			$action->ctime = date("Y-m-d H:i:s",$action_data['ctime']);
			$action->uid = $action_data['uid'];
			$action->gapper_id = $action_data['gapper_id'];
			$action->bid = $action_data['bid'];
			$ret = $action->save();
			error_log($ret ? 'O' : 'x');
	    }
	}	
}

