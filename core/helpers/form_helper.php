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
	
	if( ! function_exists('form_close')){
		function form_close(){
			return '</form>';
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