<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	class FURY_Uri{
	
	var	$keyval	= array();
	var $uri_string;
	var $segments		= array();
	var $rsegments		= array();
	
	
		function FURY_Uri(){
			$this->core =& load_class('Core');
		}
		
		function _fetch_uri_string(){
		
			if (strtoupper($this->core->get_config_item('uri_protocol')) == 'AUTO'){
				// If the URL has a question mark then it's simplest to just
				// build the URI string from the zero index of the $_GET array.
				// This avoids having to deal with $_SERVER variables, which
				// can be unreliable in some environments
				if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != ''){
					$this->uri_string = key($_GET);
					return;
				}
	
				// Is there a PATH_INFO variable?
				// Note: some servers seem to have trouble with getenv() so we'll test it two ways
				$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
				if (trim($path, '/') != '' && $path != "/".SELF){
					$this->uri_string = $path;
					return;
				}
	
				// No PATH_INFO?... What about QUERY_STRING?
				$path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
				
				if (trim($path, '/') != ''){
					$this->uri_string = $path;
					return;
				}
	
				// No QUERY_STRING?... Maybe the ORIG_PATH_INFO variable exists?
				$path = str_replace($_SERVER['SCRIPT_NAME'], '', (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO'));
				if (trim($path, '/') != '' && $path != "/".SELF){
					// remove path and script information so we have good URI data
					$this->uri_string = $path;
					return;
				}
	
				// We've exhausted all our options...
				$this->uri_string = '';
			}else{
				$uri = strtoupper($this->core->get_config_item('uri_protocol'));
	
				if ($uri == 'REQUEST_URI'){
					$this->uri_string = $this->_parse_request_uri();
					return;
				}
	
				$this->uri_string = (isset($_SERVER[$uri])) ? $_SERVER[$uri] : @getenv($uri);
			}
	
			// If the URI contains only a slash we'll kill it
			if ($this->uri_string == '/'){
				$this->uri_string = '';
			}
		}
		
		// =========== 
		// ! Due to the way REQUEST_URI works it usually contains path info
	 	// * that makes it unusable as URI data.  We'll trim off the unnecessary
	 	// * data, hopefully arriving at a valid URI that we can use.
		// =========== 	
			
		function _parse_request_uri(){
			if ( ! isset($_SERVER['REQUEST_URI']) OR $_SERVER['REQUEST_URI'] == ''){
				return '';
			}
	
			$request_uri = preg_replace("|/(.*)|", "\\1", str_replace("\\", "/", $_SERVER['REQUEST_URI']));
	
			if ($request_uri == '' OR $request_uri == SELF){
				return '';
			}
	
			$fc_path = FCPATH.SELF;
			if (strpos($request_uri, '?') !== FALSE){
				$fc_path .= '?';
			}
	
			$parsed_uri = explode("/", $request_uri);
	
			$i = 0;
			foreach(explode("/", $fc_path) as $segment){
				if (isset($parsed_uri[$i]) && $segment == $parsed_uri[$i]){
					$i++;
				}
			}
	
			$parsed_uri = implode("/", array_slice($parsed_uri, $i));
	
			if ($parsed_uri != ''){
				$parsed_uri = '/'.$parsed_uri;
			}
	
			return $parsed_uri;
		}	
		
		// =========== 
		// ! Filter segments for malacious attacks
		// =========== 	

		function _filter_uri($str){
			if ($str != '' && $this->core->get_config_item('permitted_uri_chars') != '' && $this->core->get_config_item('enable_query_strings') == FALSE){
			
				if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($this->core->get_config_item('permitted_uri_chars'), '-'))."]+$|i", $str)){
					show_error('The URI you submitted has disallowed characters.', 400);
				}
			}
	
			// Convert programatic characters to entities
			$bad	= array('$', 		'(', 		')',	 	'%28', 		'%29');
			$good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');
	
			return str_replace($bad, $good, $str);
		}
		
		// =========== 
		// ! Remove the url suffix   
		// =========== 
		
		function _remove_url_suffix(){
			if  ($this->core->get_config_item('url_suffix') != ""){
				$this->uri_string = preg_replace("|".preg_quote($this->core->get_config_item('url_suffix'))."$|", "", $this->uri_string);
			}
		}
		
		// =========== 
		// ! Explode the URI Segments. The individual segments will
	 	// * be stored in the $this->segments array  
		// =========== 
		
		function _explode_segments(){
			foreach(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val){
				// Filter segments for security
				$val = trim($this->_filter_uri($val));
	
				if ($val != ''){
					$this->segments[] = $val;
				}
			}
		}
		
		
		// =========== 
		// ! Re-index segments   
		// ===========
		
		function _reindex_segments(){
			array_unshift($this->segments, NULL);
			array_unshift($this->rsegments, NULL);
			unset($this->segments[0]);
			unset($this->rsegments[0]);
		}
		
		 // =========== 
		 // ! Fetch a URI Segment    
		 // =========== 				

		function segment($n, $no_result = FALSE){
			return ( ! isset($this->segments[$n])) ? $no_result : $this->segments[$n];
		}
		
		// =========== 
		// ! Fetch a URI routed segment   
		// =========== 
					
		function rsegment($n, $no_result = FALSE){
			return ( ! isset($this->rsegments[$n])) ? $no_result : $this->rsegments[$n];
		}
		
		// =========== 
		// ! URI TO Assoc array, generate a key value pair from the URI String
		// =========== 					
		
		function uri_to_assoc($n = 3, $default = array()){
		 	return $this->_uri_to_assoc($n, $default, 'segment');
		}
		
		// =========== 
		// ! Exactly the same as above using the re-routed segment   
		// =========== 
		
		function ruri_to_assoc($n = 3, $default = array()){
		 	return $this->_uri_to_assoc($n, $default, 'rsegment');
		}
		
		// =========== 
		// ! Generate a key value pair from the URI string or Re-routed URI string   
		// =========== 
		
		function _uri_to_assoc($n = 3, $default = array(), $which = 'segment'){
			if ($which == 'segment'){
				$total_segments = 'total_segments';
				$segment_array = 'segment_array';
			}else{
				$total_segments = 'total_rsegments';
				$segment_array = 'rsegment_array';
			}
	
			if ( ! is_numeric($n)){
				return $default;
			}
	
			if (isset($this->keyval[$n])){
				return $this->keyval[$n];
			}
	
			if ($this->$total_segments() < $n){
				if (count($default) == 0){
					return array();
				}
	
				$retval = array();
				foreach ($default as $val){
					$retval[$val] = FALSE;
				}
				return $retval;
			}
	
			$segments = array_slice($this->$segment_array(), ($n - 1));
	
			$i = 0;
			$lastval = '';
			$retval  = array();
			foreach ($segments as $seg){
				if ($i % 2){
					$retval[$lastval] = $seg;
				}else{
					$retval[$seg] = FALSE;
					$lastval = $seg;
				}
	
				$i++;
			}
	
			if (count($default) > 0){
				foreach ($default as $val){
					if ( ! array_key_exists($val, $retval)){
						$retval[$val] = FALSE;
					}
				}
			}
	
			// Cache the array for reuse
			$this->keyval[$n] = $retval;
			return $retval;
		}

		// =========== 
		// ! Generate a URI from an associative array   
		// =========== 

		function assoc_to_uri($array){
			$temp = array();
			foreach ((array)$array as $key => $val){
				$temp[] = $key;
				$temp[] = $val;
			}
	
			return implode('/', $temp);
		}

		// =========== 
		// ! Fetch a segment and add a trailing slash   
		// =========== 

		function slash_segment($n, $where = 'trailing'){
			return $this->_slash_segment($n, $where, 'segment');
		}
		
		// =========== 
		// ! Fetch a re-routed URI Segment and add a trailing slash
		// =========== 

		function slash_rsegment($n, $where = 'trailing'){
			return $this->_slash_segment($n, $where, 'rsegment');
		}
		
		// =========== 
		// ! Fetch a URI Segment and add a trailing slash - helper function  
		// =========== 
		function _slash_segment($n, $where = 'trailing', $which = 'segment'){
			if ($where == 'trailing'){
				$trailing	= '/';
				$leading	= '';
			}elseif ($where == 'leading'){
				$leading	= '/';
				$trailing	= '';
			}else{
				$leading	= '/';
				$trailing	= '/';
			}
			return $leading.$this->$which($n).$trailing;
		}
		
		// =========== 
		// ! Return segment Array   
		// =========== 
		
		function segment_array(){
			return $this->segments;
		}
		
		// =========== 
		// ! Return a re-routed segment Array   
		// =========== 
		
		function rsegment_array(){
			return $this->rsegments;
		}

		// =========== 
		// ! Return the total count of segments   
		// =========== 

		function total_segments(){
			return count($this->segments);
		}
		
		// =========== 
		// ! Return the total count of re-routed segments   
		// =========== 
		
		function total_rsegments(){
			return count($this->rsegments);
		}
		
		// =========== 
		// ! Fetch the entire URI string   
		// =========== 
		
		function uri_string(){
			return $this->uri_string;
		}
		
		// =========== 
		// ! Fetch the entire re-routed URI String   
		// =========== 
		
		function ruri_string(){
			return '/'.implode('/', $this->rsegment_array()).'/';
		}
				
	}