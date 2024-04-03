<?php

class Call_demandship {
	protected $server_host;
	protected $api;
	protected $access_token;
	public $method=1;
	public $is_message_display= false;
	
	/**
	 * CALL
	 *
	 * @param string $server_host
	 * @param string $actionUrl
	 * @param string $postData
	 * @return DomDocument
	 */
	public function call($server_host = '', $api = '', $postData = NULL) {
		$this->server_host = $server_host;
		$this->api = $api;
		
		try {
		if($this->is_message_display){
		echo "<br>";
		echo $server_host.$api."<br>";
		echo "============ postData ===========";
		
		$postData_print[description] ="";
		echo "<pre>";echo print_r($postData_print);
		echo "<br>";
		echo "method:".$this->method."<br>";
		echo "<br><br>";
		//exit;
		}
		$headers = $this->buildAuctionHeaders($this->access_token, $postData);
		if($this->is_message_display){
			echo "<pre>";print_r($headers);
			echo "<br>";
		}

		$ch = curl_init(); 
		if($this->method == 1){
		curl_setopt ($ch, CURLOPT_URL,$this->server_host . $this->api);                      // 접속할 URL 주소 
		}else{
			$postDataArray = json_decode($postData, true);
		//	print_r($postDataArray);
			$param = $this->build_http_query($postDataArray);
			$param = "access_token=".$this->access_token."&".$param;
			//echo $param;
			//exit;
		//	echo $this->server_host . $this->api."?".$param."<br>";
		curl_setopt ($ch, CURLOPT_URL,$this->server_host . $this->api."?".$param);                      // 접속할 URL 주소 
		}
		//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
		//curl_setopt ($ch, CURLOPT_SSLVERSION,3);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
		curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		if($this->method == 1){
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt ($ch, CURLOPT_POST, $this->method);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);     // 전송할 POST 값입니다.
		} 
		//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
		//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec ($ch); 
		return $result;
		/*
			$ch = curl_init ();

			curl_setopt ( $ch, CURLOPT_URL, $this->server_host . $this->api );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt ( $ch, CURLOPT_HEADER, 0);
			curl_setopt ( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			if($this->method == 1){
			curl_setopt ( $ch, CURLOPT_POST, TRUE );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData);
			}
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			
			$response = curl_exec ( $ch );
			curl_close ( $ch );
			return json_decode( $response , true );
		*/
		} catch ( Exception $e ) {
			echo $e->getMessages ();
		}
	}

	function build_http_query( $query ){

		$query_array = array();

		foreach( $query as $key => $key_value ){

			$query_array[] = urlencode( $key ) . '=' . urlencode( $key_value );

		}

		return implode( '&', $query_array );

	}

	function buildAuctionHeaders($access_token, $datas) {
		//echo $access_token;
		if(strlen($datas) > 0 && $this->method == POST){
			$headers = array (
					"Content-Type: application/json; charset=utf-8",
					"Content-Length: ".strlen ( $datas )."",
					"Authorization: Bearer ".$access_token.""
			);

		}else{
			$headers = array (
					"Content-Type: application/json; charset=utf-8", 
					"Authorization: Bearer ".$access_token.""			 
			);
		}
		
		return $headers;
	}

	public function access_token() {

	}
}