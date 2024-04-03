<?
include_once("../class/layout.class");


$pre_type = ORDER_UNRECEIVED_CLAIM;
//$type = ;
$fix_type = array(ORDER_STATUS_DELIVERY_COMPLETE);
$parent_title = "미수령신고";
$title_str = "미수령신고 상품";
include("delivery_process.php");



?>