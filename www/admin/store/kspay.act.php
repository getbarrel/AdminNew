<?
include("../../class/database.class");
include("$DOCUMENT_ROOT/class/sms.class");
include("../include/mallstory_connection.lib.php");
$db = new MySQL;

if ($act == "update"){

	foreach ($_POST as $key => $val) {
		if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y"){
			$sql = "REPLACE INTO shop_payment_config set
						mall_ix = '".$_POST[mall_ix]."',
						pg_code='kspay' ,
						config_name ='".$key."',
						config_value ='".$val."'  ";
			$db->query($sql);
		}
	}

	Get_Pg_Module(trim($kspay_id));
	echo("<script language='javascript' src='\../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();location.href='about:blank';</script>");
}

?>
