<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');	

	// =========== 
	// ! The sole purpose of this script is to congregate the stuff we are going to be 
	// doing in and around sessions   
	// There will always per script be quite a bit of session based work.
	// This aims to make it easier.
	// =========== 

	class FURY_Session{
	
	const SESSION_STARTED = TRUE;
	const SESSION_ENDED = FALSE;
	
	private $sessionState = self::SESSION_NOT_STARTED;
	
		function __construct(){
			$this->sessionState = session_start();
		}
		
		// =========== 
		// ! Set a session part   
		// =========== 
		
		function _set($var,$value){
			$_SESSION[$var] = $value;
		}
		
		// =========== 
		// ! Get a session part.   
		// =========== 
		
		function _get($var){
			if(isset($_SESSION[$var])){
				return $_SESSION[$var];
			}
			return false;
		}
		
		// =========== 
		// ! Get whole session array   
		// =========== 
		
		function _get_session_array(){
			if(isset($_SESSION)){
				return $_SESSION;
			}
		}
		
		// =========== 
		// ! Check if something is set   
		// =========== 
		
	    function _isset( $name ){
	        return isset($_SESSION[$name]);
	    }
	   	
	   	// =========== 
	   	// ! Unset a part of the session array
	   	// =========== 
	   
	    function _unset( $name ){
	        unset( $_SESSION[$name] );
	    }	
	    
	    // =========== 
	    // ! Does exactly what it says on the tin and destroys a session   
	    // =========== 
	    
	    function _destroy(){
	    	if($this->sessionState == self::SESSION_STARTED){
				$this->sessionState = !session_destroy();
	            unset( $_SESSION );	    
            }
	    }
	
	}