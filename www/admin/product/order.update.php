<?
include("../../class/database.class");

$db = new Database;
$mdb = new Database;
$db->query("SELECT id, vieworder FROM ".TBL_SHOP_PRODUCT." order by regdate desc");

for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	
	$sql = "update ".TBL_SHOP_PRODUCT." set vieworder = '".($i+1)."' where id = '".$db->dt[id]."' ";
	//$mdb->query($sql);
	echo $sql;
	
}
?>