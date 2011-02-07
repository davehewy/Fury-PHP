<?php	
	
	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	
	class FURY_Loader{
	
		// All these are set automatically. Don't mess with them.
		var $_fury_ob_level;
		var $_fury_view_path	= '';
		var $_fury_is_php5		= FALSE;
		var $_fury_is_instance 	= FALSE; // Whether we should use $this or $FURY =& get_instance()
		var $_fury_cached_vars	= array();
		var $_fury_classes		= array();
		var $_fury_loaded_files	= array();
		var $_fury_models		= array();
		var $_fury_helpers		= array();
		var $_fury_plugins		= array();
		var $_fury_varmap		= array('unit_test' => 'unit', 'user_agent' => 'agent');
		
		function FURY_Loader(){
		
			$this->_fury_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
			$this->_fury_view_path = APP_PATH.'view/';
			$this->_fury_ob_level  = ob_get_level();
		}
		
		// =========== 
		// ! This module lets users load class's as "libraries"
		// * Its designed to be used with Controllers;   
		// =========== 
		
		function library($library = '', $params = NULL, $object_name = NULL){
		
			# Nothing given
			
			if($library==''){
				return false;
			}
			
			# Check for no params
			
			if(!is_null($params) AND !is_array($params)){
				$params = NULL;
			}
			
			# If the libraray is an array load them all in.
			
			if(is_array($library)){
			
				foreach($library as $class):
					$this->_fury_load_class($class,$params,$object_name);
				endforeach;
			
			}else{
										
				$this->_fury_load_class($library,$params,$object_name);
			
			}
			
			$this->_fury_assign_to_models();
		
		}
		
		// =========== 
		// ! Model loader, lets users load and instantiate models from controllers.   
		// =========== 
		
		function model($model, $name = '', $db_conn = FALSE){
		
			if(is_array($model)){
			
				foreach($model as $m):
					$this->model($m);
				endforeach;
				
				return;
			
			}
			
			if($model==''){
				return;
			}
			
			# Is the model perhaps in a subfolder?
			
			if(strpos($model, DS)===FALSE){
				$path = '';
			}else{
				$x = explode(DS,$model);
				$model = end($x);
				unset($x[count($x)-1]);
				$path = implode(DS,$x).DS;
			}
			
			if($name == ''){
				$name = $model;
			}
			
			if(in_array($name,$this->_fury_models, TRUE)){
				return;
			}
			
			$FURY =& get_instance();
			if(isset($FURY->$name)){
				show_error("the model you are trying to load is the name of a resource that is already being used: ".$name);
			}
			
			$model = strtolower($model);
			
			if(! file_exists(APP_PATH.'model'. DS .$path.$model.EXT)){
				show_error("Unable to locate the model you have specified: ".$model);
			}
			
			if( ! class_exists('Model')){
				load_class('Model', FALSE);
			}
			
			require_once(APP_PATH . 'model' .DS. $path.$model.EXT);
			
			$model = ucfirst($model);
			
			$FURY->$name = new $model();
			$FURY->$name->_assign_libraries();
			
			$this->_fury_models[] = $name;
		
		}
		
		
		// =========== 
		// ! Load a view 
		// * This function is used to load a "view" file.  It has three parameters:
		// *
		// * 1. The name of the "view" file to be included.
		// * 2. An associative array of data to be extracted for use in the view.
		// * 3. TRUE/FALSE - whether to return the data or load it.  In
		// * some cases it's advantageous to be able to return data so that
		// * a developer can process it in some way.		  
		// =========== 
		
		function view($view, $vars = array(), $return = FALSE){
			return $this->_fury_load(array('_fury_view' => $view, '_fury_vars' => $this->_fury_object_to_array($vars), '_fury_return' => $return));
		}
		
		
		// =========== 
		// ! Load a File to the loader   
		// =========== 
		
		function file($path, $return = FALSE){
			return $this->_fury_load(array('_fury_path' => $path, '_fury_return' => $return));
		}
		
		
		// =========== 
		// ! Load a helper file to be used particuarly in views etc   
		// =========== 
		
		function helper($helpers = array()){
			if ( ! is_array($helpers)){
				$helpers = array($helpers);
			}
		
			foreach ($helpers as $helper){		
				$helper = strtolower(str_replace(EXT, '', str_replace('_helper', '', $helper)).'_helper');
	
				if (isset($this->_fury_helpers[$helper])){
					continue;
				}
				
				# Allow inclusion of application specific helpers and core helpers.
								
				if (file_exists(APP_PATH.'helpers/'.$helper.EXT)){ 
					include_once(APP_PATH.'helpers/'.$helper.EXT);
				}else{		
					if (file_exists(ROOT. SYS. 'helpers/'.$helper.EXT)){
						include_once(ROOT. SYS. 'helpers/'.$helper.EXT);
					}else{
						show_error('Unable to load the requested file: helpers/'.$helper.EXT);
					}
				}
	
				$this->_fury_helpers[$helper] = TRUE;
			}		
		}		
		
		// =========== 
		// ! Load variables to use in the controller class   
		// =========== 
		
		function vars($vars = array(), $val = ''){
			if ($val != '' AND is_string($vars)){
				$vars = array($vars => $val);
			}
		
			$vars = $this->_fury_object_to_array($vars);
		
			if (is_array($vars) AND count($vars) > 0){
				foreach ($vars as $key => $val){
					$this->_fury_cached_vars[$key] = $val;
				}
			}
		}		
			
		
		// =========== 
		// ! FURY Load - This is used to load views and files into the super system, variables are prefixed with _fury_ to avoid collisions   
		// =========== 
		
		function _fury_load($_fury_data){
		
			
			foreach(array('_fury_view','_fury_vars','_fury_path','_fury_return') as $_fury_val){
				$$_fury_val = ( ! isset($_fury_data[$_fury_val])) ? FALSE : $_fury_data[$_fury_val];
			}
			
			# Set the path to the requested file
			
			if($_fury_path == ''){
				
				$_fury_ext = pathinfo($_fury_view, PATHINFO_EXTENSION);
				$_fury_file = ($_fury_ext == '') ? $_fury_view.EXT : $_fury_view;
				$_fury_path = $this->_fury_view_path.$_fury_file;
				
				
			}else{
			
				$_fury_x = explode(DS,$_fury_path);
				$_fury_file = end($_fury_x);
			
			}
			
			# This allows anything loaded with $this->load (views, files, etc.)
			# to become accessible in the controller or model function that called it
			
			if($this->_fury_is_instance()){
							
				$_fury_FURY =& get_instance();
				
				foreach(get_object_vars($_fury_FURY) as $_fury_key=>$_fury_var){
				
					if( ! isset($this->$_fury_key)){
						$this->$_fury_key =& $_fury_FURY->$_fury_key;
					}
				
				}
				
			}
			
			# Extract and cache the variables.
			
			if(is_array($_fury_vars)){
				$this->_fury_cached_vars = array_merge($this->_fury_cached_vars, $_fury_vars);
			}
			
			extract($this->_fury_cached_vars);
					
			
			# Include the path
			
						
			include($_fury_path);
			
			if ($_fury_return === TRUE){		
				$buffer = ob_get_contents();
				@ob_end_clean();
				return $buffer;
			}
			
			# Flush the buffer
			# In order to permit views to within the other views 
			# we need to flush the content back oyt whenever we are beyond the first level.
			
			if(ob_get_level() > $this->_fury_ob_level + 1){
				ob_end_flush();
			}else{
				global $OUT;
				$OUT->append_output(ob_get_contents());
				@ob_end_clean();
			}
				
		
		}	
		
		
		// =========== 
		// ! The function to load the class name into any given object
		// =========== 
		
		function _fury_load_class($class, $params = NULL, $object_name = NULL){	
		
			// Get the class name, and while we're at it trim any slashes.  
			// The directory path can be included as part of the class name, 
			// but we don't want a leading slash
			$class = str_replace(EXT, '', trim($class, DS));
		
			// Was the path included with the class name?
			// We look for a slash to determine this
			$subdir = '';
			if (strpos($class, DS) !== FALSE){
				// explode the path so we can separate the filename from the path
				$x = explode('/', $class);	
				
				// Reset the $class variable now that we know the actual filename
				$class = end($x);
				
				// Kill the filename from the array
				unset($x[count($x)-1]);
				
				// Glue the path back together, sans filename
				$subdir = implode($x, DS).DS;
			}
	
			// We'll test for both lowercase and capitalized versions of the file name
			foreach (array(ucfirst($class), strtolower($class)) as $class){
				//$subclass = APP_PATH.'libraries/'.$subdir.$class.EXT;
	
/*
				// Is this a class extension request?			
				if (file_exists($subclass)){
					$baseclass = ROOT.'libraries/'.ucfirst($class).EXT;
					
					if ( ! file_exists($baseclass)){
						//log_message('error', "Unable to load the requested class: ".$class);
						show_error("Unable to load the requested class: ".$class);
					}
	
					// Safety:  Was the class already loaded by a previous call?
					if (in_array($subclass, $this->_fury_loaded_files)){
						// Before we deem this to be a duplicate request, let's see
						// if a custom object name is being supplied.  If so, we'll
						// return a new instance of the object
						if ( ! is_null($object_name)){
							$FURY =& get_instance();
							if ( ! isset($FURY->$object_name))
							{
								return $this->_fury_init_class($class, '', $params, $object_name);			
							}
						}
						
						$is_duplicate = TRUE;
						//log_message('debug', $class." class already loaded. Second attempt ignored.");
						return;
					}
		
					include_once($baseclass);				
					include_once($subclass);
					$this->_fury_loaded_files[] = $subclass;
		
					return $this->_ci_init_class($class, '', $params, $object_name);			
				}
*/
			
				// Lets search for the requested library file and load it.
				$is_duplicate = FALSE;		
				for ($i = 1; $i < 3; $i++){
					$path = ($i % 2) ? APP_PATH : ROOT.SYS;	
					$filepath = $path.'library/'.$subdir.$class.EXT;
										
					// Does the file exist?  No?  Bummer...
					if ( ! file_exists($filepath)){
						continue;
					}
					
					// Safety:  Was the class already loaded by a previous call?
					if (in_array($filepath, $this->_fury_loaded_files)){
						// Before we deem this to be a duplicate request, let's see
						// if a custom object name is being supplied.  If so, we'll
						// return a new instance of the object
						if ( ! is_null($object_name)){
							$FURY =& get_instance();
							if ( ! isset($FURY->$object_name))
							{
								return $this->_fury_init_class($class, '', $params, $object_name);
							}
						}
					
						$is_duplicate = TRUE;
						//log_message('debug', $class." class already loaded. Second attempt ignored.");
						return;
					}
					
					include_once($filepath);
					$this->_fury_loaded_files[] = $filepath;
					return $this->_fury_init_class($class, '', $params, $object_name);
				}
			} // END FOREACH
	
			// One last attempt.  Maybe the library is in a subdirectory, but it wasn't specified?
			if ($subdir == ''){
				$path = strtolower($class).'/'.$class;
				return $this->_fury_load_class($path, $params);
			}
			
			// If we got this far we were unable to find the requested class.
			// We do not issue errors if the load call failed due to a duplicate request
			if ($is_duplicate == FALSE){
				//log_message('error', "Unable to load the requested class: ".$class);
				show_error("Unable to load the requested class: ".$class);
			}
		}
		
		// =========== 
		// ! FURY Class instantiater 
		// =========== 		
	
		function _fury_init_class($class, $prefix = '', $config = FALSE, $object_name = NULL){

			if ($prefix == ''){		
				
				if (class_exists('FURY_'.$class)) {
					$name = 'FURY_'.$class;
				}else{
					$name = $class;
				}
				
			}else{
				$name = $prefix.$class;
			}
			
			// Is the class name valid?
			if ( ! class_exists($name)) {

				show_error("Non-existent class: ".$class);

			}
			
			// Set the variable name we will assign the class to
			// Was a custom class name supplied?  If so we'll use it
			$class = strtolower($class);
			
			if (is_null($object_name)){
				$classvar = ( ! isset($this->_fury_varmap[$class])) ? $class : $this->_fury_varmap[$class];
			}else{
				$classvar = $object_name;
			}
	
			// Save the class name and object name		
			$this->_fury_classes[$class] = $classvar;
	
			// Instantiate the class		
			$FURY =& get_instance();
						
			if ($config !== NULL){
				$FURY->$classvar = new $name($config);
			}else{
				$FURY->$classvar = new $name;
			}	
		} 
		
		
		// =========== 
		// ! The config file contains an array of class's to be
		// ! autoloaded and they will be should you choose them to be.
		// =========== 
		
		function _fury_autoload(){
			
			global $config;
			
			
			if(!isset($config['auto_load'])){
				return false;
			}
			
			if(count($config['auto_load'])>0){
			
				foreach ($config['auto_load'] as $item){
					$this->library($item);
				}
			
			}
			
			if(count($config['auto_load_models'])>0 && isset($config['auto_load_models'])){
				//$this->model($config['auto_load_models']);
			}
			
		
		}
		
		// =========== 
		// ! FURY assign_to_models function    
		// =========== 		
	
		function _fury_assign_to_models(){
		
			if (count($this->_fury_models) == 0){
				return;
			}
		
			if ($this->_fury_is_instance()){
				$FURY =& get_instance();
				foreach ($this->_fury_models as $model){			
					$FURY->$model->_assign_libraries();
				}
			}else{		
				foreach ($this->_fury_models as $model){			
					$this->$model->_assign_libraries();
				}
			}
		}
		
		
		// =========== 
		// ! Determines whether we should use the FURY instance or PHP5 allows us to use $this    
		// =========== 
		
		function _fury_is_instance(){
			if ($this->_fury_is_php5 == TRUE){
				return TRUE;
			}
		
			global $FURY;
			return (is_object($FURY)) ? TRUE : FALSE;
		}
		
		// =========== 
		// ! Object to an array   
		// =========== 
		
		function _fury_object_to_array($object){
			return (is_object($object)) ? get_object_vars($object) : $object;
		}
			
  		
	
	}