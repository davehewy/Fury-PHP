<?php

	# This is the file which is responsible for routing out all of our requests.
	
	# Lets have our nice friendly config file do some work.
	
	require_once(ROOT . 'application' .DS . 'config' . DS . 'config' .EXT);
		
	// =========== 
	// ! Show some errors out of our framework   
	// =========== 
	
	function show_error($message, $status_code = 500){
		$error =& load_class('Exceptions');
		echo $error->show_error('An Error Was Encountered', $message, 'error_general', $status_code);
		exit;
	}
	
	// =========== 
	// ! Show 404 through exceptions controller   
	// =========== 
	
	function show_404($page = ''){
		$error =& load_class('Exceptions');
		$error->show_404($page);
		exit;
	}	
	
	// =========== 
	// ! Fetch config file   
	// =========== 
	
	function &get_config(){
	
		global $config;
	
		static $main_conf;
		if(! isset($main_conf)){
		
			if ( ! isset($config) OR ! is_array($config)){
				exit('Your config file does not appear to be formatted correctly.');
			}
			
			$main_conf[0] = & $config;

		}
		return $main_conf[0];	
	}
	
	// =========== 
	// ! Class register
	// * This function acts as a singleton.  If the requested class does not
	// * exist it is instantiated and set to a static variable.  If it has
	// * previously been instantiated the variable is returned.  
	// =========== 

	function &load_class($class, $instantiate = TRUE){
		static $objects = array();
	
		// Does the class exist?  If so, we're done...
		if (isset($objects[$class])){
			return $objects[$class];
		}
	
		// Load the native file from the libraries core.

		if (file_exists(ROOT . 'core' . DS .'library'. DS . $class . EXT)){
			require(ROOT . 'core' . DS .'library'. DS . $class . EXT);
			$is_subclass = FALSE;
		}else{
			require(ROOT . 'core' . DS .'library' . DS . $class . EXT);
			$is_subclass = FALSE;
		}
	
		if ($instantiate == FALSE){
			$objects[$class] = TRUE;
			return $objects[$class];
		}
	
		$name = ($class != 'Controller') ? 'FURY_'.$class : $class;
	
		$objects[$class] =& instantiate_class(new $name());
		return $objects[$class];
	}
	
	// =========== 
	// ! Instantiate a class
	// * Returns a class object by reference use by load_class()   
	// =========== 

	function &instantiate_class(&$class_object){
		return $class_object;
	}	

	// =========== 
	// ! Load in some default class's
	// * Router (used for simply routing the requests)
	// * URI (used for helping with the URI division and returning certain segments of a given URI   
	// =========== 


	$ROUTE =& load_class('Router');
	$URI =& load_class('URI');
	$CF =& load_class("Core");
	$OUT =& load_class("Output");
	$THEMES =& load_class('Templating');
	
	require(ROOT.'core'.DS.'fury5.php');
	
	# Now lets get the base controller in there.
	
	load_class('Controller', FALSE);
	
	// Load the local application controller
	// Note: The Router class automatically validates the controller path.  If this include fails it 
	// means that the default controller in the Routes.php file is not resolving to something valid.
		
	if (!file_exists( APP_PATH . 'controller' .DS. $ROUTE->fetch_directory().$ROUTE->fetch_class().EXT)){
		
		show_error('Unable to load your default controller.  Please make sure the controller specified in your Routes.php file is valid.');
		
	}
	
	# Lets include the correct controller
	
	include(APP_PATH . 'controller' . DS .$ROUTE->fetch_directory().$ROUTE->fetch_class().EXT);
	
	# Now lets perform the requested actions.
	
	$class  = $ROUTE->fetch_class();
	$method = $ROUTE->fetch_method();

	
	if ( ! class_exists($class)
		OR $method == 'controller'
		OR strncmp($method, '_', 1) == 0
		OR in_array(strtolower($method), array_map('strtolower', get_class_methods('Controller')))
		)
	{
		show_404("{$class}/{$method}");
	}
	
	$FURY = new $class();
	
	// Is there a "remap" function?
	if (method_exists($FURY, '_remap')){
		$FURY->_remap($method);
	}else{
		// is_callable() returns TRUE on some versions of PHP 5 for private and protected
		// methods, so we'll use this workaround for consistent behavior
		if ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($FURY))))
		{
			
			# Okay so we know the URI might not be valid but 
			# lets check if the index is expecting extra params
			
			show_404("{$class}/{$method}");
					
			
		}

		// Call the requested method.
		
		// Any URI segments present (besides the class/function) will be passed to the method for convenience
		call_user_func_array(array(&$FURY, $method), array_slice($URI->rsegments, 2));
		
	}
	
	