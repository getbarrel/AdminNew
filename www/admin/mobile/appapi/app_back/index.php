<?php

	include_once 'server.php';

	$server = new SOAP_Server();
	
	$server->addObjectMap($AppLib, 'urn:AppLib');

	if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=='POST') {
			$server->service($_POST);

	} else {
	  require_once 'SOAP/Disco.php';

	  $disco = new SOAP_DISCO_Server($server,'server');
	  header("Content-type: text/xml");

	  if (isset($_SERVER['QUERY_STRING']) && strcasecmp($_SERVER['QUERY_STRING'],'wsdl')==0) {
			echo $disco->getWSDL();
	  } else {
			echo $disco->getDISCO();
	  }

	  exit;
	}
