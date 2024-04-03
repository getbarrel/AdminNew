<?

include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
//print_r($_POST);
//print_r($sno);
//print_r($sort);
//sort($sort);
//print_r($sort);
$db = new Database;


if ($act == "delete"){
	$db->query("DELETE FROM ".TBL_SNS_ORDER." WHERE oid='$oid'");
	$db->query("DELETE FROM ".TBL_SNS_ORDER_DETAIL." WHERE oid='$oid'");
	$db->query("DELETE FROM ".TBL_SNS_ORDER_DELIVERY_STATUS." WHERE oid='$oid'");
	$db->query("DELETE FROM shop_order_memo WHERE oid='$oid' ");
	echo("<script>top.location.reload();</script>");
	exit;
}


for($i=0;$i < count($sno);$i++){
	$db->query("UPDATE ".TBL_SNS_PRODUCT." SET vieworder='".$sort[$i]."' WHERE id ='".$sno[$i]."' ");
	//echo "UPDATE ".TBL_MALLSTORY_PRODUCT." SET vieworder='".$sort[$i]."' WHERE id ='".$sno[$i]."' <br />";
}

echo("<script>parent.document.location.reload();</script>");

exit;

?>