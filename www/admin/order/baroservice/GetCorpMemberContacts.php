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

		// 보낼 argument 배열로 함.
		$params = array(
			CERTKEY  	=> "6657F586-C85B-4283-9E80-94D5A44D03C6",
			CorpNum		=> "4168138772",
			CheckCorpNum	=> "4168138772"
		);
		
		// Soap 문장 전송		
		$result = $client->call('GetCorpMemberContacts', $params, '', '', false, true);
		
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
				$GetCorpMemberContactsResult = $result['GetCorpMemberContactsResult'];
				$GetCorpMemberContactsResult_Contact = $GetCorpMemberContactsResult['Contact'];

				// 각 파라미터별 값에 대한 출력
				echo ("ID==>" . $GetCorpMemberContactsResult_Contact[0]['ID'] ."<BR>"); 
				echo ("ContactName==>" . $GetCorpMemberContactsResult_Contact[0]['ContactName'] ."<BR>"); 
				echo ("Grade==>" . $GetCorpMemberContactsResult_Contact[0]['Grade'] ."<BR>"); 
				echo ("Email==>" . $GetCorpMemberContactsResult_Contact[0]['Email'] ."<BR>"); 
				echo ("TEL==>" . $GetCorpMemberContactsResult_Contact[0]['TEL'] ."<BR>"); 
				echo ("HP==>" . $GetCorpMemberContactsResult_Contact[0]['HP'] ."<BR>"); 

			}
		}
	} catch (Exception $e) {
		echo '접속오류: ',  $e->getMessage(), "\n";
	}
?>