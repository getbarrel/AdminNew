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
			CERTKEY  		=> "6657F586-C85B-4283-9E80-94D5A44D03C6",
			CorpNum			=> "4168138772",
			CorpName		=> "(주)테스트",
			CEOName			=> "테스트",
			BizType			=> "서비스업",
			BizClass		=> "소프트웨어",
			PostNum			=> "000-000",
			Addr1			=> "광주광역시",
			Addr2			=> "주소",
			MemberName		=> "으흐흐흐",
			JuminNum		=> "000000-0000000",
			ID				=> "test33",
			PWD				=> "test33",
			Grade			=> "과장",
			TEL				=> "010-0000-0000",
			HP				=> "010-0000-0000",
			Email				=> "test@test.com"
		);

		// Soap 문장 전송
		$result = $client->call('RegistCorp', $params, '', '', false, true);

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
				echo '<h2>Result</h2><pre>';
				print_r($result);
				echo '</pre>';
				// 각 파라미터별 값을 배열에서 가져 오기 위한 처리
				$RegistCorpResult = $result['RegistCorpResult'];
				if( $RegistCorpResult == 1) {
					echo "등록성공";
				} else {
					$errCode = errCodeCheck($RegistCorpResult);
						echo $errCode;
				}
			}
		}

	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>