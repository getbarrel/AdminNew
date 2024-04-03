<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_BUY_FINALIZED;
//$type = ;
$fix_type = array(ORDER_STATUS_BUY_FINALIZED);

for($i=0;$i < count($fix_type);$i++){
    if($type_param == ""){
        $type_param = "type%5B%5D=".$fix_type[$i];
    }else{
        $type_param .= "&type%5B%5D=".$fix_type[$i];
    }
}

$parent_title = "배송관리";
$title_str = "거래완료";
include("delivery_process.php");



//	웰숲 우클릭 방지
include_once("./wel_drag.php");
?>