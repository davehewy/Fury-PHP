<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	class FURY_Validation{
		
		var $FURY;
		var $_fury_error_string = '';
		var $_fury_error_array = array();
	
		function Fury_Validation(){
			$this->FURY =& get_instance();
			$this->load =& load_class("Loader");
		}
		
		// =========== 
		// ! Useful checkdata functions to sanatise inputs from various places 
		// 1. Checks for a number between 1-30 characters in length
		// 2. Adds slashes, strips_tags and trims
		// 3. Any number and or alpha numeric character string between 1-50 in length  
		// 4. Any number and or alpha numeric character, including -_!| and space string between 1-50 in length
		// 5. Same as above but 1-200 in length
		// 6. Same as above but 1-100 in length and including a \ symbol 
		// =========== 
		
		function checkdata($data,$type){
			
			$data = trim(stripslashes($data));
		
			switch($type){
				case 1: 
						if(!preg_match('/^[0-9]{1,30}+$/',$data)){
							return false;
						} else {
							return $data;
						}
						break;
				case 2:
						return addslashes(strip_tags(trim($data)));
						break;
				case 3:
						if(!preg_match('/^[A-Za-z0-9]{1,50}+$/',$data)){
							return false;
						} else {
							return $data;
						}
						break;
				case 4: 
						if(!preg_match('/^[-A-Za-z0-9_!| ]{1,50}+$/',$data)){
							return false;
						} else {
							return $data;
						}
						break;
				case 5:
						if(!preg_match('/^[-A-Za-z0-9_!| ]{1,200}+$/',$data)){
							return false;
						} else {
							return $data;
						}
						break;
				case 6: 
						if(!preg_match('/^[-A-Za-z0-9_!|\'" ]{1,100}+$/',$data)){
							return false;
						} else {
							return $data;
						}
						break;
			}
		}
		
		// =========== 
		// ! function character name checker   
		// check characters, profanity, filter, 3-15 characters long.
		// =========== 
		
		function characterName($str){
		$this->load->library("Profanity");
			if(preg_match("/^[a-zA-Z0-9_|-\s]{3,16}$/", $str)){
				if($this->profanity->getScore($str)<=0){
					return true;
				}else{
					$this->_fury_error_string = gettext("Your username contains words which we do not allow as a username, try changing it to something else!");
				}
			}else{
				$this->_fury_error_string = gettext("The username is invalid it may only contain letters, numbers, spaces, -_| and must be between 3 and 15 characters in length.");
			}
		}
       
		// =========== 
		// ! Validate a url   
		// =========== 
		
		function validate_url($str){
			if(!preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i",$str)) {
				return false;
			}
			return true;		
		}
		
		// =========== 
		// ! Make a field required and do not let it be false.   
		// =========== 
		
		function required($str){
			if ( ! is_array($str)){
				return (trim($str) == '') ? FALSE : TRUE;
			}else{
				return ( ! empty($str));
			}
		}
		
		// =========== 
		// ! Make sure two fields match   
		// =========== 
		
		function matches($str, $field){
			if ( ! isset($_POST[$field])){
				return FALSE;				
			}
			
			$field = $_POST[$field];
	
			return ($str !== $field) ? FALSE : TRUE;
		}		
		
		// =========== 
		// ! Prep for form   
		// =========== 
		
		function prep_for_form($data = ''){
			if (is_array($data)){
				foreach ($data as $key => $val){
					$data[$key] = $this->prep_for_form($val);
				}
				
				return $data;
			}
			
			if ($this->_safe_form_data == FALSE OR $data == ''){
				return $data;
			}
	
			return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
		}		
		
		// =========== 
		// ! Prep Url   
		// ===========
		
		function prep_url($str = ''){
			if ($str == 'http://' OR $str == ''){
				$_POST[$this->_current_field] = '';
				return;
			}
			
			if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://'){
				$str = 'http://'.$str;
			}
			
			$_POST[$this->_current_field] = $str;
		}
			 
		
		// =========== 
		// ! Strip image tags   
		// =========== 
		
		function strip_image_tags($str){

			$str = preg_replace("#<img\s+.*?src\s*=\s*[\"'](.+?)[\"'].*?\>#", "\\1", $str);
			$str = preg_replace("#<img\s+.*?src\s*=\s*(.+?).*?\>#", "\\1", $str);
				
			return $str;

		}		
		
		// =========== 
		// ! Exact length   
		// =========== 
		
		function exact_length($str, $val){
			if (preg_match("/[^0-9]/", $val)){
				return FALSE;
			}
		
			if (function_exists('mb_strlen')){
				return (mb_strlen($str) != $val) ? FALSE : TRUE;		
			}
	
			return (strlen($str) != $val) ? FALSE : TRUE;
		}		
		
		// =========== 
		// ! Valid email   
		// =========== 
		
		function valid_email($str){
			return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
		}
		
		// =========== 
		// ! Are is this list valid emails
		// =========== 		
		
		function valid_emails($str){
			if (strpos($str, ',') === FALSE){
				return $this->valid_email(trim($str));
			}
			
			foreach(explode(',', $str) as $email){
				if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE){
					return FALSE;
				}
			}
			
			return TRUE;
		}
		
		
		// =========== 
		// ! Valid Ip   
		// =========== 
		
		function valid_ip($ip){

			$ip_segments = explode('.', $ip);
	
			// Always 4 segments needed
			if (count($ip_segments) != 4)
			{
				return FALSE;
			}
			// IP can not start with 0
			if ($ip_segments[0][0] == '0')
			{
				return FALSE;
			}
			// Check each segment
			foreach ($ip_segments as $segment)
			{
				// IP segments must be digits and can not be 
				// longer than 3 digits or greater then 255
				if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
				{
					return FALSE;
				}
			}
	
			return TRUE;
		
		}
		
		// =========== 
		// ! Any number, upper or lower case lettering
		// =========== 
		
		function alpha_numeric_tight(){
			return ( ! preg_match("/^([a-zA-Z0-9])+$/i", $str)) ? FALSE : TRUE;
		}
		
		// =========== 
		// ! Alpha   
		// =========== 
		
		function alpha($str){
			return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
		}
		
		// =========== 
		// ! Alpha upper case and space   
		// =========== 
		
		function alpha_loose($str){
			return ( ! preg_match("/^([a-zA-Z ])+$/i", $str)) ? FALSE : TRUE;
		}		
		
		// =========== 
		// ! Alpha Numeric only   
		// ===========
		 
		function alpha_numeric($str){
			return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
		}
		  
		// =========== 
		// ! Alpha dash only   
		// =========== 
		
		function alpha_dash($str){
			return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
		}		
		
		// =========== 
		// ! Numeric   
		// =========== 
		
		function numeric($str){
			return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
		}
		
		// =========== 
		// ! Is Numeric   
		// =========== 
		
		function is_numeric($str){
			return ( ! is_numeric($str)) ? FALSE : TRUE;
		} 
		
		// =========== 
		// ! Is integer   
		// =========== 
		
		function integer($str){
			return (bool)preg_match( '/^[\-+]?[0-9]+$/', $str);
		}		
		
		// =========== 
		// ! Natural number  
		// =========== 
		
		function is_natural($str){   
	   		return (bool)preg_match( '/^[0-9]+$/', $str);
		}
		
		// =========== 
		// ! Is natural no zero   
		// =========== 

		function is_natural_no_zero($str){   
			if ( ! preg_match( '/^[0-9]+$/', $str)){
				return FALSE;
			}
		
			if ($str == 0){
				return FALSE;
			}
	
			return TRUE;
		}
		
		// =========== 
		// ! Valid base 64    
		// =========== 
		
		function valid_base64($str){
			return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
		}		
		
		
		// =========== 
		// ! Return a single error   
		// =========== 
	
		function retErrors(){
			return $this->_fury_error_string;
		}
	
	}
		
		