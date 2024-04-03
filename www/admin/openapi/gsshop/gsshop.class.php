<?php

class Call_gsshop {
	protected $serverUrl;
	
	/**
	 * CALL
	 *
	 * @param string $serverUrl        	
	 * @param string $actionUrl        	
	 * @param string $postData        	
	 * @return DomDocument
	 */
	function call($serverUrl = '', $postData = NULL, $dataBuild = true) {
		$this->serverUrl = $serverUrl;
		
		try {
			
			$print_yn = false;
			
			if($print_yn){
				echo '<br/>----data-----<br/>';
				print_r($postData);
			}

			//if($postData != NULL){
				if($dataBuild){
					$data = $this->buildGSshopData ( $postData );
				}else{
					$data = $postData;
				}
			//}

			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $this->serverUrl );
			if( substr_count($this->serverUrl,'https://') > 0 ){
				curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
				curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, TRUE );
			}else{
				curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
				curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
			}
			//if($postData != NULL){
				curl_setopt ( $ch, CURLOPT_POST, TRUE );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
			//}

			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			$response = curl_exec ( $ch );
			curl_close ( $ch );
			//echo '----response---'.print_R($response);

			$response = mb_convert_encoding($response,'UTF-8','EUC-KR');
			
			if($print_yn){
				echo '<br/>----response-----<br/>';
				print_r($response);
			}

			return $response;
		} catch ( Exception $e ) {
			echo $e->getMessages ();
		}
	}

	private function buildGSshopData($postData) {
		
		$str = "";
		if( count($postData) > 0 ){
			foreach($postData as $dt){

				$value = current($dt);
				$value = mb_convert_encoding($value,'EUC-KR','UTF-8');

				$str .= "&".key($dt)."=".urlencode($value);
			}
		}
		
		$str = substr($str,1);
		return $str;
	}
}