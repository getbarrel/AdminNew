<?
include("../../class/database.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
/*
if($_POST['act'] == 'select') {
	$select = $_POST[select];
	$db->query("update shop_idea set idea_select = '$select' where ix = '".$ix."'");
	$db->fetch();
	if($_POST['select'] == 'Y') {
		echo "<script>alert('채택이 정상적으로 처리되었습니다.');self.close();</script>";
	}else {
		echo "<script>self.close();</script>";
	}
}*/


if($_GET[mode] =='del') {
	$db->query("delete from shop_bug where ix = '".$_GET[ix]."'");
	$db->fetch();
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');opener.document.location.reload();self.close();</script>";
}

?>