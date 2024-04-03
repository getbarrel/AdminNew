<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_INCOM_COMPLETE;

$fix_type = array(ORDER_STATUS_DELIVERY_READY, ORDER_STATUS_DELIVERY_DELAY);

$invoice_no_bool = "N";



for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
//echo $type_param;
$parent_title = "배송관리";
$title_str = "빠른송장입력";
include("delivery_process.php");



//	웰숲 우클릭 방지
include_once("./wel_drag.php");
?>