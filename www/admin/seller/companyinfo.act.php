<?
include("../../class/database.class");



$db = new Database;


if ($act == "update"){
	
	$mall_companyinfo = $content;
	
	
	$sql = "update ".TBL_SHOP_SHOPINFO." Set ";
	$sql = $sql." mall_companyinfo  = '$mall_companyinfo' where mall_ix = '$mall_ix'";
	
	
	//echo $sql;
	$db->query($sql);
	
	$db->query("select mall_companyinfo from ".TBL_SHOP_SHOPINFO."");
	$db->fetch();
//	echo $content;

	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/shop_templete/".$admin_config[mall_use_templete]."/ms_companyinfo.htm","w");
	fwrite($fp,$db->dt[mall_companyinfo]);	

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = '../store/companyinfo.php';</script>");
}

?>
