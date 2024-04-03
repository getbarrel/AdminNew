<?
include_once("../class/layout.class");

if($fix_type_type =='excel'){
	$pre_type = 'AIR_TRANSPORT_EXCEL';
}else{
	$pre_type = ORDER_STATUS_AIR_TRANSPORT_READY;
}

$fix_type = array(ORDER_STATUS_AIR_TRANSPORT_READY);//"EA";

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$parent_title = "해외배송관리";
$title_str = "항공배송준비중";
include("oversea_delivery_process.php");

?>