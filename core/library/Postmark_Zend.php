<?php	
	
	if ( ! defined('ROOT')) exit('No direct script access allowed');

	# FURY Supports direct integration of Postmark with both a addon for zend mail or alternatively just using the plain call.
	
	set_include_path(
	get_include_path().PATH_SEPARATOR.
	$zendvar);
	require_once 'Zend/Mail.php';
	require_once 'Zend/Mail/Transport/Smtp.php';
	
	class FURY_Postmark_Zend{
		
		private $_apiKey = '';
		private $_e_from = '';
		
	    /**
	     * API key required by Postmark
	     *
	     * @var string
	     */		
	
		function FURY_Postmark_Zend(){
				
			$this->core =& load_class('Core');
			
			$apiKey = $this->core->get_config_item('postmark','apikey');
			$_e_from = $this->core->get_config_item('webmaster_email');
						
			
	        if ( empty( $apiKey ) ) {
	            throw new Exception( __CLASS__ . ' must be instantiated with a API key' );
	        }
	        
	        $this->_apiKey = $apiKey;
	        
	        $this->_include_zend();
		
		}

		function
	    
	    public function _sendMail()
	    {
	        // Retrieve the headers and appropriate keys we need to construct our mail
	        $headers = $this->_mail->getHeaders();
	        
	        $to = array();
	        if ( array_key_exists( 'To', $headers ) ) {
	            reset($headers['To']);
	            foreach($headers['To'] as $key => $val ) {
	                if( empty($key) || $key != 'append' )
	                {
	                    $to[] = $val;
	                }
	            }
	            reset($headers['To']);
	        }
	        
	        $cc = array();
	        if ( array_key_exists( 'Cc', $headers ) ) {
	            reset($headers['Cc']);
	            foreach($headers['Cc'] as $key => $val ) {
	                if( empty($key) || $key != 'append' )
	                {
	                    $cc[] = $val;
	                }
	            }
	            reset($headers['Cc']);
	        }
	        
	        $bcc = array();
	        if ( array_key_exists( 'Bcc', $headers ) ) {
	            reset($headers['Bcc']);
	            foreach($headers['Bcc'] as $key => $val ) {
	                if( empty($key) || $key != 'append' )
	                {
	                    $bcc[] = $val;
	                }
	            }
	            reset($headers['Bcc']);
	        }
	        
	        $from = array();
	        if ( array_key_exists( 'From', $headers ) ) {
	            reset($headers['From']);
	            foreach($headers['From'] as $key => $val ) {
	                if( empty($key) || $key != 'append' )
	                {
	                    $from[] = $val;
	                }
	            }
	            reset($headers['From']);
	        }
	        
	        $replyto = array();
	        if ( array_key_exists( 'Reply-To', $headers ) ) {
	            reset($headers['Reply-To']);
	            foreach($headers['Reply-To'] as $key => $val ) {
	                if( empty($key) || $key != 'append' )
	                {
	                    $replyto[] = $val;
	                }
	            }
	            reset($headers['Reply-To']);
	        }
		
		    $tags = array();
	        if (array_key_exists('postmark-tag', $headers)) {
	            reset($headers['postmark-tag']);
	            foreach ($headers['postmark-tag'] as $key => $val) {
	                if (empty($key) || $key != 'append')
	                {
	                    $tags[] = $val;
	                }
	            }
	            reset($headers['postmark-tag']);
	        }
	        
	        $postData = array(
	            'From'     => implode( ',', $from ),
	            'To'       => implode( ',', $to ),
	            'Cc'       => implode( ',', $cc ),
	            'Bcc'      => implode( ',', $bcc),
	            'Subject'  => $this->_mail->getSubject(),
	            'ReplyTo'  => implode( ',', $replyto ),
	            'tag'      => implode(',', $tags)
	        );
	        
	        // We first check if the relevant content exists (returned as a Zend_Mime_Part)
	        if ( $this->_mail->getBodyText() ) {
	            $part = $this->_mail->getBodyText();
	            $part->encoding = false;
	            $postData['TextBody'] = $part->getContent();            
	        }
	        
	        if ( $this->_mail->getBodyHtml() ) {
	            $part = $this->_mail->getBodyHtml();
	            $part->encoding = false;
	            $postData['HtmlBody'] = $part->getContent();
	        }
	        
	        require_once 'Zend/Http/Client.php';
	        $client = new Zend_Http_Client();
	        $client->setUri( 'http://api.postmarkapp.com/email' );
	        $client->setMethod( Zend_Http_Client::POST );
	        $client->setHeaders( array(
	            'Accept' => 'application/json',
	            'X-Postmark-Server-Token' => $this->_apiKey
	        ));
	        $client->setRawData( json_encode( $postData ), 'application/json' );
	        $response = $client->request();
	        
	        if ( $response->getStatus() != 200 ) {
	            throw new Exception( 'Mail not sent - Postmark returned ' . $response->getStatus() . ' - ' . $response->getMessage() );
	        }
	    }
	}