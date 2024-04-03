<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_INCOM_COMPLETE;

$fix_type = array(ORDER_STATUS_DELIVERY_READY, ORDER_STATUS_DELIVERY_DELAY);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
//echo $type_param;
$view_type = "sc_order";
$parent_title = "배송관리";
$title_str = "빠른송장입력";
include("../order/delivery_process.php");



?>