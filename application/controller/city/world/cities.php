<?php

	class Cities extends Controller{
	
		function __construct(){
			
			parent::Controller();
		
		}	
	
		function index(){
			
			echo 'Im calling the index file path';
		
		}
		
		function perform(){
			
			$this->load->view('shop/test');
			//echo 'Lets make some mess and fuck some people up';
		
		}
	
	}