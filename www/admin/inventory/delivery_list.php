<?

//include_once("../class/layout.class");
$page_type = 'delivery';
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
$title_str = "출고리스트";
$sub_title = "출고";
$sub_price_title = "매출가";
$type = "2";
$h_div = "2"; // 1:입고, 2:출고
include("history_list.php");
//include("orders.goods_list.php");



?>