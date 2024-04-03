<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

$db = new Database;




if ($act == "memo_insert"){	
	$sql = "insert into service_order_memo(om_ix,ucode,oid,memo,counselor,regdate) values('$om_ix','$ucode','$oid','$memo','".$admininfo[charger]."',NOW())";
	$db->query("insert into service_order_memo(om_ix,ucode,oid,memo,counselor,regdate) values('$om_ix','$ucode','$oid','$memo','".$admininfo[charger]."',NOW()) ");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메모가 정상적으로 입력되었습니다.');parent.document.location.reload();location.href='about:blank';</script>");
}

if ($act == "memo_delete"){	
	$db->query("DELETE FROM service_order_memo WHERE oid='$oid' and om_ix ='$om_ix'");
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메모가 정상적으로 삭제되었습니다.');parent.document.location.reload();location.href='about:blank';</script>");
}

?>
