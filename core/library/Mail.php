<?php
		
	if ( ! defined('ROOT')) exit('No direct script access allowed');	
	
	class FURY_Mail{
		
		var $_default_system_email;
		var $_default_system_name;
		var $_from_email;
		var $_from_name;
		var $_replyto_email;
		var $_replyto_name;
		var $_send_to;
		var $_send_to_name;
		var $_subject;
		var $_plain_text;
		var $_html_body;
		
		var $library;
		var $system = null;
		
		var $var_array = array();
		var $send_errors = array();

	
		function FURY_Mail(){
		
			$this->core =& load_class('Core');
			$this->load =& load_class('Loader');
			
			if($zendpath = $this->core->get_config_item('zend_local_include')){
				
				set_include_path(
				get_include_path().PATH_SEPARATOR.$zendpath);
				require_once 'Zend/Mail.php';
				require_once 'Zend/Mail/Transport/Smtp.php';			
				
			}		
			
			$this->load_default_library();
			$this->set_defaults();	
		}
		
		
		function set_defaults(){
			$this->_default_system_email = ( ! $this->core->get_config_item("default_system_email")) ? FALSE : $this->core->get_config_item('default_system_email');
			$this->_default_system_name = ( ! $this->core->get_config_item("default_system_name")) ? FALSE : $this->core->get_config_item('default_system_name');
			$this->_from_email = ( ! $this->core->get_config_item("webmaster_email")) ? FALSE : $this->core->get_config_item('webmaster_email');
			$this->_from_name = ( ! $this->core->get_config_item("webmaster_sendfrom")) ? FALSE : $this->core->get_config_item('webmaster_sendfrom');
			$this->_replyto_email = (! $this->core->get_config_item("reply_to_email")) ? FALSE : $this->core->get_config_item('reply_to_email');
			$this->_replyto_name = ( ! $this->core->get_config_item("reply_to_name")) ? FALSE : $this->core->get_config_item('reply_to_name');
		
		}
		
		# Set the name from
		
		function setFromEmail($email=false,$name=false){
		
			if(isset($email)){
				$this->_from_email = $email;
			}
			
			if(isset($email)){
				$this->_from_name = $name;
			}
			
			return $this;
			
		}

		# Set reply to stuff.
		
		function setReplyTo($email=false,$name=false){
			
			if(isset($email)){
				$this->_replyto_email = $email;
			}
			
			if(isset($name)){
				$this->_replyto_name = $name;
			}
			
			return $this;
		
		}
		
		function setTo($email,$name=false){
			$this->_send_to = $email;
			$this->_send_to_name = $name;
			return $this;
		}
		
		function setToName($name){
			$this->_send_to_name = $name;
			return $this;
		}
		
		function setSubject($subject){
			$this->_subject = $subject;
			return $this;
		}
		
		function setPlain($text){
			$this->_plain_text = $text;
			return $this;
		}		
		
		function setHtml($html){
			$this->_html_body = $html;
			return $this;
		}
		
		function setSystem(){
			$this->system = true;
			return $this;
		}
		
		
		# If a default library is present load it in.
		
		function load_default_library(){
			
			if($this->library = $this->core->get_config_item('email_app')){
			
				$this->load_mail_library($this->library);
			
			}else{
				
				$this->library = 'zendPHPLibrary';
				$this->load_mail_library();
			
			}
			
		}
		
		function load_mail_library(){
		
			$this->load->library($this->library);
		
		}
		
		// We require by default that all emails are sent with all needed credentials.
		// So to ensure this we will make sure those fields are set
		
		function checkFields(){
			
			$problem = 0;
		
			$this->var_array = array("From email"=>$this->_from_email,"From name"=>$this->_from_name,"Reply to email"=>$this->_replyto_email,"Reply to name"=>$this->_replyto_name,"Subject"=>$this->_subject,"Send to email"=>$this->_send_to,"Send to name"=>$this->_send_to_name,"Plain text version"=>$this->_plain_text,"HTML version"=>$this->_html_body);
			
			foreach($this->var_array as $key=>$val):
				
				if(!isset($val)){
					$problem++;
				}
				
				$this->send_errors[] = sprintf(gettext("%s not set! Given: %s"),$key,$val);
				
			endforeach;
			
			if($problem>0){
				return false;
			}
			return true;
		
		}
		
		function send(){
		
			if($this->system){
				if(!$this->_default_system_name){
					$this->setTo($this->_default_system_email);
				}else{
					if($this->_default_system_name && !$this->_send_to_name){
					$this->setTo($this->_default_system_email,$this->_default_system_name);
				}
			}
			
			// Check for certain fields before sending
			if($this->checkFields()){

				# Send to another send function to incorporate.
				switch($this->library){
					case "postmark_zend": $this->postmark_send(); break;
				}	
				
			}
		
		}
		
		function postmark_send(){
		
			$mail = new Zend_Mail();
		    $mail->setFrom( $this->_from_email , $this->_from_name );
		    $mail->setReplyTo( $this->_replyto_email, $this->_replyto_name );
		    $mail->addTo( $this->_send_to, $this->_send_to_name );
		    $mail->setSubject( $this->_subject );
		    $mail->setBodyText( $this->_plain_text );
		    $mail->setBodyHtml( $this->_html_body );
		    $mail->send();		
		    
		}
		
		
	
	}