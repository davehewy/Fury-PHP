<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	class FURY_Utilities{
	
		function FURY_Utilities(){
			
		}
		
		function generateToken($length){
			$uniqueId = str_replace(
			array('+','/','='),
			array('','',''),
			base64_encode(file_get_contents('/dev/urandom', null, null, -1, $length)));
			
			return $uniqueId;
		}
	
	}