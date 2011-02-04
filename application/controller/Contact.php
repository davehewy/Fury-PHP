<?php

	
	class Contact extends Controller{
	
		function __construct(){
			
			parent::Controller();
		
		}
		
		function index(){
			
			$this->load->helper('form');
			
			$this->load->view('contact');
			
			echo 'hey dude';
			
			//$this->load->view('contact');
			
					
			//$this->load->view("shop/test");
		
		}

		
		function submit($string=false){
			
			$this->load->view('contact');
		
			echo '<br><Br>Im trying to submit my contact form';
			
			//echo $this->model;
		
		}
	
	}