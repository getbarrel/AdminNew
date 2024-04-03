<?

//include_once("../class/layout.class");
$page_type = 'stocked';
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
$title_str = "견적완료";
$depth = '7';
include("estimate.list.php");
//include("orders.goods_list.php");



?>