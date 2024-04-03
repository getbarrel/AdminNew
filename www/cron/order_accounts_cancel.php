<?

include("../class/layout.class");
include("../admin/seller_accounts/accounts.lib.php");

$db = new Database;

exit;
$AC_DATE = "2014-08-01";

$sql=" select * from shop_accounts where ac_date ='".$AC_DATE."' and status='AR' ";
$db->query($sql);
$ac_list = $db->fetchall("object");

foreach($ac_list as $list){
	
	$sql="update ".TBL_SHOP_ORDER_DETAIL." set ac_ix = '0' , accounts_status = null , ac_date = null where ac_ix ='".$list[ac_ix]."' ";
	echo $sql."<br/><br/>";
	$db->query($sql);
	
	$sql="update ".TBL_SHOP_ORDER_DETAIL." set refund_ac_ix = '0' where refund_ac_ix ='".$list[ac_ix]."' ";
	echo $sql."<br/><br/>";
	$db->query($sql);

	$sql="update shop_order_delivery set ac_ix = '0' where ac_ix ='".$list[ac_ix]."' ";
	echo $sql."<br/><br/>";
	$db->query($sql);

	$sql="update shop_order_claim_delivery set ac_ix = '0' where ac_ix ='".$list[ac_ix]."' ";
	echo $sql."<br/><br/>";
	$db->query($sql);

	$sql="delete from  shop_accounts where ac_ix ='".$list[ac_ix]."' ";
	echo $sql."<br/><br/>";
	$db->query($sql);

}

?>