<?php
	
	if ( ! defined('ROOT')) exit('No direct script access allowed');

	/**
	 * FURY PHP
	 *
	 * An open source application development framework for PHP 4.3.2 or newer
	 *
	 * @package		FURY PHP
	 * @author		Bytewire Ltd
	 * @copyright	Copyright (c) 2009 - 2010, Bytewire, Ltd.
	 * @license		http://fury.bytewire.co.uk/user_guide/license.html
	 * @link		http://fury.bytewire.co.uk
	 * @since		Version 1.0
	 * @filesource
	 */

	class Controller extends FURY_Base{
	
		function Controller(){
			parent::FURY_Base();
			$this->_fury_initialize();
		}
		
		function _fury_initialize(){
		
			// Assign all of the class's called by our bootstrap file so that FURY can be used as one big
			// super remote control.
			
			$classes = array(
								'core'	=> 'Core',
								'uri'		=> 'URI',
								'router'	=> 'Router',
								'output'	=> 'Output',
								'themes' => 'Templating',
							);
			
			foreach ($classes as $var => $class){
				$this->$var =& load_class($class);
			}

			// In PHP 5 the Loader class is run as a discreet
			// class.  In PHP 4 it extends the Controller
			if (floor(phpversion()) >= 5){
				$this->load =& load_class('Loader');
				$this->load->_fury_autoload();
			}else{
				
				//$this->_fury_autoloader();
				
				// sync up the objects since PHP4 was working from a copy
				foreach (array_keys(get_object_vars($this)) as $attribute){
					if (is_object($this->$attribute)){
						$this->load->$attribute =& $this->$attribute;
					}
				}
			}
		}			
	}
	