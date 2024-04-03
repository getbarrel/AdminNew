<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	$sql = "insert into ".TBL_BBS_MANAGE_STATUS." (status_ix,bm_ix , status_name,view_order, disp,regdate) values('','$bm_ix','$status_name','$view_order','$disp',NOW())";
	$db->sequences = "BBS_MANAGE_STATUS_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 처리상태가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update ".TBL_BBS_MANAGE_STATUS." set status_name='$status_name',view_order='$view_order',disp='$disp' where status_ix='$status_ix' and bm_ix = '$bm_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 처리상태가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from ".TBL_BBS_MANAGE_STATUS." where status_ix='$status_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 처리상태가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
