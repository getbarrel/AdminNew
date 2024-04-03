<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_DEFERRED_PAYMENT;

$fix_type = array(ORDER_STATUS_DEFERRED_PAYMENT);//"EA";	후불(외상) 리스트
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$view_type_order = 'offline_order_order';
$parent_title = "주문관리";
$title_str = "후불(외상) 리스트";
include("orders.goods_list.php");

?>