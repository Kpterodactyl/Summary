<?php

namespace Gini\ORM;

class base_point extends Object
{
	public $source_id = "int";    
	public $source_name = "string:120";   
	public $bid = "int";
	public $gapper_id = "int";    
	public $uid = "int";
	public $member_type = "int";  
	public $address = "string:120";
	public $province = "string:120";
	public $city = "string:120";
	public $sid = "string:120";
	public $os = "string:120";
	public $browser = "string:120";
	public $version = "string:120";
	public $dtstart = "datetime";
	public $device = "string:120";
	public $device_type = "string:120";
	public $dtend = "datetime";
	public $keeptime = "int";
	public $way = "int";	
	
	protected static $db_index = [
		'unique:sid',
	];
}
