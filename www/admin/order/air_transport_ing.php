<?
include_once("../class/layout.class");


$pre_type = ORDER_STATUS_AIR_TRANSPORT_ING;
//$type = ;
$fix_type = array(ORDER_STATUS_AIR_TRANSPORT_ING);//"EA";
$parent_title = "해외배송관리";
$title_str = "항공배송중";
include("oversea_delivery_process.php");

?>