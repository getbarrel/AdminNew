<?
Class templet_ms
{
	var $loop_pt1 = "|{[^}]+}[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/[^}]+}|U"; // loop contents 
	var $templet_path = "";
	var $data;

	function templet_ms(){
		$this->data = array();
	}
	
	function define($var_name, $filepath){
		$contents_ = $this->load_templet($filepath);						
		//$contents_ = eregi_replace("\"","\\\"", $contents_);
		$this->data[$var_name] = $contents_;
		//eval ("\$this->$var_name = \"".$contents_."\";");
	}
	
	function load_templet($filepath){		

		if($filepath == "" || !file_exists($filepath)) return;
		$thisfile = file($filepath);
	
		while(list($line,$value) = each($thisfile)) {
			$value = ereg_replace("(\r|\n)","",$value);
			$result .= "$value\r\n";
		}
	
		return $result;
	}
	
	function replace_loop_value($loop_name, $loop_content){
		$loop_value_pt = "|{".$loop_name.".(.*)}|U";		
		preg_match_all("$loop_value_pt", "$loop_content", $out, PREG_PATTERN_ORDER);

		$i = 1;
		for($j=0;$j < count($out[$i]);$j++){
				$loop_content = str_replace($out[0][$j], "$".$loop_name."[".$out[1][$j]."]",$loop_content);
		}
				
		return $loop_content;	
	}
	function assignif($var_name, $loop_name, $loop_array){				
			//eval ("\$if_content = \$this->$var_name ;");
			
			$if_content = $this->data[$var_name];
			$if_contents_ = $this->if_contents($loop_name,$loop_array, $if_content);
			
			if($if_contents_ == ""){
				//$loop_text = eregi_replace("\"","\\\"", $loop_array);			
				$loop_text = $loop_array;	
						
				;
				//eval ("\$this->$var_name = str_replace('{".$loop_name."}',\"$loop_text\",\$this->$var_name);");					
				$this->data[$var_name] = str_replace("{".$loop_name."}",$loop_text,$this->data[$var_name]);				
				
				//echo count($if_contents_);
				//exit;
			}else{
				
				$if_all_pt = "|{[\?].* ".$loop_name.".*}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/}|U";				
				$if_contents_ = eregi_replace("\"","\\\"", $if_contents_);			
				
				$if_content = str_replace("<!--","",$if_content );
				$if_content = str_replace("-->","",$if_content );
				
				
				//echo "<textarea style='width:800px;height:500px;'>";
				//echo $if_all_pt;
				//echo "</textarea>";
				//eval  ("\$this->$var_name = preg_replace(\$if_all_pt,\$if_contents_,\$if_content);");									
				$this->data[$var_name] = preg_replace($if_all_pt,$if_contents_,$if_content);					
			}
	}
	function assign($var_name, $loop_name, $loop_array){				
		if(!is_array($loop_array)){
			//echo $loop_name."<br>";
			//eval ("\$if_content = \$this->$var_name ;");
			
			$if_contents = $this->data[$var_name];
			
			//$if_contents_ = $this->if_contents($loop_name,$loop_array, $if_content);
			//if($if_contents_ == ""){
				//$loop_text = eregi_replace("\"","\\\"", $loop_array);			
				$loop_text = $loop_array;
				//eval ("\$this->$var_name = str_replace('{".$loop_name."}',\"$loop_text\",\$this->$var_name);");
				
				$this->data[$var_name] = str_replace("{".$loop_name."}",$loop_text,$this->data[$var_name]);
				
				if($loop_name == "sub_categorys"){
					//echo $this->data[$var_name];					
				}
			
			/*	
				//echo count($if_contents_);
				//exit;
			}else{
				
				$if_all_pt = "|{[\?].* ".$loop_name.".*}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/}|U";				
				$if_contents_ = eregi_replace("\"","\\\"", $if_contents_);			
				
				$if_content = str_replace("<!--","",$if_content );
				$if_content = str_replace("-->","",$if_content );
				
				
				//echo "<textarea style='width:800px;height:500px;'>";
				//echo $if_all_pt;
				//echo "</textarea>";
				eval  ("\$this->$var_name = preg_replace(\$if_all_pt,\$if_contents_,\$if_content);");					
			}
			*/
			
			
		}else{			
			
				
			//eval ("\$loop_content = \$this->$var_name ;");
			$loop_content = $this->data[$var_name];
			//$loop_content = str_replace("<!-- ","",$loop_content );
			//$loop_content = str_replace(" -->","",$loop_content );			
			$loop_ = $this->loop_contents($loop_name,$loop_content);
			
			if($loop_name == "code"){//sub_categorys
				//echo $this->data[$var_name];
				//echo print_r($loop_);	
				//echo $loop_content;
				//exit;				
			}
			
			if($loop_name == "categorys"){//sub_categorys
				//echo $this->data[$var_name];
				//echo print_r($loop_);	
				//exit;				
			}
			
			$loop_str = $this->replace_loop_value($loop_name, $loop_ [0]);			
			$loop_str = eregi_replace("\"","\\\"", $loop_str);
			//echo $loop_name;
			
			
			if(count($loop_array) > 0){
				
				
					foreach ($loop_array as $key => $sub_array) {
						if(is_array($sub_array)){
							$loop_ing = $loop_str;
							foreach ($sub_array as $key => $value) {
								$value = str_replace("\"","\\\"",$value);
								//$value = str_replace("<","$lt;",$value);
								//echo $value;
								
								eval ("\$".$loop_name."[$key] = \"$value\";");
								
							}
							eval ("\$loog_result .= \"".$loop_ing."\";");
						}else{									
							$sub_array = str_replace("\"","\\\"",$sub_array);	
							eval ("\$".$loop_name."[$key] = \"$sub_array\";");							
						}
					}
					
					if(!is_array($sub_array)){
						$loop_ing = $loop_str;
						eval ("\$loog_result .= \"".$loop_ing."\";");
					}
				
			}
			
			
			
			//removeTmpCode
			
			if(count($loop_array) > 0){
				//$loop_all_pt = "|{@.* ".$loop_name.".*}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/}|U";	
				//eval ("\$this->$var_name = preg_replace(\$loop_all_pt,\$loog_result,\$this->$var_name);");				
				
				//eval ("\$this->$var_name = str_replace(\$loop_[0],\$loog_result,\$this->$var_name);");
				eval ("\$this->data[$var_name] = str_replace(\$loop_[0],\$loog_result,\$this->data[$var_name]);");
				//$this->data[$var_name] = str_replace($loop_[0],$loog_result,$this->data[$var_name]);
				
				//eval ("\$this->$var_name = str_replace(\$loop_[1],'',\$this->$var_name);");				
				eval ("\$this->data[$var_name] = str_replace(\$loop_[1],'',\$this->data[$var_name]);");	
				//$this->data[$var_name] = str_replace($loop_[1],'',$this->data[$var_name]);
				
			//	if($loop_name == "history"){
			//		echo $this->$var_name;
			//		exit;	
			//	}
			}else{
				//echo $loop_[0];
				eval ("\$this->data[$var_name] = str_replace(\$loop_[0],'',\$this->data[$var_name]);");
				//$this->data[$var_name] = str_replace($loop_[0],'',$this->data[$var_name]);
				
			}
			
			
		}
		
	}
	
	
	
	function loop_contents($loop_name,$loop_content){
		//$loop_pt = "|{[^}]+}[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/[^}]+}|U"; 
		//$loop_pt = "|{@ ".$loop_name."[^}]+}[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/".$loop_name."[^}]+}|U"; 
		//$loop_pt = "|{@ ".$loop_name." }[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/".$loop_name." }|U"; 
		//$loop_pt = "|{@ ".$loop_name."}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/[^}]+}|U";
		
		//if($loop_name == "sub_categorys" || $loop_name == "best1" || $loop_name == "best2" || $loop_name == "best3" || $loop_name == "best4" || $loop_name == "best5" ){			
		//	$loop_pt = "|<!--{@ ".$loop_name."+}-->[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*<!--{/}-->|U";
		//}else{
			$loop_pt = "|{@ ".$loop_name."+}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/}|U";	
		//}
		
		
		//echo $loop_pt;
		//echo $loop_content;
		
		preg_match_all("$loop_pt", "$loop_content", $out, PREG_PATTERN_ORDER);

		
		//for($i=0;$i < count($out);$i++){
			for($j=0;$j < count($out[1]);$j++){
					$loop_contents_ = $out[1][$j];				
			}
			//echo "\n\n\n\n";
		//}
		
		
		$loop_ = split("{:}",$loop_contents_);
		//list($loop_,$loop_noting) = split("{:$loop_name}",$loop_contents_);
		
		return $loop_;
	}
	
	
	
	function if_contents($if_name,$if_value, $if_content){
		//$loop_pt = "|{[^}]+}[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/[^}]+}|U"; 
		//$loop_pt = "|{@ ".$loop_name."[^}]+}[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/".$loop_name."[^}]+}|U"; 
		//$loop_pt = "|{@ ".$loop_name." }[\-\>\n.*]*([^-]*)[\-\<\n.*]*{/".$loop_name." }|U"; 
		//$if_pt = "|{? ".$if_name."}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/[^}]+}|U";
		//$if_pt = "|{? ".$if_name."[^}]+}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/}|U";
		
		
		
		//$if_pt = "|{? ".$if_name."[^}]+}[\-\>\r\n.*]*([^~]*).*{/}|U";
		//$if_pt = "/{[\:\?].* ".$if_name."=='".$if_value."'.*}[\-\>\r\t\n.*]*(.*?)[!\-\<\r\t\n.*]*{[\:|\/]+|".$if_name."!='".$this->convert_reg_pattern($if_value)."'.*}[\-\>\r\t\n.*]*(.*?)[!\-\<\r\t\n.*]*{[\:|\/]+/";
		//$if_pt = "/{[\:\?].* ".$if_name."=='".$if_value."'.*}[\-\>\r\t\n.*]*([^~]*)[!\-\<\r\t\n.*]*{[\:|\/]+|".$if_name."!='".$this->convert_reg_pattern($if_value)."'.*}[\-\>\r\t\n.*]*(.*?)[!\-\<\r\t\n.*]*{[\:|\/]+/";
		$if_pt = "/{[\:\?].* ".$if_name."=='".$if_value."'.*}[\-\>\r\t\n.*]*(.*?)[!\-\<\r\t\n.*]*{[\:|\/]+|".$if_name."!='".$this->convert_reg_pattern($if_value)."'.*}[\-\>\r\t\n.*]*(.*?)[!\-\<\r\t\n.*]*{[\:|\/]+/";
		
		//$loop_pt = "|{@ ".$loop_name."+}[\-\>\r\n.*]*([^~]*)[\-\<\r\n.*]*{/}|U";
		//echo $if_pt;
		
		
		
		
		preg_match_all("$if_pt", "$if_content", $out, PREG_PATTERN_ORDER);
		if($if_name == "code" && false){
			echo "<textarea cols=100 rows=50>";
			print_r($out);
			echo "</textarea>";
			exit;
		}
		
		for($i=0;$i < count($out);$i++){			
			for($j=0;$j < count($out[$i]);$j++){								
				if($i != 0 && $out[$i][$j] != ""){
					$if_content_ = $out[$i][$j];
					//echo $out[$i][$j]."<br><br><br>\n\n\n\n";				
				}
			}
			//echo "\n\n\n\n";
		}
		
		/*
		if($if_name == "code"){
			print_r($if_content_);
			exit;
		}
		*/
		
		//echo $if_content_;		
		//exit;
		
		//$if_content_ = preg_split("/{:.*}/",$if_content_);
		
		
		/*
		echo $if_content_[0]."<br>\n";
		echo $if_content_[1]."<br>\n";
		echo $if_content_[2]."<br>\n";
		exit;
		*/
		//list($loop_,$loop_noting) = split("{:$loop_name}",$loop_contents_);
		$if_content_ = str_replace("<!--","",$if_content_ );
		$if_content_ = str_replace("-->","",$if_content_ );
		return $if_content_;
	}
	
	function mb_str_split($str, $length = 1) {
	  if ($length < 1) return FALSE;
	
	  $result = array();
	
	  for ($i = 0; $i < mb_strlen($str); $i += $length) {
	   $result[] = mb_substr($str, $i, $length);
	  }
	
	  return $result;
	}

	
	function convert_reg_pattern($str){		
		//$str_arry = $this->mb_str_split($str);		
		$str_arry = str_split($str);
		
		$mstr = "";
		for($i=0;$i<count($str_arry);$i++){			
			$mstr .= "[^".$str_arry[$i]."]?";	
		}
		
		return $mstr;
	}
	
	function print_($var_name){		
		//eval("echo \$this->$var_name;");
		echo $this->data[$var_name];
	}
	
	function get_contents_($var_name){				
		//eval("\$return_value = \$this->$var_name;");
		eval("\$return_value = \$this->data[$var_name];");
		//$return_value = $this->data[$var_name];
		
		return $return_value;
		//eval("return \$this->$var_name;");
	}
	
	function removeTmpCode($var_name, $source)
	{
		//$source = preg_replace('@{.*?}[ \t]*(\r\n|\n|\r)?\s*@s', '', $source);		
		//$source = preg_replace('@<!---->[ \t\r\n]*(\r\n|\n|\r)?\s*@s', '', $source);		
		return preg_replace('@<!--{.*?}-->[ \t\r\n]*(\r\n|\n|\r)?\s*@s', '', $source);
		
		//$source = preg_replace('@{.*?}[ \t]*(\r\n|\n|\r)?\s*@s', '', $source);
		//return preg_replace('@<!--{.*?}-->[ \t]*(\r\n|\n|\r)?\s*@s', '', $source);
		
		//$source = preg_replace('@[ \t\r\n\-\<\!]{.*?}[ \t\r\n\-\>\!]*(\r\n|\n|\r)?\s*@s', '', $source);
		//return preg_replace('@[ \t\r\n]<!--{.*?}-->[ \t\r\n]*(\r\n|\n|\r)?\s*@s', '', $source);
	}
	
	function get_contents($var_name){		
		
		
	
		//eval ("\$this->$var_name = \$this->removeTmpCode(\$var_name, \$this->$var_name);");
		eval ("\$this->data[$var_name] = \$this->removeTmpCode(\$var_name, \$this->data[$var_name]);");
		//$this->data[$var_name] = $this->removeTmpCode($var_name, $this->data[$var_name]);
		
		eval("\$return_value = \$this->data[$var_name];");
		//echo $return_value;
		//exit;
		return $return_value;
		//eval("return \$this->$var_name;");
	}
	
	
}

if(!function_exists('str_split')){ 
   function str_split($string,$split_length=1){ 
       $count = strlen($string);  
       if($split_length < 1){ 
           return false;  
       } elseif($split_length > $count){ 
           return array($string); 
       } else { 
           $num = (int)ceil($count/$split_length);  
           $ret = array();  
           for($i=0;$i<$num;$i++){  
               $ret[] = substr($string,$i*$split_length,$split_length);  
           }  
           return $ret; 
       }      
   }  
}


?>