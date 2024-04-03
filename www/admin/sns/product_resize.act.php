<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;
for($i=0;$i<count($idx);$i++){
	$db->query("update ".TBL_SNS_IMAGE_RESIZEINFO." set width = '$width[$i]',height='$height[$i]' where idx = '$idx[$i]' ");
}
echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('수정이 완료 되었습니다.');parent.location.reload()</script>";
?>