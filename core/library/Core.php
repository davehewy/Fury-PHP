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
		
		// =========== 
		// ! Fetch a config item from the config file   
		// =========== 
		
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
		

		
		// =========== 
		// ! Return the site url based on defined set of params in the config   
		// =========== 
		
		function site_url($uri = ''){
			if (is_array($uri)){
				$uri = implode('/', $uri);
			}
	
			if ($uri == ''){
				return $this->slash_item('base_url').$this->get_config_item('index_page');
			}else{
				$suffix = ($this->get_config_item('url_suffix') == FALSE) ? '' : $this->get_config_item('url_suffix');
				return $this->slash_item('base_url').$this->slash_item('index_page').trim($uri, '/').$suffix; 
			}
		}
		
		// =========== 
		// ! Add a slash after the end of a parameter given   
		// =========== 
		
		function slash_item($item){
			if ( ! isset($this->config[$item])){
				return FALSE;
			}
	
			$pref = $this->config[$item];
	
			if ($pref != '' && substr($pref, -1) != '/'){	
				$pref .= '/';
			}
	
			return $pref;
		}		
				
	}