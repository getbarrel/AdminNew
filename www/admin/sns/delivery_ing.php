<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_DELIVERY_ING;
//$type = ;
$fix_type = array(ORDER_STATUS_DELIVERY_ING);

$view_type = "sc_order";
$parent_title = "배송관리";
$title_str = "배송중상품";
include("../order/delivery_process.php");



?>