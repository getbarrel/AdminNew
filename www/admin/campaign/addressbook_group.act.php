<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_addressbook_group (group_ix,company_id, parent_group_ix , group_name,group_depth, vieworder, disp,regdate) values('','".$admininfo[company_id]."','$parent_group_ix','$group_name','$group_depth','$vieworder','$disp',NOW())";
	$db->sequences = "SHOP_ADDRESSBOOK_GROUP_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메일링/SMS 그룹이 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update shop_addressbook_group set group_name='$group_name',vieworder='$vieworder',disp='$disp' where group_ix='$group_ix'  ";
	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메일링/SMS 그룹이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from shop_addressbook_group where group_ix='$group_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메일링/SMS 그룹이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
