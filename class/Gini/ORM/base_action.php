<?php

namespace Gini\ORM;

class base_action extends Object
{
	public $source_id = "string:120"; 
	public $uid = "int";
	public $gapper_id = "int";
	public $bid = "int";
	public $action = "string:120";
	public $module = "string:120";
	public $ctime = "datetime";   
	
	protected static $db_index = [
		'source_id',
		'uid',
		'gapper_id',
		"bid",
		'action',
		'module',
		'ctime',
		
	];
}
