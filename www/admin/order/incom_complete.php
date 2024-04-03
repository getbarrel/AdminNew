<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_INCOM_COMPLETE;

$fix_type = array(ORDER_STATUS_INCOM_COMPLETE);//"EA";
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$parent_title = "주문관리";
$title_str = "입금확인 리스트";
include("../order/orders.goods_list.php");



?>