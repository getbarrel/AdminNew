<?
include("../class/layout.class");

$db = new Database;

if($act=="payment_yn_update"){
	
	if($payment_yn=="Y"){
		$update_str=" , payment_date = NOW() ";


		$db->query("select odd.add_delivery_price,od.oid,od.od_ix,od.company_id from shop_order_detail_deliveryinfo odd left join shop_order_detail od on (odd.odd_ix=od.odd_ix) WHERE od.odd_ix ='".$odd_ix."' ");
		$add_delivery_price = $db->dt[add_delivery_price];
		$oid = $db->dt[oid];
		$od_ix = $db->dt[od_ix];
		$company_id = $db->dt[company_id];

		table_order_price_data_creation($oid,$od_ix,$company_id,'A','D',0,$add_delivery_price,$msg,0,0,0);
	}

	$db->query("UPDATE shop_order_detail_deliveryinfo SET payment_yn = '$payment_yn' $update_str  WHERE odd_ix ='".$odd_ix."' ");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상태 변경이 정상적으로  처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
	exit;
}

?>

