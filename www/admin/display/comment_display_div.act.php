<?
include("../../class/database.class");

$db = new Database;

if ($act == "insert"){

	$sql = "insert into shop_display_comment_div (div_ix, event_ix, depth, div_name,disp,regdate) values('','$event_ix','$depth','$div_name','$disp',NOW())";
	$db->sequences = "SHOP_CT_MAIN_DIV_SEQ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('카테고리 메인 분류가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='event_comment_category.php?event_ix=$event_ix&mmode=pop';</script>");
}


if ($act == "update"){

	$sql = "update shop_display_comment_div set
				depth='$depth',
				div_name='$div_name',
				disp='$disp' 
			where 
				div_ix='$div_ix' ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('카테고리 메인 분류가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href='event_comment_category.php?event_ix=$event_ix&mmode=pop';</script>");
}

if ($act == "delete"){

	$sql = "delete from shop_display_comment_div where div_ix='$div_ix'";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('카테고리 메인 분류가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='event_comment_category.php?event_ix=$event_ix&mmode=pop';</script>");

}

?>
