<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_DELIVERY_READY;
$delivery_status = "WDA";

$fix_type = array(ORDER_STATUS_DELIVERY_READY);//"EA";
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$parent_title = "출고요청";
$title_str = "출고요청";
if($list_type == "" || $list_type == "item" || $list_type == "item_member"){
	include("delivery_processbyitem.php");
}else if($list_type == "order"  ){
	include("delivery_process.php");
}

?>