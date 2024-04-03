<?
include("../../class/database.class");



$db = new Database;


if ($act == "update"){
	
	$mall_raw1 = $content;
	
	
	$sql = "update ".TBL_SHOP_SHOPINFO." Set ";
	$sql = $sql." mall_raw1  = '$mall_raw1' where mall_ix = '$mall_ix'";
	
	
	//echo $sql;
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'settlement_config.php';</script>");
}

?>
