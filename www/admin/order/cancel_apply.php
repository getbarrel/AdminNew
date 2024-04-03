<?
include_once("../class/layout.class");

$pre_type = ORDER_STATUS_CANCEL_APPLY;
$fix_type = array(ORDER_STATUS_CANCEL_APPLY);
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
$title_str = "발주취소요청리스트";
include("../order/orders.goods_list.php");
//include("orders.goods_list.php");

//	웰숲 우클릭 방지
include_once("./wel_drag.php");
?>