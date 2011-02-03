<?php	
		
	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	class FURY_Db{
	
		var $connections = array();
	
		 function FURY_Db(){
		 	
		 	// Use default connections and save them
		 	$this->core =& load_class('Core');
		 	
		 	# Auto connect if told too
		 	if($this->core->get_config_item('auto_db_connect')){
		 		$this->connectDefault();
		 	}
		 	
		 }
		 
		 function connectDefault(){
		 
		 	if($default = $this->core->get_config_item('default','database')){
		 	
		 		if($default['user'] && $default['password'] && $default['host']){
		 	
		 			$this->connections['default'] = mysql_connect($default['host'],$default['user'],$default['password']);
		 			if($this->connections['default']){
		 				
		 				# Select the database.
		 				$this->selectDb($default['database']);
		 				
		 			}else{
		 				show_error("Problem connecting to default database, please check your credentials over before trying again!");
		 			}
		 		
		 		}else{
		 			show_error("It looks like you want to auto connect but have not supplied all the database credentials, please do so and try again.");
		 		}
		 	
		 	}
		 	
		 }
		 
		 function selectDb($database){	
		 	if(!$db = mysql_select_db($database)){
		 		show_error("Unable to select the database, you provided.");
		 		return false;
		 	}
		 	return true;
		 }
		 
		 function query($query){
		 	$this->current_query = mysql_query($query);
		 	return $this;
		 }
		 
		 function row(){
		 	return mysql_fetch_assoc($this->current_query);
		 }
		 
		 function rows(){
		 	$array = array();
		 	while($r = mysql_fetch_assoc($this->current_query)){
		 		$array[] = $r;
		 	}
		 	return $array;
		 }
		 
		 # Returns the mysql_object as an object
		 function as_object(){
		 	return mysql_fetch_object($this->current_query);
		 }
		 
		 # Returns the mysql_object as an associative array
		 function as_assoc(){
		 	return mysql_fetch_assoc($this->current_query);
		 }
		 
		 # Returns the objects as an array
		 function as_array(){
		 	return mysql_fetch_array($this->current_query);
		 }
		 
		 # Returns a single value 
		 function get(){
		 	$assoc = mysql_fetch_assoc($this->current_query);
		 	if(is_array($assoc)){
			 	foreach($assoc as $k=>$v){
			 		return $v;
			 	}
		 	}
		 }
	
	}
		