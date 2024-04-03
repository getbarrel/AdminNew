<?
include_once("../class/layout.class");
$pre_type = ORDER_STATUS_REFUND_APPLY;
$fix_type = array(ORDER_STATUS_REFUND_APPLY);//"EA";
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
$title_str = "환불리스트";
include("orders.goods_list.php");



?>