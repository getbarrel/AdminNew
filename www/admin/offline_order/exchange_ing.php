<?
include_once("../class/layout.class");
$pre_type = ORDER_STATUS_EXCHANGE_ING;

$fix_type = ORDER_STATUS_EXCHANGE_ACCEPT;
/*
$fix_type = array(ORDER_STATUS_EXCHANGE_APPLY,
ORDER_STATUS_EXCHANGE_DENY,
ORDER_STATUS_EXCHANGE_ING,
ORDER_STATUS_EXCHANGE_DELIVERY,
ORDER_STATUS_EXCHANGE_ACCEPT,
ORDER_STATUS_EXCHANGE_DEFER,
ORDER_STATUS_EXCHANGE_IMPOSSIBLE,
ORDER_STATUS_EXCHANGE_COMPLETE);


for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
*/

$title_str = "교환 미처리스트";
$view_type = "offline_order";
include("../order/orders.goods_list.php");

?>