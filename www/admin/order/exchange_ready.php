<?
include_once("../class/layout.class");

$pre_type = ORDER_STATUS_EXCHANGE_READY;

$fix_type = array(ORDER_STATUS_EXCHANGE_READY);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$title_str = "교환상품발송예정";
include("orders.goods_list.php");


//	웰숲 우클릭 방지
include_once("./wel_drag.php");
?>