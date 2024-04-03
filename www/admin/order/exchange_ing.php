<?
include_once("../class/layout.class");

$pre_type = ORDER_STATUS_EXCHANGE_ING;

$fix_type = ORDER_STATUS_EXCHANGE_ACCEPT;

$excel_type = array(
ORDER_STATUS_EXCHANGE_ACCEPT,
ORDER_STATUS_EXCHANGE_DEFER,
ORDER_STATUS_EXCHANGE_IMPOSSIBLE
);


for($i=0;$i < count($excel_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$excel_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$excel_type[$i];
	}
}


$title_str = "교환 미처리리스트";
include("orders.goods_list.php");

?>