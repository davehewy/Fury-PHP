<?php

	
	class Contact extends Controller{
	
		function __construct(){
			
			parent::Controller();
			
			$this->load->library('Mail');
									
		}
		
		function index(){
			
			$this->load->helper('form');
			
			$this->load->view('contact');
			
			echo 'hey dude';
			
			//$this->load->view('contact');
			
					
			//$this->load->view("shop/test");
		
		}

		
		function submit($string=false){
			
			$this->load->library('Mail');
			$this->mail
				->setTo("dave@bytewire.co.uk","David Heward")
				->setSubject("Some subject")
				->setPlain("This is some plain text")
				->setHtml("<b>Goody string</b> i cant be bothered to watch lol.")
				->send();
			
/*
			$this->load->model('postmark');
			$this->postmark
				->setFrom('webmaster@street-crime.com')
				->placeHead('likethis')
				-
			$this->postmark->send_email();

*/
			
			$this->load->view('contact');
		
			echo '<br><Br>Im trying to submit my contact form';
			
			//echo $this->model;
		
		}
	
	}