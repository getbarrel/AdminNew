<?
include("../../class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");

$install_path = "../../include/";
include("SOAP/Client.php");
//session_start();
//
	
	$soapclient = new SOAP_Client("http://221.141.3.92/VESAPI/VESAPIWS.asmx?wsdl=0");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:TestApiConsoleProject','trace' => 1);
	
	
	//$ret = $soapclient->call("Tiling ",$params = array("inputMapPathString"=> "/testimages/1.JPG","outputMapPathString"=> "/data/2222"),	$options);
	
	//echo $ret;
	
	$client = new SoapClient("http://221.141.3.92/VESAPI/VESAPIWS.asmx?wsdl=0");
	//print_r($client);
	$params = new stdClass();
	$params->inputMapPathString = "/testimages/1.JPG";
	$params->outputMapPathString = "/data/2222";
	
	$response = $client->Tiling($params);
	print_r($response);
/*
create table co_sellershop_apply (
company_id varchar(32) not null,
co_company_id varchar(32) not null,
apply_status enum('AP','AU') default 'AP',
regdate datetime not null,
primary key(company_id))



*/
?>
