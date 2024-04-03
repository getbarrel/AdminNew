<?php
		## PEAR SOAP를 사용
		//echo $_SERVER["DOCUMENT_ROOT"];
		ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
		$install_path = "../../../include/";
		require_once 'SOAP/Server.php';
		//$install_path = "/lib/";
		
		$server = new SOAP_Server;
		$server->_auto_translation = true;

		require_once '../../../include/lib.function.php';
		require_once 'server_api.php'; ## 실제 서버 API 파일

		$soapclass = new SOAP_FORBIZ_CoGoods_Server();
		$server->addObjectMap($soapclass,'urn:SOAP_FORBIZ_CoGoods_Server');

		if (isset($_SERVER['REQUEST_METHOD']) &&
		$_SERVER['REQUEST_METHOD']=='POST') {
				$server->service($HTTP_RAW_POST_DATA);
		} else {
				require_once 'SOAP/Disco.php';
				$disco = new SOAP_DISCO_Server($server,'FORBIZ_Server');
				header("Content-type: text/xml");
				if (isset($_SERVER['QUERY_STRING']) &&
				strcasecmp($_SERVER['QUERY_STRING'],'wsdl')==0) {
						echo $disco->getWSDL();
				} else {
						echo $disco->getDISCO();
				}
				exit;
		}
?>

