<?
include("../class/layout.class");

$db = new Database;
$db4 = new Database;

$db->query("select * from  ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_PRODUCT." p where od.pid = p.id  ");

for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	
	echo ("update ".TBL_SHOP_ORDER_DETAIL." set reserve = '".$db->dt[reserve]."' where oid ='".$db->dt[oid]."' and pid ='".$db->dt[pid]."' ");
	//$db4->query("update ".TBL_SHOP_ORDER_DETAIL." set reserve = '".$db->dt[reserve]."' where oid ='".$db->dt[oid]."' and pid ='".$db->dt[pid]."' ");
}

?>