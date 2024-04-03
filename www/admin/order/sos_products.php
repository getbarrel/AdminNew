<?php
include_once("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$sms_design = new SMS;
$pre_type = 'sos_product';

$fix_type = array(ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_BUY_FINALIZED);
for($i=0;$i < count($fix_type);$i++){
    if($type_param == ""){
        $type_param = "type%5B%5D=".$fix_type[$i];
    }else{
        $type_param .= "&type%5B%5D=".$fix_type[$i];
    }
}
$product_type = '55';
$title_str = "SOS티켓 입금확인리스트";
include("../order/orders.goods_list.php");