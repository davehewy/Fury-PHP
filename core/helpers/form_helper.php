<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	if( ! function_exists('form_open_this')){
	
		function form_open_this($action = '', $attributes = '', $hidden = array()){
		
			$FURY =& get_instance();
	
			if ($attributes == ''){
				$attributes = 'method="post"';
			}
	
			$action = ( strpos($action, '://') === FALSE) ? $FURY->core->site_url($action) : $action;
	
			$form = '<form action="'.$action.'"';
		
			$form .= _attributes_to_string($attributes, TRUE);
		
			$form .= '>';
	
			if (is_array($hidden) AND count($hidden) > 0){
				$form .= form_hidden($hidden);
			}
	
			return $form;
		}
	
	}
	
	// =========== 
	// ! Closes a form.   
	// =========== 
	
	if( ! function_exists('form_close')){
		function form_close(){
			return '</form>';
		}
	}
	
	// =========== 
	// ! Error box presets   
	// =========== 
	
	if( ! function_exists('errorbox')){
	
		function errorbox($msg,$class=false){
			$class = (!$class) ? "errorbox" : $class;
			echo '<div class="'.$class.'"></div>';
		}
	
	}
	
	// =========== 
	// ! Success box presets   
	// =========== 
	
	if( ! function_exists('successbox')){
	
		function successbox($msg,$class=false){
			$class = (!$class) ? "errorbox" : $class;
			echo '<div class="'.$class.'"></div>';
		}
	
	}
	
	
	// =========== 
	// ! Sets a value to the value  
	// =========== 
	
	if( ! function_exists('set_value')){
	
		function set_value($value,$default = false){
			
			if(isset($_POST[$value])){
				return $_POST[$value];
			}elseif($default){
				return $default;
			}else{
				return false;
			}
		
		}
	
	}
	
	// =========== 
	// ! Converts all attributes passed in an array to a string.   
	// =========== 
	
	if ( ! function_exists('_attributes_to_string')){
		function _attributes_to_string($attributes, $formtag = FALSE){
			if (is_string($attributes) AND strlen($attributes) > 0){
				if ($formtag == TRUE AND strpos($attributes, 'method=') === FALSE){
					$attributes .= ' method="post"';
				}
	
			return ' '.$attributes;
			}
		
			if (is_object($attributes) AND count($attributes) > 0){
				$attributes = (array)$attributes;
			}
	
			if (is_array($attributes) AND count($attributes) > 0){
			$atts = '';
	
			if ( ! isset($attributes['method']) AND $formtag === TRUE){
				$atts .= ' method="post"';
			}
	
			foreach ($attributes as $key => $val){
				$atts .= ' '.$key.'="'.$val.'"';
			}
	
			return $atts;
			}
		}
	}	
