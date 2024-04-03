<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING;

$fix_type = array(ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_DELAY);

$delivery_status = array(ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

for($i=0;$i < count($delivery_status);$i++){
	if($delivery_type_param == ""){
		$delivery_type_param = "delivery_status%5B%5D=".$delivery_status[$i];
	}else{
		$delivery_type_param .= "&delivery_status%5B%5D=".$delivery_status[$i];
	}
}


$parent_title = "배송관리";
$title_str = "(WMS)포장대기";
include("delivery_process.php");

?>