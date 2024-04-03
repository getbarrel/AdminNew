<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into bbs_group (div_ix,div_name,disp,regdate) values('','$div_name','$disp',NOW())";
	$db->sequences = "BBS_GROUP_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 그룹이 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='board_group.php?mmode=$mmode';</script>");
}


if ($act == "update"){

	$sql = "update bbs_group set div_name='$div_name',disp='$disp' where div_ix='$div_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 그룹이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'board_group.php?mmode=$mmode';</script>");
}

if ($act == "delete"){

	$sql = "delete from bbs_group where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 그룹이 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='board_group.php?mmode=$mmode';</script>");
}

?>
