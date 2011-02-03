<?php
	
	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	// =========== 
	// ! The templating file for FURY   
	// =========== 
	
	class FURY_Templating{
	
		var $themes = array();
		
		function FURY_Templating(){
			
			$this->core =& load_class('Core');
			$this->initialise_template();
			
		}
		
		
		function initialise_template(){
			
			if($this->core->get_config_item("default_template") && $this->core->get_config_item("auto_templating")){
			
				# Is the default template an array?
				
				$themes = $this->core->get_config_item("default_template");
				
				if(is_array($themes)){
					
					# It is an array
					foreach($themes as $val):
						$this->themes->$val
					endforeach;
					
				}else{
			
				# Default single theme site.
				
				$theme 
				
				array_search($theme,$)
									
				# Set the theme
								
				$this->themes->$theme = = $theme .DS. 'header'.EXT;
				$this->themes->$theme['footer'] = $theme .DS. 'footer'.EXT;
				
				$this->load->template('default/header');
				
				$this->themes->default_inside->load('header');
				
				}
				
			
			}
		
		}	
	
	}