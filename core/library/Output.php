<?php  

	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	
	class FURY_Output{
	
		var $final_output;
		var $cache_expiration	= 0;
		var $headers 			= array();
		var $enable_profiler 	= FALSE;

		function FURY_Output(){
			
		}
		
		// =========== 
		// ! Returns the current output string.   
		// =========== 
		
		function get_output(){
			return $this->final_output;
		}
		
		// =========== 
		// ! Sets the output string   
		// =========== 
		
		function set_output($output){
			$this->final_output = $output;
		}
		
		// =========== 
		// ! Append Output 
		// =========== 	
		
		function append_output($output){
			if ($this->final_output == ''){
				$this->final_output = $output;
			}else{
				$this->final_output .= $output;
			}
		}
		
		// =========== 
		// ! Lets you set a server header which will be outputted with the final display   
		// =========== 
		
		function set_header($header, $replace = TRUE){
			$this->headers[] = array($header, $replace);
		}
		
		// =========== 
		// ! Set HTTP status header  
		// =========== 
		
		function set_status_header($code = '200', $text = ''){
			set_status_header($code, $text);
		}
		
		// =========== 
		// ! Finally dipslay the output   
		// =========== 
		
		function _display($output = ''){
		
			// Note:  We use globals because we can't use $FURY =& get_instance()
			// since this function is sometimes called by the caching mechanism,
			// which happens before the FURY super object is available.
			global $CF;
			
			// --------------------------------------------------------------------
			
			// Set the output data
			if ($output == ''){
				$output =& $this->final_output;
			}
	
			// --------------------------------------------------------------------
	
			// Parse out the elapsed time and memory usage,
			// then swap the pseudo-variables with the data
	
	
			// --------------------------------------------------------------------
			
			// Are there any server headers to send?
			if (count($this->headers) > 0){
				foreach ($this->headers as $header){
					@header($header[0], $header[1]);
				}
			}		
	
			// --------------------------------------------------------------------
			
			// Does the get_instance() function exist?
			// If not we know we are dealing with a cache file so we'll
			// simply echo out the data and exit.
			if ( ! function_exists('get_instance')){
				echo $output;
				return TRUE;
			}
		
			// --------------------------------------------------------------------
	
			// Grab the super object.  We'll need it in a moment...
			$FURY =& get_instance();
			
			// --------------------------------------------------------------------
	
			// Does the controller contain a function named _output()?
			// If so send the output there.  Otherwise, echo it.
			if (method_exists($FURY, '_output')){
				$FURY->_output($output);
			}else{
				echo $output;  // Send it to the browser!
			}
			
		}		
	
	}