<?
include("../../class/database.class");
session_start();


$db = new Database;

if ($act == "insert")
{


	if($db->dbms_type == "oracle"){
		$sql = "insert into common_branch (cb_ix,rg_ix, branch_name, level_ , disp,regdate) values('','$rg_ix','$branch_name','$level','$disp',NOW())";
	}else{
		$sql = "insert into common_branch (cb_ix,rg_ix, branch_name,  level, disp,regdate) values('','$rg_ix','$branch_name','$level','$disp',NOW())";
	}

	$db->sequences = "COMMON_BRANCH_SEQ";
	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지사정보가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	if($db->dbms_type == "oracle"){
		$sql = "update common_branch set rg_ix = '$rg_ix',
						branch_name='$branch_name' , level_='$level',disp='$disp'
						where cb_ix='$cb_ix' ";
	}else{

		$sql = "update common_branch set rg_ix = '$rg_ix',
						branch_name='$branch_name' , level='$level',disp='$disp'
						where cb_ix='$cb_ix' ";
	}

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지사정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	/*
	$sql = "select ps_img from common_branch where cb_ix='$cb_ix' ";
	$db->query($sql);

	if($db->total){
		$db->fetch();
		if ($db->dt[ps_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img])){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img]);
		}
	}
	*/
	$sql = "delete from common_branch where cb_ix='$cb_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('지사정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

?>
