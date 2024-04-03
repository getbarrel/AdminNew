<?
include_once("../class/layout.class");

$view_type = "inventory";
$stock_use_yn = 'Y';	//wms 상품체크(검색조건)
$pre_type = "WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM;

$fix_type = array(ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_DELAY);

$delivery_status = array(ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM);

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


$parent_title = "출고관리";
$title_str = "출고요청확정";
include("../order/delivery_process.php");

?>