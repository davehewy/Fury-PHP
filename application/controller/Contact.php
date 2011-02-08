<?php

	
	class Contact extends Controller{
	
		function __construct(){
			
			parent::Controller();
			
			$this->load->library('Mail');
									
		}
		
		function index($email,$code){
			
			print_r(func_get_args());
			
			$this->load->helper('form');
			
			$this->load->view('contact');
			
		
			
			//$this->load->view('contact');
			
					
			//$this->load->view("shop/test");
		
		}

		
		function submit($string=false){
		
		$this->load->library('Profanity');
		
		$str = "Cock ass grass wanker fest";
		
		echo $this->profanity->strip($str);
		
		
/*
		$this->load->library('Utilities');
		echo $this->utilities->generateToken(40);
*/
			
/*
			$this->load->library('Mail');
			$this->mail
				->setToName("Contact Form")
				->setSubject("Some subject")
				->setPlain("This is some plain text")
				->setHtml("<b>Goody string</b> i cant be bothered to watch lol.")
				->setSystem()
				->send();
*/
			
/*
			$this->load->model('postmark');
			$this->postmark
				->setFrom('webmaster@street-crime.com')
				->placeHead('likethis')
				-
			$this->postmark->send_email();

*/
			
			$this->load->view('contact');
		
			
			//echo $this->model;
		
		}
	
	}