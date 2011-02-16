<?php
	
	# Set some defines and call our bootstrap
	
	define('EXT', '.php');
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', dirname(dirname(__FILE__)).'/scshop/');
	define('SYS', 'core/');
	
		
	// =========== 
	// ! Development environment globals   
	// =========== 
	
	define( 'DEVELOPMENT_ENVIRONMENT' , true);
	
	# True error reporting should be set here, to find problems right from the route.	
	
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}		
	
	# Require the bootstrap file
		 
	require_once (ROOT . DS . 'core' . DS . 'bootstrap.php');