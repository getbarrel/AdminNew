<?
include("../../class/database.class");
$db = new MySQL;

if ($act == "update"){

    foreach ($_POST as $key => $val) {
        if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y"){
            $sql = "REPLACE INTO shop_payment_config set
						mall_ix = '".$_POST[mall_ix]."',
						pg_code='lguplus' ,
						config_name ='".$key."',
						config_value ='".$val."'  ";
            $db->query($sql);
        }
    }


    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
    echo("<script>parent.document.location.reload();location.href='about:blank';</script>");
}

?>
