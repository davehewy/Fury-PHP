<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	// =========== 
	// ! To avoid confusion this is literally a class to dev up and 
	// * retrieve config settings and return them as and how needed.   
	// =========== 
	
	class FURY_Core{
	
		function FURY_Core(){
		
			$this->config =& get_config();
		
		}
		
		function get_config_item($item,$index=''){
			
			if ($index == ''){	
				if ( ! isset($this->config[$item])){
					return FALSE;
				}
	
				$pref = $this->config[$item];
			}else{
				if ( ! isset($this->config[$index])){
					return FALSE;
				}
	
				if ( ! isset($this->config[$index][$item])){
					return FALSE;
				}
	
				$pref = $this->config[$index][$item];
			}	
			
			return $pref;
				
		}
		
		
		
	}