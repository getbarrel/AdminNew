<?
include_once("../class/layout.class");

$view_type = "buyer_accounts";
$pre_type = 'refund';

$fix_type = array(ORDER_STATUS_REFUND_APPLY,ORDER_STATUS_REFUND_COMPLETE);

if(empty($refund_type)){
	for($i=0;$i < count($fix_type);$i++){
		if($type_param == ""){
			$type_param = "refund_type%5B%5D=".$fix_type[$i];
		}else{
			$type_param .= "&refund_type%5B%5D=".$fix_type[$i];
		}
	}
}

//[Start] orders.goods_list.php 를 사용하는 페이지들과 검색부분을 공통적으로 맞추기 위해 추가 kbk 13/08/08
//$date_type="o.date";
//$orderdate = 1;
//[End] orders.goods_list.php 를 사용하는 페이지들과 검색부분을 공통적으로 맞추기 위해 추가 kbk 13/08/08

$title_str = "환불리스트";
include("../order/orders.goods_list.php");


//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>