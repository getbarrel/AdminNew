<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-06-22
 * Time: 오후 5:24
 */

include("../../class/database.class");

$db = new Database;

if ($act == "update"){

    $sql = "delete from shop_payment_config where pg_code = 'inipay_standard' and mall_ix = '".$_POST[mall_ix]."' ";
    $db->query($sql);

    foreach ($_POST as $key => $val) {
        if($key != "act" && $key != "x" && $key != "y"){ //&& $key != "mall_ix"
            $sql = "REPLACE INTO shop_payment_config set 
						mall_ix = '".$_POST[mall_ix]."',
						pg_code='inipay_standard' , 
						config_name ='".$key."',
						config_value ='".$val."'  ";
            $db->query($sql);
            echo $sql;
        }
    }


    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
    echo("<script>parent.document.location.reload();location.href='about:blank';</script>");
}