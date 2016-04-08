<?php
 
namespace Gini\Controller\CGI;

class Summary extends Layout
{
	function actionSummary() {
		$db = \Gini\Database::db();
		$login_count = those('base_point')->totalCount();
		$today = date("Y-m-d",time());
		$yesterday = date("Y-m-d",time()-86400);
		$tomorrow = date("Y-m-d",time()+86400);		
	}

}

