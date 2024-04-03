<?
include_once("../class/layout.class");

//배송중리스트 WMS사용 2014-08-06 이학봉
$stock_use_yn = 'Y';
$view_type = 'inventory';

$pre_type = ORDER_STATUS_DELIVERY_ING;
$fix_type = array(ORDER_STATUS_DELIVERY_ING);
$parent_title = "배송관리";
$title_str = "배송중상품";
include("../order/delivery_process.php");
?>