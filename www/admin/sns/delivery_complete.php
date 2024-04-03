<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_DELIVERY_COMPLETE;
//$type = ;
$fix_type = array(ORDER_STATUS_DELIVERY_COMPLETE);

$view_type = "sc_order";

$parent_title = "배송관리";
$title_str = "배송완료 상품";
include("../order/delivery_process.php");



?>