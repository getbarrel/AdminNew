<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_RETURN_ING;

$fix_type = ORDER_STATUS_RETURN_ACCEPT;

//$type = ;
/*
$fix_type = array(ORDER_STATUS_RETURN_APPLY,ORDER_STATUS_RETURN_ING,ORDER_STATUS_RETURN_DELIVERY,ORDER_STATUS_RETURN_COMPLETE,ORDER_STATUS_RETURN_DEFER,ORDER_STATUS_RETURN_DENY);//"EA";
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
*/
$title_str = "반품 미처리리스트";
$view_type = "offline_order";
include("../order/orders.goods_list.php");



?>