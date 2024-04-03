<?
include_once("../class/layout.class");

//배송취소요청 WMS사용 2014-08-06 이학봉
$stock_use_yn = 'Y';
$view_type = 'inventory';

$title_str = "주문엑셀양식관리";
include("../order/excel_template.php");

?>