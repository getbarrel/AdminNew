<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE;

$fix_type = array(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE);
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$parent_title = "주문관리";
$title_str = "입금전 취소리스트";
include("orders.goods_list.php");


//	웰숲 우클릭 방지
include_once("./wel_drag.php");
?>