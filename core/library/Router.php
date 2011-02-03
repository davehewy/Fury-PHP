<?php	

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	# The routing file to route request through to the correct controller and method.
	# Passing all additional paramaters through as a array.
	
	 class FURY_Router{
	 
		var $core;	
		var $routes 		= array();
		var $error_routes	= array();
		var $class			= '';
		var $method			= 'index';
		var $directory		= '';
		var $uri_protocol 	= 'auto';
		var $default_controller;
		var $scaffolding_request = FALSE; // Must be set to FALSE
	 	
	 	# Runs the route mapping function.
	 	
	 	function FURY_Router(){
	 		
		 	$this->core =& load_class('Core');
			$this->uri =& load_class('URI');
			$this->_set_routing();

	 	}
	 	
	 	function _set_routing(){
	 		 		
	 		if($routes = $this->core->get_config_item('routes')){
	 		
	 			# A default route is likely set lets check again.
	 			
	 			if($routes['default_controller'] && $routes['default_controller']!=''){
	 			
	 				# Yup theres a default controller set.
	 			
	 				$this->default_controller = strtolower($routes['default_controller']);
	 			
	 			}
	 		
	 		}
	 		
	 		# Fetch the entire URI String.
	 		$this->uri->_fetch_uri_string();
	 		 		
			# Is there a URI string? 
			# If not, the default controller specified 
			# in the "routes" file will be shown.
			
			if ($this->uri->uri_string == ''){
				if ($this->default_controller === FALSE){
					show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
				}
				
				if (strpos($this->default_controller, '/') !== FALSE){
					$x = explode('/', $this->default_controller);
	
					$this->set_class(end($x));
					$this->set_method('index');
					$this->_set_request($x);
				}else{
					$this->set_class($this->default_controller);
					$this->set_method('index');
					$this->_set_request(array($this->default_controller, 'index'));
				}
	
				// re-index the routed segments array so it starts with 1 rather than 0
				$this->uri->_reindex_segments();
				
				return;
			}
			
			unset($routes['default_controller']);
			
			// Do we need to remove the URL suffix?
			$this->uri->_remove_url_suffix();
			
			// Compile the segments into an array
			$this->uri->_explode_segments();
			
			// Parse any custom routing that may exist
			$this->_parse_routes();		
			
			// Re-index the segment array so that it starts with 1 rather than 0
			$this->uri->_reindex_segments();
			
	 	
	 	}
	 	
	 	// =========== 
	 	// ! Set the class name of the request   
	 	// =========== 
	 	
		function set_class($class){
			$this->class = $class;
		}
		
		// =========== 
		// ! Fetch the class name if any   
		// =========== 
		
		function fetch_class(){
			return $this->class;
		}
 		
 		// =========== 
 		// ! Set the method of the request   
 		// =========== 
 		
		function set_method($method){
			$this->method = $method;
		}
		
		// =========== 
		// ! Fetch method of the request   
		// =========== 
		
		function fetch_method(){
			if ($this->method == $this->fetch_class()){
				return 'index';
			}
	
			return $this->method;
		}
		
		
		// =========== 
		// ! Set the directory name   
		// =========== 
		
		function set_directory($dir){
			$this->directory = $dir.'/';
		}
		
		// =========== 
		// ! Fetch the directory name if any   
		// =========== 
	
		function fetch_directory(){
			return $this->directory;
		}
		
		
		// =========== 
		// ! Sets the Route from an array of URI segments 
		// ===========
		
		function _set_request($segments = array()){
			$segments = $this->_validate_request($segments);
			
			if (count($segments) == 0){
				return;
			}
							
			$this->set_class($segments[0]);
			
			if (isset($segments[1])){
				
				// A standard method request
				$this->set_method($segments[1]);
					
			}else{
				// This lets the "routed" segment array identify that the default
				// index method is being used.
				$segments[1] = 'index';
			}
			
			// Update our "routed" segment array to contain the segments.
			// Note: If there is no custom routing, this array will be
			// identical to $this->uri->segments
			$this->uri->rsegments = $segments;
		}
		
		// =========== 
		// ! Validates the supplied segments of the URI.
		// * Attempts to determine the path to the controller
		// =========== 
	
		function _validate_request($segments)
		{
			if (file_exists(APP_PATH.'controller/'.$segments[0].EXT))
			{
				return $segments;
			}
	
			if (is_dir(APP_PATH.'controller/'.$segments[0]))
			{
				$this->set_directory($segments[0]);
				$segments = array_slice($segments, 1);
	
				/* ----------- ADDED CODE ------------ */
	
				while(count($segments) > 0 && is_dir(APP_PATH.'controller/'.$this->directory.$segments[0]))
				{
					// Set the directory and remove it from the segment array
	    		$this->set_directory($this->directory . $segments[0]);
	    		$segments = array_slice($segments, 1);
				}
	
				/* ----------- END ------------ */
	
				if (count($segments) > 0)
				{
					if ( ! file_exists(APP_PATH.'controller/'.$this->fetch_directory().$segments[0].EXT))
					{
						show_404($this->fetch_directory().$segments[0]);
					}
				}
				else
				{
					$this->set_class($this->default_controller);
					$this->set_method('index');
	
					if ( ! file_exists(APP_PATH.'controller/'.$this->fetch_directory().$this->default_controller.EXT))
					{
						$this->directory = '';
						return array();
					}
	
				}
	
				return $segments;
			}
	
			$this->throw404($segments[0]);
		}	
	
	
/*
		function _validate_request($segments){
		// Does the requested controller exist in the root folder?
		if (file_exists(APP_PATH.'controller/'.$segments[0].EXT)){
			return $segments;
		}

		// Is the controller in a sub-folder?
		if (is_dir(APP_PATH.'controller/'.$segments[0])){		
			// Set the directory and remove it from the segment array
			$this->set_directory($segments[0]);
			$segments = array_slice($segments, 1);
			
			if (count($segments) > 0){
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists(APP_PATH.'controller/'.$this->fetch_directory().$segments[0].EXT))
				{
					$this->throw404($this->fetch_directory().$segments[0]);
				}
			}else{
			
				$this->set_class($this->default_controller);
				$this->set_method('index');
			
				// Does the default controller exist in the sub-folder?
				if ( ! file_exists(APP_PATH.'controller/'.$this->fetch_directory().$this->default_controller.EXT)){
					$this->directory = '';
					return array();
				}
			
			}

			return $segments;
		}

		// Can't find the requested controller...
		$this->throw404($segments[0]);
	}		
*/
	
	// =========== 
	// ! This function matches any routes that may exist in
	// * the config/routes.php file against the URI to
	// * determine if the class/method need to be remapped.   
	// =========== 
	
		function _parse_routes(){
			// Do we even have any custom routing to deal with?
			// There is a default scaffolding trigger, so we'll look just for 1
			if (count($this->routes) == 1){
				$this->_set_request($this->uri->segments);
				return;
			}
	
			// Turn the segment array into a URI string
			$uri = implode('/', $this->uri->segments);
	
			// Is there a literal match?  If so we're done
			if (isset($this->routes[$uri])){
				$this->_set_request(explode('/', $this->routes[$uri]));		
				return;
			}
					
			// Loop through the route array looking for wild-cards
			foreach ($this->routes as $key => $val){						
				// Convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
				
				// Does the RegEx match?
				if (preg_match('#^'.$key.'$#', $uri)){			
					// Do we have a back-reference?
					if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE){
						$val = preg_replace('#^'.$key.'$#', $val, $uri);
					}
				
					$this->_set_request(explode('/', $val));		
					return;
				}
			}
	
			// If we got this far it means we didn't encounter a
			// matching route so we'll set the site default route
			$this->_set_request($this->uri->segments);
		}
		 			 		 	
		
		function throw404($segments){
		
			# 404 redirect
			if(file_exists(APP_PATH . 'view' . DS . '404' .EXT)){
			
				include_once(APP_PATH . 'view' . DS . '404'.EXT);
			
			}else{
			
				include_once(ROOT . 'core' . DS . 'defaults' . DS . '404'.EXT);
			
			}				
		
		}
		
	 }