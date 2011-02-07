<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	# Some file to basically setup all of the basic variables.
	
	// =========== 
	// ! Version   
	// =========== 
	
	define('FURY_VERSION',	'1.0.0');
	
	// =========== 
	// ! Your application details   
	// =========== 
	
	$config['application']['version'] = '1.0.0';
	$config['application']['name'] = 'Street Crime';
	
	// =========== 
	// ! Base site URL
	// ! Typically this would be your base URL with a trailing slash
	// ! example http://website.com/ 	   
	// =========== 
	
	$config['base_url'] = "http://localhost:8888/Fury-PHP/";
	
	// =========== 
	// ! Assets folder location   
	// =========== 
	
	$config['assets_url'] = "assets/";
	
	// =========== 
	// ! Index page
	// This would typically be the index.php file, or whatever it is the name of your route controller.
	// If you are mod_rew_writing this out of your URLS leave it blank.   
	// =========== 
	
	$config['index_page'] = "";
	
	// =========== 
	// ! Auto load libraries
	// =========== 
	
	$config['auto_load'] = array("db");
	
	// =========== 
	// ! Auto load models   
	// =========== 
	
	$config['auto_load_models'] = array("test");
			
	// =========== 
	// ! Database Config   
	// =========== 
	
	$config['database'] = array();
	
	// =========== 
	// ! Default database   
	// =========== 
	
	$config['database']['default'] = array(
		
		"user" => "root",
		"password" => "root",
		"database" => "scshop",
		"host" => "localhost",
		"char_set" => "utf8",
		"dbcollat" => "utf8_general_ci"
		
	);
	
	// =========== 
	// ! Alternative databases can be listed here   
	// =========== 
	
	//For example: $config['database']['test'] = array();
	
	// =========== 
	// ! Auto connect  
	// =========== 
		
	$config['auto_db_connect'] = TRUE;	
	
	// =========== 
	// ! Antiscript config   
	// =========== 
	
	// Does this app require 
	
	$config['antiscript'] = '';
	
	// =========== 
	// ! Email delivering, do you want to use a defined client library
	// * as found in either core system files or your own apps library? 
	// =========== 
	
	// Available core apps are postmark, postmark_zend (postmark but using zend for delivering help)
	
	$config['email_app'] = "postmark_zend";
	
	// =========== 
	// ! If you are using Zend in any part of this application please define your include path   
	// =========== 
	
	$config['zend_local_include'] = "/usr/lib/php/libraries/zend-framework-1.11.2/";
	$config['remote_include'] = "/";
	
	// =========== 
	// ! Configure API Keys. Require by the likes of Postmark or what not.
	// =========== 
	
	$config['apikey'][$config['email_app']] = 'a9bf3b3f-4109-4bb9-8e71-3ba43aa54fa7';
	
	
	// =========== 
	// ! EMAIL Default Settings    
	// =========== 	
	$config['default_system_email'] = 'bytewire@bytewire.co.uk';
	$config['webmaster_email'] = 'webmaster@street-crime.com';
	$config['webmaster_sendfrom'] = 'Street Crime Mmorpg Game';
	$config['reply_to_email'] = 'feedback@street-crime.com';
	$config['reply_to_name'] = 'Street Crime Feedback';
	
	// =========== 
	// ! Basic routing   
	// =========== 
	
	define( 'APP_PATH' , ROOT .'application' .DS  );
	
	define( 'ASSETS_PATH' , ROOT .'assets' .DS );
	
	// =========== 
	// ! Uri protocol, determines how the application reads in
	// * The URI. You have the below options.
	// =========== 
	
	/*	
	| 'AUTO'			Default - auto detects
	| 'PATH_INFO'		Uses the PATH_INFO
	| 'QUERY_STRING'	Uses the QUERY_STRING
	| 'REQUEST_URI'		Uses the REQUEST_URI
	| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
	*/
	
	$config['uri_protocol'] = "AUTO";
	
	// =========== 
	// ! Allowed URI Characters   
	// =========== 
	
	$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\@-';	
	
	// =========== 
	// ! Add a suffix to all the FURY generated urls  
	// =========== 
	
	$config['url_suffix'] = "";
	
	// =========== 
	// ! Enable query strings? 	
	// * By default FURY uses search-engine friendly segment based URLs:
	// * example.com/who/what/where/
	// *
	// * You can optionally enable standard query string based URLs:
	// * example.com?who=me&what=something&where=here  
	// =========== 
	
	$config['enable_query_strings'] = false;
	
	// =========== 
	// ! Default Controller   
	// =========== 
	
	$config['routes']['default_controller'] = "home/login";
	
	// =========== 
	// ! Turn on and off Templating   
	// =========== 
	
	$config['auto_templating'] = TRUE;
	
	// =========== 
	// ! Templating this will be the default header and footer loaded into your template view   
	// =========== 
	
	# Specify the folder containing your default template.
	
	$config['default_template'] = "default";
	