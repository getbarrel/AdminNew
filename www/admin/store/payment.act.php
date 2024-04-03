<?
include("../../class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new Database;

//print_r($_POST);
if ($act == "update"){

	foreach ($_POST as $key => $val) {

		if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y" && $key != "pg_code"){

			$sql ="select * from shop_payment_config where mall_ix = '".$_POST[mall_ix]."' and pg_code= '".$_POST[pg_code]."' and config_name ='".$key."' ";
			$db->query($sql);

			if($db->total){
				$sql = "update shop_payment_config set  config_value ='".$val."'
						where mall_ix = '".$_POST[mall_ix]."' and pg_code='".$_POST[pg_code]."' and config_name ='".$key."' ";
				$db->query($sql);
			}else{
				$sql ="insert into shop_payment_config(mall_ix,pg_code,config_name,config_value) values('".$_POST[mall_ix]."','".$_POST[pg_code]."','".$key."','".$val."') ";
				$db->query($sql);
			}


			/*
			$sql = "REPLACE INTO shop_payment_config set
						mall_ix = '".$_POST[mall_ix]."',
						pg_code='nicepay' ,
						config_name ='".$key."',
						config_value ='".$val."'  ";
			$db->query($sql);
			*/

		}

	}


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();location.href='about:blank';</script>");
}

?>
