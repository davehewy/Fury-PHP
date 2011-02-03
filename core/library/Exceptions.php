<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');

	// =========== 
	// ! Class To literally through errors at users as to why things didn't work as expected!   
	// =========== 
	
	class FURY_Exceptions{
	
		function FURY_Exceptions(){
		
			echo 'Exceptions triggered';
		
		}
		
		function show_error($title,$text,$type,$code){
		
			echo '<h1>'.$title.'</h1>';
			
			echo $text;
			
			echo '<p>Error status code: '.$code.'</p>';
		
		}
		
		function show_404(){
		
			echo '<h1>You reached a 404! Doh!</h1>';
		
		}
	
	}