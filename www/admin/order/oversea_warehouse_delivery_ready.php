<?
include_once("../class/layout.class");


if($fix_type_type=='excel'){
	$pre_type = 'OVERSEA_WAREHOUSE_DELIVERY_READY_EXCEL';
}else{
	$pre_type = ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY;
}
//$type = ;

$fix_type = array(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
//echo $type_param;
$parent_title = "해외배송관리";
$title_str = "해외프로세싱중";
include("oversea_delivery_process.php");

?>