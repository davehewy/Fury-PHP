<?php

	class Email extends Model{
	
		var $_from_email;
		var $_from_name;
		var $_replyto_email;
		var $_replyto_name;
		var $_subject;
		var $_plain_text;
		var $_html_body;
	
		function Email(){
			parent::Model();
			$this->core =& load_class('Core');
			
			$this->set_defaults();
			
		}
		
		# On load set the defaults from the config file if there are any to the script.
		
		function set_defaults(){
			$webmaster_email = ( ! isset( $this->core->get_config_item("webmaster_email"))) ?
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
			
			return $thisl
		
		}
		
		function setTo($email,$name){
			$this->_send_to = $email;
			$this->_send_to_name = $name;
		}
		
		function setSubject($subject){
			$this->_subject = $subject;
		}
		
		function setPlain($text){
			$this->_plain_text = $text;
		}		
		
		function setHtml($html){
			$this->_html_body = $html;
		}
		
		function send_email($to,$subject,$text,$html,$params=false){
		
			if(!is_empty($to) && !is_empty($subject) && !is_empty($text) && !is_empty($html)){
					
				 
	  	  		$mail = new Zend_Mail();
			    $mail->setFrom(  $this->_from_email,  $this->_from_name );
			    $mail->setReplyTo( $this->_replyto_email, $this->_replyto_name );
			    $mail->addTo( $this->_send_to, $this->send_to_name );
			    $mail->setSubject( $this->_subject );
			    $mail->setBodyText( $this->_plain_text );
			    $mail->setBodyHtml( $this->_html_body );
			    $mail->send();		
			
				return true;
			
			}else{
			
				show_error("An email was attempted to be sent, but you failed because you did provide all the details required.");
			
			}
			
			return false;
		    
		}
	
	}