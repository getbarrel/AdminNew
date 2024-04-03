<?php

class Call_cjmall {
	protected $serverUrl;
	
	/**
	 * CALL
	 *
	 * @param string $serverUrl        	
	 * @param string $actionUrl        	
	 * @param string $postData        	
	 * @return DomDocument
	 */
	function call($serverUrl = '', $postData = NULL) {
		$this->serverUrl = $serverUrl;
		
		try {
			/*
			$header = array();
			$url = $this->serverUrl;
			
			$postData = preg_replace(array("/\r\n/","/\t/"),"",$postData);

			$header[] = "Content-Type: text/xml; charset=utf-8";
			$header[] = "Content-Length: ".strlen ( $postData )."";
			$header[] = "SOAPAction: ".$this->actionUrl."";
			
			$data = "--data '".$postData."'";
			$header = implode("' -H '", $header);
			$command = "curl -H '".$header."' ".$data." ".$url."";

			$response = shell_exec( $command );
			*/
			
			$headers = $this->buildcjmallHeaders ( strlen ( $postData ) );
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $this->serverUrl );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
			curl_setopt ( $ch, CURLOPT_POST, TRUE );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			#echo $postData;
			#exit;
			$response = curl_exec ( $ch );
			//echo iconv('euc-kr', 'utf-8', $response);
			//exit;
			curl_close ( $ch );
		
			$responseDoc = new DomDocument ();
			$responseDoc->loadXML ( $response );
			
			/* Soap result를 simplexml Object로 변환 */
			$xmlString = $responseDoc->saveXML ();
			$xmlObj = simplexml_load_string ( str_replace ( 'ns1:', '', $xmlString ) );
			
			return $xmlObj;
		} catch ( Exception $e ) {
			echo $e->getMessages ();
		}
	}
	private function buildcjmallHeaders($postDataLength) {
		$headers = array (
				"Content-Type: text/xml; charset=utf-8"
		);
		//"Content-Length: $postDataLength"

		return $headers;
	}
}