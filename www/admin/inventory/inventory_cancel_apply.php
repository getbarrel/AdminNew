<?
include_once("../class/layout.class");

//배송취소요청 WMS사용 2014-08-06 이학봉
$stock_use_yn = 'Y';
$view_type = 'inventory';

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

?>