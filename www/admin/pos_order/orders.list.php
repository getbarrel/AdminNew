<?
include_once("../class/layout.class");
$view_type = "pos_order";
$fix_type = array(ORDER_STATUS_DELIVERY_COMPLETE,ORDER_STATUS_BUY_FINALIZED);

include("../order/orders.list.php");
?>