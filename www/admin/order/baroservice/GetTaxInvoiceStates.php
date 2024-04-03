<?php
	require_once('lib/nusoap.php');
	require_once("./GetErrString.php");
	
	try {		
		$client = new nusoap_client("https://testws.baroservice.com:8010/ti.asmx?WSDL", true);
		$client->xml_encoding = "utf-8";
		$client->soap_defencoding = "utf-8";
		$client->decode_utf8 = false;
		
		$err = $client->getError();
		if ($err) {
			// 접속 오류에 대한 대응
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}


		$params = array(
			CERTKEY  	=> "6657F586-C85B-4283-9E80-94D5A44D03C6",
			CorpNum		=> "4168138772",
			MgtKeyList	=> array(string => array("INTEROP20100830142015","INTEROP20100830142016"))
					
		);
		
		// Soap 문장 전송		
		$result = $client->call('GetTaxInvoiceStates', $params, '', '', false, true);
		
		if ($client->fault) {
			// Soap 문장 오류시 예외처리
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; 
			print_r($result); echo '</pre>';
		} else {
			$err = $client->getError();
			if ($err) {
				// Soap 결과 오류시 예외처리
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				// 정상적으로 응답이 오는 경우에 대한 처리

				// 결과 문장에 대한 출력
				echo '<h2>Result</h2><pre>'; 
				print_r($result); 
				echo '</pre>';
				
				// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
				$GetTaxInvoiceStatesResult = $result['GetTaxInvoiceStatesResult'];				
				//if( strlen($GetTaxInvoiceStatesResult) == 4) {
					echo "<br />발행자 관리번호 :". $GetTaxInvoiceStatesResult['TaxInvoiceState'][0]['MgtKey'];
					echo "<br />반환값 :". $GetTaxInvoiceStatesResult['RetVal'];
					echo "<br />바로빌 관리번호 :". $GetTaxInvoiceStatesResult['InvoiceKey'];
					echo "<br />비고1 :". $GetTaxInvoiceStatesResult['Remark1'];
					echo "<br />비고2 :". $GetTaxInvoiceStatesResult['Remark2'];
				//} else {
				//	echo $errMsg[$GetTaxInvoiceStatesResult];
				///}
				echo "<br />";
				if( strlen($GetTaxInvoiceStatesResult['RetVal']) == 4) {
					switch (substr($GetTaxInvoiceStatesResult['RetVal'], 0,1)) {					
						case 1:
							echo "임시저장";
							break;
						case 2:
							echo "진행중";
							break;
						case 3:
							echo "완료";
							break;
						case 4:
							echo "거부됨";
							break;
						case 5:
							echo "취소됨";
							break;					
					}
					
					switch (substr($GetTaxInvoiceStatesResult['RetVal'], 1,2)) {					
						case "01":
							echo "정발행승인요청";
							break;
						case "02":
							echo "역발행요청";
							break;
						case "03":
							echo "취소요청(공급자)";
							break;
						case "04":
							echo "취소요청(공급받는자)";
							break;
						case "05":
							echo "내부발행요청";
							break;					
						case "06":
							echo "내부발행 요청";
							break;	
					}
						
					switch (substr($GetTaxInvoiceStatesResult['RetVal'], 3,1)) {					
						case 1:
							echo "미처리";
							break;
						case 2:
							echo "승인";
							break;
						case 3:
							echo "거부";
							break;
						case 4:
							echo "자체취소";
							break;
					}
					
				} else {
					echo $errMsg[$GetTaxInvoiceStatesResult['RetVal']];
				}
				
			}
		}
		
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>