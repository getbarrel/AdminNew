<?
include_once("../class/layout.class");

$pre_type = ORDER_STATUS_RETURN_ING;

$fix_type = ORDER_STATUS_RETURN_ACCEPT;

$excel_type = array(
ORDER_STATUS_RETURN_ACCEPT,//상품회수
ORDER_STATUS_RETURN_DEFER,//보류
ORDER_STATUS_RETURN_IMPOSSIBLE//불가
);//확정

for($i=0;$i < count($excel_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$excel_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$excel_type[$i];
	}
}

$title_str = "반품 미처리리스트";
include("orders.goods_list.php");


//	웰숲 우클릭 방지
include_once("./wel_drag.php");

?>