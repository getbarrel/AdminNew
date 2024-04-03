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
			CERTKEY  	=> "6657F586-C85B-4283-9E80-94D5A44D03C6",
			CorpNum		=> "4168138772",
			MgtKey		=> "INTEROP20100217220922",
			ProcType	=> "REFUSE_ISSUEREQ",
			Memo		=>"일사처리"
		);

		// Soap 문장 전송
		$result = $client->call('ProcTaxInvoice', $params, '', '', false, true);

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
				$ProcTaxInvoiceResult = $result['ProcTaxInvoiceResult'];
				if( $ProcTaxInvoiceResult > -1) {
					switch ($ProcTaxInvoiceResult ) {
						case 0:
							echo "동일한 프로세스가 처리중";
							break;
						case 1:
							echo "처리 완료.";
							break;
					}
				} else {
					$errCode = errCodeCheck($ProcTaxInvoiceResult);
						echo $errCode;
				}
			}
		}

	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>