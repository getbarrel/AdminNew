<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert")
{

	
	$sql = "insert into delivery_group (group_ix,parent_group_ix , group_name,group_depth, vieworder, disp,regdate) values('','$parent_group_ix','$group_name','$group_depth','$vieworder','$disp',NOW())";
	$db->query($sql);


	echo("<script>alert('택배 그룹이 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
		
	$sql = "update delivery_group set group_name='$group_name',vieworder='$vieworder',disp='$disp' where group_ix='$group_ix'  ";
	
	$db->query($sql);

	

	echo("<script>alert('택배 그룹이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from delivery_group where group_ix='$group_ix'";
	$db->query($sql);


	echo("<script>alert('택배 그룹이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
