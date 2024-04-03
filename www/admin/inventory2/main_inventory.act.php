<?
include("../../class/database.class");
$db = new Database;

$db->query("update shop_product set main_inventory = '".$inventory_code."' where id = '$pid'");
echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');self.close();</script>";
?>