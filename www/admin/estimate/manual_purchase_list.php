<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_DELIVERY_READY;
//$type = ;

//$fix_type = array(ORDER_STATUS_INCOME_READY);//"EA";
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$page_type = "manual_purchase_list";
$parent_title = "수동수주서 리스트";
$title_str = "수동수주서 리스트";
if($list_type == "item"){
	include("../inventory/delivery_processbyitem.php");
}else{
	include("../inventory/delivery_process.php");
}

?>