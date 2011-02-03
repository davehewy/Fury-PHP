<?php

	
	class Contact extends Controller{
	
		function __construct(){
			
			parent::Controller();
		
		}
		
		function index(){
				
			$this->load->model('special/mate');
			$this->mate->makeMe();
			
			$data = array("name"=>"dave");
			
			
			$this->load->view('header',$data);
			$this->load->view("contact");
			
			$this->db->connectDefault();
			if($this->db->query("select category from categories where category='Membership'")->row()){
			
			
			
					
			//$this->load->view("shop/test");
		
		}

		
/*
		function index(){
			
			echo '<Br>index';
		
		}
*/
		
		function submit($string=false){
			
			$this->load->view('contact');
		
			echo '<br><Br>Im trying to submit my contact form';
			
			//echo $this->model;
		
		}
	
	}