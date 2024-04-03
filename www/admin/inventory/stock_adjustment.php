<?

//include_once("../class/layout.class");
$page_type = 'adjustment';
/*
$fix_type = array('MI');
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
*/
$title_str = "재고조정";
$sub_title = "조정";
$sub_price_title = "매입가";
$sub_amount_title = "조정수량";

if($adjustment_type == "delivery"){
	$h_div = "2"; // 1:입고, 2:출고
	//$h_type = "27"; // 출고 재고조정
	$type = "2";
	$type_div = "11"; // 재고조정
}else if($adjustment_type == "stocked"  || $adjustment_type == ""){//
	$h_div = "1"; // 1:입고, 2:출고
//	$h_type = "26"; // 입고 재고조정
	$type = "1";
	$type_div = "1"; // 재고조정
}else if($adjustment_type == "basic"){//
	$h_div = "1"; // 1:입고, 2:출고
//	$h_type = "26"; // 입고 재고조정
	$type = "1";
	$type_div = "1"; // 재고조정
	$type_code = "FC";	//기초조정

	$title_str = "재고조정";
	$sub_title = "조정";
	$sub_price_title = "매입가";
	$sub_amount_title = "기초재고";
}
include("register.php");
//include("orders.goods_list.php");



?>