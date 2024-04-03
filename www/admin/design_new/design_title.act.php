<?
include("../class/layout.class");
include('../../include/xmlWriter.php');


$db = new Database;

if($act == "apply"){
	updateModuleTitleXML($templet);
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('타이틀이 정상적으로 적용되었습니다.');parent.document.location.reload();</script>");
}
function updateModuleTitleXML($title_templet){
	global $db, $DOCUMENT_ROOT, $admin_config;

	$xml = new XmlWriter_();

	
	$xml->push('title');
	$xml->element('title_templet', $title_templet); 
	$xml->pop();
	//print $xml->getXml();
	
	$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/module/title_templet/";
	
	//$fileName = "main_flash.xml"; 
	$fp = fopen($dirname."/title.xml","w");
	fputs($fp, $xml->getXml());
	fclose($fp);
}
?>