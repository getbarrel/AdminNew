<?
include("../../class/database.class");

session_start();

$db = new Database;

if ($act == "update")
{
	foreach ($_POST as $key => $val) {
		if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y"){ 
			$sql = "REPLACE INTO `shop_mall_config` set 
					mall_ix = '".$_POST[mall_ix]."',
					config_name ='".$key."',
					config_value ='".$val."'  ";
					echo "GG : ".$sql."<br>";
			$db->query($sql);

			$sql = "REPLACE INTO `shop_mall_config` set 
					mall_ix = '20bd04dac38084b2bafdd6d78cd596b2',
					config_name ='".$key."',
					config_value ='".$val."'  ";
			$db->query($sql);
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

?>
