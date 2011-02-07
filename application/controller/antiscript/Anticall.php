<?php

	class Anticall extends Controller{
	
		function Anticall(){
			parent::Controller();
		}
		
		function index(){
			
			$this->load->library('securimage/securimage');
			
			$this->securimage->show();
			
		}
	
	}