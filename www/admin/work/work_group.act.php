<?
include("../class/layout.class");



$db = new Database;

if ($act == "insert"){
	$sql = "insert into work_group 
			(group_ix,company_id, parent_group_ix , group_name,group_depth, project_sdate, project_edate, is_project, vieworder, disp,regdate) 
			values
			('','".$admininfo["company_id"]."','$parent_group_ix','$group_name','$group_depth','$sdate','$edate','$is_project','$vieworder','$disp',NOW())";
	$db->query($sql);


	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('업무 그룹이 정상적으로 등록되었습니다.');</script>" ;
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo "<script>parent.document.location.reload();</script>";
}


if ($act == "update"){
	if($is_project == ""){
		$is_project = "0";
	}
	$sql = "update work_group set 
			group_name='$group_name',is_project='$is_project',vieworder='$vieworder',parent_group_ix='$parent_group_ix' ,project_sdate='$sdate'  ,project_edate='$edate'  ,disp='$disp' 
			where company_id = '".$admininfo["company_id"]."' and group_ix='$group_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('업무 그룹이 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "change_disp"){

	$sql = "update work_group set  disp='$disp' where company_id = '".$admininfo["company_id"]."' and group_ix='$group_ix'  ";
	
	$db->query($sql);

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('업무 상태가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "delete"){
	
	$sql = "delete from work_group where company_id = '".$admininfo["company_id"]."' and group_ix='$group_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('업무 그룹이 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
