<?
include("../../class/database.class");



$db = new Database;


if ($act == "update"){
	
	$mall_useinfo = $content;
	

	$sql = "update ".TBL_SHOP_SHOPINFO." Set ";
	$sql = $sql." mall_useinfo  = '$mall_useinfo' where mall_ix = '$mall_ix'";
	
	
	//echo $sql;
	$db->query($sql);
	$db->query("select mall_useinfo from ".TBL_SHOP_SHOPINFO."");
	$db->fetch();
//	echo $content;

	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/shop_templete/".$admin_config[mall_use_templete]."/ms_useinfo.htm","w");
	fwrite($fp,$db->dt[mall_useinfo]);	
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = '../store/useinfo.php';</script>");
}

?>
