<?php
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
if($act == "insert"){

    $db->query("insert into shop_product_options_sort_by_value (value,view_order,regdate,editdate) values ('$option_name','$option_sort',NOW(),NOW())");

    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('옵션/옵션순서가 등록 되었습니다..');parent.location.reload();</script>";
}

if($act == "update"){
    $db->query("update shop_product_options_sort_by_value set value = '$option_name', view_order='$option_sort', editdate = NOW() where idx = '$idx' ");

    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('옵션/옵션순서가 수정 되었습니다..');parent.location.reload();</script>";
}

if($act == "delete"){
    $db->query("delete from shop_product_options_sort_by_value where idx = '$idx' ");


    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('옵션/옵션순서가 삭제 되었습니다..');parent.location.reload();</script>";
}