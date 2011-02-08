<?php

	if ( ! defined('ROOT')) exit('No direct script access allowed');
	
	# This will basically control all forum and chat replacement for smilies.
	
	class FURY_BBcode{
	
	var $site_URI;
	var $smily_library_folder;
	
		function BBcode(){
			$this->core =& load_class("Core");
			$this->site_URI = $this->core->get_config_item('base_url');
		}
		
		function _setSmileyLibrary($folder){
		
			#ÊCheck folder exists
		
			$this->smily_library_folder = $folder;
		}
	
		function bbcode_format($str){
  
	  	//look for a [user] element
	  	$str = preg_replace_callback("/\[user\=(.*?)\]/is","fetch_user_id",$str);
	  	$str = preg_replace_callback("/\[crew\=(.*?)\]/is","fetch_crew_id",$str);
	    
	    $simple_search = array(  
	                //added line break
	                '/\[list\]/is',  
	                '/\[\/list\]/is',  
	                '/\[item\](.*?)\[\/item\]/is',  
	                '/\[br\]/is',  
	                '/\[b\](.*?)\[\/b\]/is',  
	                '/\[i\](.*?)\[\/i\]/is',  
	                '/\[u\](.*?)\[\/u\]/is',  
	                '/\[s\](.*?)\[\/s\]/is',  
	                '/\[scroll\](.*?)\[\/scroll\]/is',  
	                '/\[url\=(.*?)\](.*?)\[\/url\]/is',
					'/\[player\](.*?)\[\/player\]/is',  
					'/\[crew\](.*?)\[\/crew\]/is',  
	 				'/\[url\](.*?)\[\/url\]/is',  
					'/\[center\](.*?)\[\/center\]/is',
					'/\[left\](.*?)\[\/left\]/is',
					'/\[right\](.*?)\[\/right\]/is',
	                '/\[align\=(left|center|right)\](.*?)\[\/align\]/is',  
	                '/\[img\](.*?)\[\/img\]/is',  
	                '/\[mail\=(.*?)\](.*?)\[\/mail\]/is',  
	                '/\[mail\](.*?)\[\/mail\]/is',  
	                '/\[font\=(.*?)\](.*?)\[\/font\]/is',  
	                '/\[size\=(.*?)\](.*?)\[\/size\]/is',  
	                '/\[color\=(.*?)\](.*?)\[\/color\]/is',  
	                  //added textarea for code presentation  
	               '/\[codearea\](.*?)\[\/codearea\]/is',  
	                 //added pre class for code presentation  
	              '/\[code\](.*?)\[\/code\]/is',  
	                //added paragraph  
	              '/\[p\](.*?)\[\/p\]/is',       
				  '/:\)/',
				  '/;\)/',
				  '/:\>/',
				  '/:d/',
				  '/:\@/',
				  '/:\(/',
				  '/:o/',
				  '/\(cool\)/',
				  '/:s/',
				  '/8\-\)/',
				  '/:p/',
				  '/:\$/',
				  '/:\#/',
				  '/:\|/',
				  '/\(k\)/',
				  '/;\(/',
				  '/\(laugh\)/',
				  '/\(y\)/',
				  '/\(n\)/',
				  '/\(angry\)/',			  
				  '/8o\|/',
				  '/:\^\)/',
				  '/\|\-\)/',
				  '/:6/',
				  '/:z/',
				  '/\(z\)/',
				  '/\(i\)/',
				  '/\(l\)/',
				  '/\(g\)/',
				  '/\(6\)/',
				  '/\(blush\)/',
				  '/;9/',
				  '/:\-k/',
				  '/\|\-\|/',
				  '/\(c\)/',
				  '/\(dead\)/',
				  '/\(d\)/',
				  '/\(f\)/',
				  '/\(\$\)/',
				  '/\(p\)/',				  
				  '/\^o\)/',
				  '/8\-\|/',
				  '/\(v\)/',
				  '/\(w\)/',
				  '/\(s\)/',			  
				  '/\(e\)/',
				  '/\(glasses\)/',
				  '/\(m\)/',
				  '/\(karate\)/',
				  '/\(rock\)/',
				  '/8\*\)/',
				  '/:\-\*/',
				  '/\*\-\)/',
				  '/\(thumbs\)/',
				  '/\(bandana\)/',
				  '/\(b\)/',
				  '/\(a\)/',			  
				  '/\(alien\)/',
				  '/\(thumbup\)/',
				  '/\(thumbdown\)/',
				  '/\(beer\)/',
				  '/\(coffee\)/',
				  '/\(poop\)/',
				  '/\(heart\)/',
				  '/\(rose\)/',
				  '/\(r\)/',
				  '/\(rip\)/',			  
				  			  
				  );  
	  
	    $simple_replace = array(  
					//added line break
	                '<ul style="margin-top:0;">',  
	                '</ul>',  
	                '<li>$1</li>',  
	               '<br />',
	                '<strong>$1</strong>',  
	                '<em>$1</em>',  
	                '<u>$1</u>',  
	                '<del>$1</del>',  
	                '<marquee behavior="scroll" direction="left">$1</marquee>',  
					// added nofollow to prevent spam  
	                '<a href="$1" rel="nofollow">$2</a>',
	 				'<a href="/personal/profile.php?profuser=$1" rel="nofollow" style="text-decoration:none;"><b>$1</b></a>',
					'<a href="/crew/crewprofile.php?crewname=$1" rel="nofollow" style="text-decoration:none;"><b>$1</b></a>',						
	                '<a href="$1" rel="nofollow" title="$1">$1</a>', 
					'<div style="text-align:center;">$1</div>', 
					'<div style="text-align:left;">$1</div>',  
					'<div style="text-align:right;">$1</div>',  
	                '<div style="text-align: $1;">$2</div>',  
					//added alt attribute for validation  
	                '<img src="$1" alt="" />',  
	                '<a href="mailto:$1">$2</a>',  
	                '<a href="mailto:$1">$1</a>',  
	                '<span style="font-family: $1;">$2</span>',  
	                '<span style="font-size: $1;">$2</span>',  
	                '<span style="color: $1;">$2</span>',  
					//added textarea for code presentation  
					'<textarea class="code_container" rows="30" cols="70">$1</textarea>',  
					//added pre class for code presentation  
					'<pre class="code">$1</pre>',  
					//added paragraph  
					'<p>$1</p>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/smiley.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/wink.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/cheesy.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/grin.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/angry.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/sad.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/shocked.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/cool.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/huh.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/rolleyes.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/tongue.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/embarassed.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/lipsrsealed.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/undecided.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/kiss.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/cry.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/laugh.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/yes.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/no.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/veryangry.gif"/>',			
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/wacko.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/uhoh.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/tired.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/surprised.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/stunned.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/snore.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/sick.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/lips.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/goofy.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/evil.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/blush.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/anxious.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/brood.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/chinese.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/confused.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/dead.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/drunk.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/freak.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/greedy.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/pirate.gif"/>',					
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/smug.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/smart.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/vampire.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/wacky.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/smoking.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/ears.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/glasses2.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/mask.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/karate.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/rockstar.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/wideeyed.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/shy.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/thinking.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/thumbsup.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/bandana.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/baby.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/army.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/alien.gif"/>',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/thumbup.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/thumb_down.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/beer.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/coffee.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/poop.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/heart.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/rose.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/deadrose.png" />',
					'<img src="'.$this->site_URI.'/assets/images/smilies/'.$this->smily_library_folder.'/grave.png" />',				
					
					);  
	  
		    // Do simple BBCode's  
		    $str = preg_replace ($simple_search, $simple_replace, $str);  
	    
	  	
		    // Do <blockquote> BBCode  
		    $str = $this->bbcode_quote ($str);  
		    return $str;  
		 
		}
		
		function bbcode_quote ($str) {  
		    //added div and class for quotes  
		    $open = '<blockquote><div class="quote">';  
		    $close = '</div></blockquote>';  
		  
		    // How often is the open tag?  
		    preg_match_all ('/\[quote\]/i', $str, $matches);  
		    $opentags = count($matches['0']);  
		  
		    // How often is the close tag?  
		    preg_match_all ('/\[\/quote\]/i', $str, $matches);  
		    $closetags = count($matches['0']);  
		  
		    // Check how many tags have been unclosed  
		    // And add the unclosing tag at the end of the message  
		    $unclosed = $opentags - $closetags;  
		    for ($i = 0; $i < $unclosed; $i++) {  
		        $str .= '</div></blockquote>';  
		    }  
		  
		    // Do replacement  
		    $str = str_replace ('[' . 'quote]', $open, $str);  
		    $str = str_replace ('[/' . 'quote]', $close, $str);  
		  
		    return $str;  
		} 
	
	}
	