<?
include_once("../class/layout.class");

//배송취소요청 WMS사용 2014-08-06 이학봉
$stock_use_yn = 'Y';
$view_type = 'inventory';

$title_str = "제휴사엑셀주문등록";
include("../order/orders_input_excel.php");

?>