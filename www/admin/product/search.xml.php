<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
header("Pragma: no-cache");
header('Content-type: text/xml;');

if($search_text){
	$db = new Database;
	if($search_type == ""){
		$search_type = "pname";
	}
	//echo "Select $search_type, id from ".TBL_SHOP_PRODUCT." where $search_type LIKE '%".iconv('EUC-KR','UTF-8',$search_text)."%' ";
	$db->query("Select $search_type, id from ".TBL_SHOP_PRODUCT." p where $search_type LIKE '%".iconv('EUC-KR','UTF-8',$search_text)."%' limit 30");
	//$db->query("Select $search_type, id from ".TBL_SHOP_PRODUCT." where $search_type LIKE '%".$search_text."%' ");
	
	//$db->query("Select pname from ".TBL_SHOP_PRODUCT." where pname LIKE '".iconv('EUC-KR','UTF-8',$search_text)."%'");
	//echo ("Select pname from ".TBL_SHOP_PRODUCT." where pname = '".$search_text."%'");
	
	if($db->total){
		$xmlTmp = "<response>";
		for($i=0;$i < $db->total; $i++){
			$db->fetch($i);		
			$xmlTmp .= "	<pid>". $db->dt[id]."</pid>";
			//$xmlTmp .= "	<name>". iconv('EUC-KR','UTF-8',$db->dt[0])."</name>";
			$xmlTmp .= "	<name>". cut_str($db->dt[0],36)."</name>";
			//$xmlTmp .= "	<name>".utf8_encode($db->dt[pname])."</name>";
		}
		$xmlTmp .= "</response>";
	}else{
	//	$xmlTmp = "<response>";	
	//	$xmlTmp .= "	<name>".iconv('EUC-KR','UTF-8','대밴寃곌낵媛 議댁吏 듬')."</name>";	
	//	$xmlTmp .= "</response>";
	}
	echo $xmlTmp;
}
?>
