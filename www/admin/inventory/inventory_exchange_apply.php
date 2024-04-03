<?
include_once("../class/layout.class");

//WMS 교환리스트 2014-08-07 이학봉
$stock_use_yn = 'Y';
$view_type = 'inventory';

$pre_type = ORDER_STATUS_EXCHANGE_APPLY;

$fix_type = ORDER_STATUS_EXCHANGE_APPLY;

$excel_type = array(ORDER_STATUS_EXCHANGE_APPLY, //요청
ORDER_STATUS_EXCHANGE_DENY,//거부
ORDER_STATUS_EXCHANGE_ING,//승인
ORDER_STATUS_EXCHANGE_DELIVERY,//배송중
ORDER_STATUS_EXCHANGE_ACCEPT,//교환상품회수
ORDER_STATUS_EXCHANGE_DEFER,//교환보류
ORDER_STATUS_EXCHANGE_IMPOSSIBLE,//교환불가
ORDER_STATUS_EXCHANGE_COMPLETE);//교환확정


for($i=0;$i < count($excel_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$excel_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$excel_type[$i];
	}
}


$title_str = "교환리스트";
include("../order/orders.goods_list.php");

?>