<?
include_once("../class/layout.class");


$pre_type = 'MethodBank';


$fix_type = array(ORDER_STATUS_INCOM_READY,ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,ORDER_STATUS_CANCEL_COMPLETE);
$method =array(ORDER_METHOD_BANK);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$type_param .= "&method%5B%5D=".ORDER_METHOD_BANK;

$parent_title = "주문관리";
$title_str = "무통장입금리스트";
include("orders.goods_list.php");



?>