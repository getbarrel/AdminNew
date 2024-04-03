<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_category_main_position(cmp_ix,div_ix,cmp_name,disp,vieworder,regdate) values ('','$div_ix','$cmp_name','$disp','$vieworder',NOW()) ";
	//echo $sql;
	//exit;
	$db->sequences = "SHOP_BANNER_POSITION_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전시분류 위치가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='category_main_position.php?mmode=$mmode';</script>");
}


if ($act == "update"){

	$sql = "update shop_category_main_position set div_ix='$div_ix',cmp_name='$cmp_name',disp='$disp',vieworder='$vieworder' where cmp_ix='$cmp_ix' ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전시분류 위치가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'category_main_position.php?mmode=$mmode';</script>");
}

if ($act == "delete"){

	$sql = "delete from shop_category_main_position where cmp_ix='$cmp_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전시분류 위치가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='category_main_position.php?mmode=$mmode';</script>");
}

?>
