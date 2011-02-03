<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	// =========== 
	// ! FURY developed by Bytewire Ltd, Model class.   
	// =========== 

	class Model {
	
		var $_parent_name = '';
	
		function Model(){
		
			# If the magic __get() or __set() methods are used in a Model references can't be used.
			$this->_assign_libraries( (method_exists($this, '__get') OR method_exists($this, '__set')) ? FALSE : TRUE );
			
			# Grab the name of the first class
			$this->_parent_name = ucfirst(get_class($this));
			
		}
		
		// =========== 
		// ! Create local references to all currently used objects   
		// =========== 
		
		function _assign_libraries($use_reference = TRUE){
		
			$FURY =& get_instance();				
			foreach (array_keys(get_object_vars($FURY)) as $key){
			
				if ( ! isset($this->$key) AND $key != $this->_parent_name){			
					// In some cases using references can cause
					// problems so we'll conditionally use them
					
					if ($use_reference == TRUE){
						$this->$key = NULL; // Needed to prevent reference errors with some configurations
						$this->$key =& $FURY->$key;
					}else{
						$this->$key = $FURY->$key;
					}
				}
				
			}		
		}
	
	}
