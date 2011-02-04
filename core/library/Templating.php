<?php
	
	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	// =========== 
	// ! The templating file for FURY   
	// =========== 
	
	class FURY_Templating{
		
		var $_fury_default_theme;
		var $_fury_themes = array();
		var $_fury_theme_directory;
		var $_fury_cached_variables;
		
		function FURY_Templating(){
			
			$this->core =& load_class('Core');
			$this->initialise_template();
			
		}
		
		// =========== 
		// ! Detect whether or not we have a default_template to work with, 
		// * and if we do serve it up so long as not overwritten   
		// =========== 
		
		function initialise_template(){
		
			$this->_fury_theme_directory = APP_PATH . 'themes' .DS;
			
			if($this->core->get_config_item("default_template") && $this->core->get_config_item("auto_templating")){
			
				# Is the default template an array?
				
				$theme = $this->core->get_config_item("default_template");
				
				# Just need to set what the default controller is.
				
				$this->_fury_default_theme= $theme;
			
			}
		
		}
		
		// =========== 
		// ! A render call serves up a template piece whilst passing generously to
		// * the template any passed paramaters in the form of a basic array.   
		// =========== 
		
		function render($theme,$vars = false){
		
			$path = explode(DS,$theme);
			
			$_fury_theme_path = $this->_fury_theme_directory;
			
			if(count($path)>1){
			
				
				// There is a subdirectory to filter down
				
				foreach($path as $key => $val): 
					if(count($path)-1!=$key){
						$_fury_theme_path.= $val .DS;
					}else{
						$_fury_theme_path.= $val .EXT;
					}
				endforeach;
				
				# Ok we now have a path.
				
				#ÊDoes it exist
				
				if(file_exists($_fury_theme_path)){
					
					if($vars){
						$pass = $this->convert_pass_data($vars);
						extract($vars);
					}
							
					# Now we know the file exists lets load it in passing data to it 
					
					include($_fury_theme_path);
					
					$buffer = ob_get_contents();
					@ob_end_clean();
					return $buffer;
					
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
										
				}else{
					show_error("Damn. We couldn't find the template piece you were looking for: ".$theme);
				}
				
			
			}else{
				
				// If they have a default set then we can try and use that.
				
				if($this->_fury_default_theme){
					
					# If the template part exists
					
					$_fury_theme_path.= $this->_fury_default_theme .DS. $theme . EXT;
					
					if(file_exists( $_fury_theme_path )){
						
						if($vars){
							$pass = $this->convert_pass_data($vars);
							extract($vars);
						}
								
						# Now we know the file exists lets load it in passing data to it 
						
						include($_fury_theme_path);
						
						$buffer = ob_get_contents();
						@ob_end_clean();
						return $buffer;
						
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
												
					}else{
						show_error("We couldn't seem to find the template part: ".$theme." in your default theme folder.");
					}
					
				}
				
			
			}
		
		}
		
		function convert_pass_data($object){
			return (is_object($object)) ? get_object_vars($object) : $object;
		}
	
	}