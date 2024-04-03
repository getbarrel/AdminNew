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
		//ProcType 타입은 테이블 참고
		$params = array(
			CERTKEY  		=> "41002A15-CBDA-4C0B-96F1-B81B819BB119",
			CorpNum			=> "2141009837",
			MemberName		=> "324235234"
			
		);

		// Soap 문장 전송
		$result = $client->call('CheckMgtNumIsExists', $params, '', '', false, true);

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
				$CheckMgtNumIsExistsResult = $result['CheckMgtNumIsExistsResult'];				
				switch ($CheckMgtNumIsExistsResult) {
					case 1:
						echo "해당 발행자 관리번호는 등록이 되어 있습니다.";
						break;
					case 2:
						echo "해당 발행자 관리번호는 등록되지 않았습니다.";
						break;
					default:
						$errCode = errCodeCheck($CheckMgtNumIsExistsResult);
					echo $errCode;
						break;
				}
			}
		}

	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>