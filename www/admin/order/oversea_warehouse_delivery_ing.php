<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING;
//$type = ;
$fix_type = array(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING);//"EA";
$parent_title = "해외배송관리";
$title_str = "해외창고배송중";
include("oversea_delivery_process.php");

?>