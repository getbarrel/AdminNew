<?
include("../../class/database.class");

session_start();

$db = new Database;


if ($act == "insert"){
	$sql = "insert into ".TBL_SHOP_HTML_LIBRARY."(hl_ix,hl_name,hl_desc,html_code,regdate) values ('','$hl_name','$hl_desc','$html_code',NOW())";

	$db->query($sql);
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');</script>");
	echo("<script>opener.document.location.reload();self.close();</script>");
}


if ($act == "update"){
	$sql = "update ".TBL_SHOP_HTML_LIBRARY." set hl_name='$hl_name',hl_desc='$hl_desc',html_code='$html_code',regdate='$regdate' where hl_ix='$hl_ix' ";
			
	$db->query($sql);
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>opener.document.location.reload();self.close();</script>");
}
?>
