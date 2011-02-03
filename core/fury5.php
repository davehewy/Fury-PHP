<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	// =========== 
	// ! You must include this for graciously operating with PHP 5   
	// =========== 


	class FURY_Base {
	
		private static $instance;
	
		public function FURY_Base(){
			self::$instance =& $this;
		}
	
		public static function &get_instance(){
			return self::$instance;
		}
	}
	
	function &get_instance(){
		return FURY_Base::get_instance();
	}