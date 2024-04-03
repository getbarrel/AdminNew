<?
include_once("../class/layout.class");
include '../include/phpexcel/Classes/PHPExcel.php';

$pre_type = ORDER_STATUS_WAREHOUSING_STANDYBY;// 사입중
//$type = ;
$fix_type = array(ORDER_STATUS_WAREHOUSING_STANDYBY);//"EA"; 사입중 과 배송준비중(사입완료) 
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
//echo $type_param;
$parent_title = "배송관리";
$title_str = "사입중 리스트";

include("delivery_processbysc.php");



?>
