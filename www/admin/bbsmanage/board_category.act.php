<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	$sql = "insert into ".TBL_BBS_MANAGE_DIV." (div_ix,bm_ix, parent_div_ix , div_name,div_info_text,div_depth,view_order, disp,regdate) values('','$bm_ix','$parent_div_ix','$div_name','$div_info_text','$div_depth','$view_order','$disp',NOW())";
	$db->sequences = "BBS_MANAGE_DIV_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 분류가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='board_category.php?mmode=$mmode&bm_ix=$bm_ix';</script>");
}


if ($act == "update"){

	$sql = "update ".TBL_BBS_MANAGE_DIV." set div_name='$div_name',div_info_text='$div_info_text',view_order='$view_order',disp='$disp' where div_ix='$div_ix' and bm_ix = '$bm_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 분류가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'board_category.php?mmode=$mmode&bm_ix=$bm_ix';</script>");
}

if ($act == "delete"){

	$sql = "delete from ".TBL_BBS_MANAGE_DIV." where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('게시판 분류가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='board_category.php?mmode=$mmode&bm_ix=$bm_ix';</script>");
}

?>
