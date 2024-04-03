<?

//include_once("../class/layout.class");
$move_status = 'MA';
$fix_type = array('MA');

$warehouse_list_type = "outside_move";

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
$title_str = "창고이동출고";
include("warehouse_move_list.php");
//include("orders.goods_list.php");



?>