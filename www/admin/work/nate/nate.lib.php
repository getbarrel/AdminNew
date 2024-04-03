<?
Class NateAuth {

		var $oauth_consumer_key ; // Your consumer key
		var $oauth_consumer_key_secret ; // Your consumer key secret
		var $oauth_signature_method;
		var $oauth_timestamp ;
		var $oauth_nonce; // md5s look nicer than numbers;
		var $oauth_version ;
		var $oauth_callback ;// Your Call Back Page URL
		var $get_request_token_url ;
		var $oauth_token;
		var $oauth_token_secret;
		var $oauth_signature;
		var $request_token;
		var $request_token_secret;
		var $debug;

	function NateAuth(){
		$this->oauth_consumer_key = "b13ca9da99cfe9f74690a4a9ab6c4b9b04d7b433d"; // Your consumer key
		$this->oauth_consumer_key_secret = "15f90718cab4b8aa1c31c3671d2dc036"; // Your consumer key secret
		$this->oauth_signature_method = "HMAC-SHA1";
		$this->oauth_timestamp = time();
		$this->oauth_nonce = md5(microtime().mt_rand()); // md5s look nicer than numbers;
		$this->oauth_version = "1.0";
		$this->oauth_callback = "https://dev.forbiz.co.kr/admin/work/nate/step2.php";// Your Call Back Page URL
		$this->get_request_token_url = "https://oauth.nate.com/OAuth/GetRequestToken/V1a";
		$this->oauth_token = "";
		$this->oauth_token_secret = "";
		$this->oauth_signature = "";
		$this->request_token = "";
		$this->request_token_secret = "";
		$this->debug = false;
	}

	function GetRequestToken(){
		$strMysqlHost = "127.0.0.1:3306";
		$strMysqlID = "forbiz";
		$strMysqlPassword = "vhqlwm2011";


		


		//Get Request Token

		//Generate Base String For Get Request Token
		//!!파라메터 이름 순서로 조합해야 한다.
		//!!파라메터의 이름과 값은 rfc3986 으로 encode
		//[Name=Valeu&Name=Value…] 형식으로 연결
		$Query_String  = $this->urlencode_rfc3986("oauth_callback")."=".$this->urlencode_rfc3986($this->oauth_callback);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_consumer_key")."=".$this->urlencode_rfc3986($this->oauth_consumer_key);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_nonce")."=".$this->urlencode_rfc3986($this->oauth_nonce);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_signature_method")."=".$this->urlencode_rfc3986($this->oauth_signature_method);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_timestamp")."=".$this->urlencode_rfc3986($this->oauth_timestamp);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_version")."=".$this->urlencode_rfc3986($this->oauth_version);

		if($this->debug){
			echo("Query_String=".$Query_String."<br /><br />");
		}
		//Base String 요소들을 rfc3986 으로 encode
		$Base_String = $this->urlencode_rfc3986("POST")."&".$this->urlencode_rfc3986($this->get_request_token_url)."&".$this->urlencode_rfc3986($Query_String);
		if($this->debug){
			echo("Base_String=".$Base_String."<br /><br />");
		}

		//지금 단계에서는 $this->oauth_token_secret이 ""
		$Key_For_Signing = $this->urlencode_rfc3986($this->oauth_consumer_key_secret)."&".$this->urlencode_rfc3986($this->oauth_token_secret);
		if($this->debug){
			echo("Key_For_Signing=".$Key_For_Signing."<br /><br />");
		}

		//oauth_signature 생성
		$this->oauth_signature=base64_encode(hash_hmac('sha1', $Base_String, $Key_For_Signing, true));
		if($this->debug){
			echo("oauth_signature=".$this->oauth_signature."<br /><br />");
		}

		//Authorization Header 조합
		$Authorization_Header  = "Authorization: OAuth ";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_version")."=\"".$this->urlencode_rfc3986($this->oauth_version)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_nonce")."=\"".$this->urlencode_rfc3986($this->oauth_nonce)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_timestamp")."=\"".$this->urlencode_rfc3986($this->oauth_timestamp)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_consumer_key")."=\"".$this->urlencode_rfc3986($this->oauth_consumer_key)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_callback")."=\"".$this->urlencode_rfc3986($this->oauth_callback)."\",";

		$Authorization_Header .= $this->urlencode_rfc3986("oauth_signature_method")."=\"".$this->urlencode_rfc3986($this->oauth_signature_method)."\",";

		$Authorization_Header .= $this->urlencode_rfc3986("oauth_signature")."=\"".$this->urlencode_rfc3986($this->oauth_signature)."\"";
		if($this->debug){
			echo("Authorization_Header=".$Authorization_Header."<br /><br />");
		}
		$parsed = parse_url($this->get_request_token_url);
		$scheme = $parsed["scheme"];
		$path = $parsed["path"];
		$ip = $parsed["host"];
		$port = @$parsed["port"];	

		
		if ($scheme == "http")
		{
			if(!isset($parsed["port"])) { $port = "80"; } else { $port = $parsed["port"]; };
			$tip = $ip;
		} else if ($scheme == "https")
		{
			if(!isset($parsed["port"])) { $port = "443"; } else { $port = $parsed["port"]; };
			$tip =  "ssl://" . $ip;
		} 
		$timeout = 5;
		$error = null;
		$errstr = null;

		//Request 만들기
		$out  = "POST " . $path . " HTTP/1.1\r\n";
		$out .= "Host: ". $ip . "\r\n";
		$out .= $Authorization_Header . "\r\n";
		$out .= "Accept-Language: ko\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-Length: 0\r\n\r\n";//Request Token 받기에서는 post body에 들어가는 파라메터가 없어서 0
		if($this->debug){
			echo("Request=".$out."<br /><br />");
		}
		//Request 보내기
		
		$fp = fsockopen($tip, $port, $errno, $errstr, $timeout);

		//Reponse 받기
		if (!$fp) {
			echo("ERROR!!");
		} else {
			fwrite($fp, $out);
			$response = "";
			while ($s = fread($fp, 4096)) {
				$response .= $s;
			}
			if($this->debug){
				echo("response=".$response."<br /><br />");
			}
			
			//Response Header와 Body 분리
			$bi = strpos($response, "\r\n\r\n");
			$body = substr($response, $bi+4);
			
			//정상적인 경우 $body값은
			//oauth_token=5a3377a10ad1f2c937e7bd8c83e57bec&oauth_token_secret=5be6580cc3e8ea2c71a1c56106c19c1f&oauth_callback_confirmed=true	
			//의 형식으로 떨어짐.
			$tmpArray = explode("&",$body);
			$TokenArray = 	explode("=",$tmpArray[0]);
			$TokenSCArray = 	explode("=",$tmpArray[1]);
			$this->request_token = $TokenArray[1];
			$this->request_token_secret = $TokenSCArray[1];
			
			//request_token, request_token_secret 출력
			if($this->debug){
			echo ("request_token = ".$this->request_token."<br /><br />");
			echo ("request_token_secret = ".$this->request_token_secret."<br /><br />");
			}
		//exit;	
			//request_token, request_token_secret DB저장	
			$mysql_connect=mysql_connect($strMysqlHost,$strMysqlID,$strMysqlPassword);
			
			$sql = "insert into dev.tbRequestToken (TOKEN,TOKEN_SC) values ('".$this->request_token."', '".$this->request_token_secret."');";
			//echo $sql;
			$result = mysql_query($sql);
			
			mysql_close($mysql_connect);
			//exit;
		}
	}

	function SendNote(){
		
		$send_memo_url = "https://openapi.nate.com/OApi/RestApiSSL/ON/250060/nateon_SendNote/v1";
		//Generate Base String For Get Request Token
		//!!파라메터 이름 순서로 조합해야 한다.
		//!!파라메터의 이름과 값은 rfc3986 으로 encode
		//[Name=Valeu&Name=Value…] 형식으로 연결
		$Query_String  = $this->urlencode_rfc3986("oauth_callback")."=".$this->urlencode_rfc3986($this->oauth_callback);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_consumer_key")."=".$this->urlencode_rfc3986($this->oauth_consumer_key);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_nonce")."=".$this->urlencode_rfc3986($this->oauth_nonce);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_signature_method")."=".$this->urlencode_rfc3986($this->oauth_signature_method);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_timestamp")."=".$this->urlencode_rfc3986($this->oauth_timestamp);
		$Query_String .= "&";
		$Query_String .= $this->urlencode_rfc3986("oauth_version")."=".$this->urlencode_rfc3986($this->oauth_version);

		if($this->debug){
			echo("Query_String=".$Query_String."<br /><br />");
		}
		//Base String 요소들을 rfc3986 으로 encode
		$Base_String = $this->urlencode_rfc3986("POST")."&".$this->urlencode_rfc3986($this->get_request_token_url)."&".$this->urlencode_rfc3986($Query_String);
		if($this->debug){
			echo("Base_String=".$Base_String."<br /><br />");
		}

		//지금 단계에서는 $this->oauth_token_secret이 ""
		$Key_For_Signing = $this->urlencode_rfc3986($this->oauth_consumer_key_secret)."&".$this->urlencode_rfc3986($this->oauth_token_secret);
		if($this->debug){
			echo("Key_For_Signing=".$Key_For_Signing."<br /><br />");
		}

		//oauth_signature 생성
		$this->oauth_signature=base64_encode(hash_hmac('sha1', $Base_String, $Key_For_Signing, true));
		if($this->debug){
			echo("oauth_signature=".$this->oauth_signature."<br /><br />");
		}

		//Authorization Header 조합
		$Authorization_Header  = "Authorization: OAuth ";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_version")."=\"".$this->urlencode_rfc3986($this->oauth_version)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_nonce")."=\"".$this->urlencode_rfc3986($this->oauth_nonce)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_timestamp")."=\"".$this->urlencode_rfc3986($this->oauth_timestamp)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_consumer_key")."=\"".$this->urlencode_rfc3986($this->oauth_consumer_key)."\",";
		$Authorization_Header .= $this->urlencode_rfc3986("oauth_callback")."=\"".$this->urlencode_rfc3986($this->oauth_callback)."\",";

		$Authorization_Header .= $this->urlencode_rfc3986("oauth_signature_method")."=\"".$this->urlencode_rfc3986($this->oauth_signature_method)."\",";

		$Authorization_Header .= $this->urlencode_rfc3986("oauth_signature")."=\"".$this->urlencode_rfc3986($this->oauth_signature)."\"";
		if($this->debug){
			echo("Authorization_Header=".$Authorization_Header."<br /><br />");
		}
		$parsed = parse_url($send_memo_url);
		$scheme = $parsed["scheme"];
		$path = $parsed["path"];
		$ip = $parsed["host"];
		$port = @$parsed["port"];	

		
		if ($scheme == "http")
		{
			if(!isset($parsed["port"])) { $port = "80"; } else { $port = $parsed["port"]; };
			$tip = $ip;
		} else if ($scheme == "https")
		{
			if(!isset($parsed["port"])) { $port = "443"; } else { $port = $parsed["port"]; };
			$tip =  "ssl://" . $ip;
		} 
		$timeout = 5;
		$error = null;
		$errstr = null;

		//Request 만들기
		$out  = "POST " . $path . " HTTP/1.1\r\n";
		$out .= "Host: ". $ip . "\r\n";
		$out .= $Authorization_Header . "\r\n";
		$out .= "Accept-Language: ko\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-Length: 0\r\n\r\n";//Request Token 받기에서는 post body에 들어가는 파라메터가 없어서 0
		if($this->debug){
			echo("Request=".$out."<br /><br />");
		}
		//Request 보내기
		
		$fp = fsockopen($tip, $port, $errno, $errstr, $timeout);

		//Reponse 받기
		if (!$fp) {
			echo("ERROR!!");
		} else {
			fwrite($fp, $out);
			$response = "";
			while ($s = fread($fp, 4096)) {
				$response .= $s;
			}
			if($this->debug){
				echo("response=".$response."<br /><br />");
			}
			
			//Response Header와 Body 분리
			$bi = strpos($response, "\r\n\r\n");
			$body = substr($response, $bi+4);
			
			//정상적인 경우 $body값은
			//oauth_token=5a3377a10ad1f2c937e7bd8c83e57bec&oauth_token_secret=5be6580cc3e8ea2c71a1c56106c19c1f&oauth_callback_confirmed=true	
			//의 형식으로 떨어짐.
			$tmpArray = explode("&",$body);
			$TokenArray = 	explode("=",$tmpArray[0]);
			$TokenSCArray = 	explode("=",$tmpArray[1]);
			$this->request_token = $TokenArray[1];
			$this->request_token_secret = $TokenSCArray[1];
			
			//request_token, request_token_secret 출력
			if($this->debug){
			echo ("request_token = ".$this->request_token."<br /><br />");
			echo ("request_token_secret = ".$this->request_token_secret."<br /><br />");
			}
		//exit;	
			//request_token, request_token_secret DB저장	
		//	$mysql_connect=mysql_connect($strMysqlHost,$strMysqlID,$strMysqlPassword);
			
			//$sql = "insert into tbRequestToken (TOKEN,TOKEN_SC) values ('".$this->request_token."', '".$this->request_token_secret."');";
			//echo $sql;
			//$result = mysql_query($sql);
			
			//mysql_close($mysql_connect);
		}
	}

	function urlencode_rfc3986($input) {
		if (is_scalar($input)) {
			return str_replace(
			  '+',
			  ' ',
			  str_replace('%7E', '~', rawurlencode($input))
			);
		  } else {
			return '';
		  }
	}
}
?>