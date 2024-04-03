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
			MgtKey		=> "INTEROP20100830142015"
		);

		// Soap 문장 전송
		$result = $client->call('GetAttachedFileList', $params, '', '', false, true);

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
				$GetAttachedFileListResult = $result['GetAttachedFileListResult']["AttachedFile"];
				if( $GetAttachedFileListResult != null) {
					//$GetAttachedFileListResult["AttachedFile"]
					$count = count($GetAttachedFileListResult);
					for ($i = 0; $i < $count; $i++) {
						echo "FileIndex: " . $GetAttachedFileListResult[$i]['FileIndex'] . "<br/>";
						echo "FileName: " . $GetAttachedFileListResult[$i]['FileName'] . "<br/>";
						echo "DisplayFileName: ".$GetAttachedFileListResult[$i]['DisplayFileName']."<br/>";
					}
				} else {
					echo $errMsg[$GetAttachedFileListResult];
				}
			}
		}

	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>