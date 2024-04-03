<?
include("../../class/database.class");
session_start();


$db = new Database;

if ($act == "insert"){


	$sql = "insert into admin_auth_templet (auth_templet_ix,auth_templet_name,  auth_templet_level, disp,regdate) values('','$auth_templet_name','$auth_templet_level','$disp',NOW())";
	$db->sequences = "ADMIN_AUTH_TEMPLET_SEQ";
	$db->query($sql);

	if($mode == "copy"){
		if($db->dbms_type == "oracle"){
			$this_auth_templet_ix = $db->last_insert_id;
			//echo $INSERT_PRODUCT_ID;
			//exit;
		}else{
			$db->query("SELECT auth_templet_ix FROM admin_auth_templet WHERE auth_templet_ix=LAST_INSERT_ID()");
			$db->fetch();
			$this_auth_templet_ix = $db->dt[auth_templet_ix];
		}

			$sql = "insert into admin_auth_templet_detail
					select menu_code,'".$this_auth_templet_ix."' as auth_templet_ix,auth_read, auth_write_update, auth_delete, auth_excel, NOW()
					from admin_auth_templet_detail where  auth_templet_ix = '".$auth_templet_ix."' ";
			//echo $sql;
			$db->query($sql);

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('권한 템플릿 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='auth_templet.php';</script>");
}


if ($act == "update"){

	if($basic == "Y"){
		$sql = "update admin_auth_templet set
						auth_templet_name='$auth_templet_name',
						disp='$disp'
						where auth_templet_ix='$auth_templet_ix' ";
	}else{
		$sql = "update admin_auth_templet set
						auth_templet_name='$auth_templet_name',
						auth_templet_level='$auth_templet_level',disp='$disp'
						where auth_templet_ix='$auth_templet_ix' ";
	}
	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('권한 템플릿 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'auth_templet.php';</script>");
}

if ($act == "delete"){

	$sql = "delete from admin_auth_templet where auth_templet_ix='$auth_templet_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('권한 템플릿 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='auth_templet.php';</script>");
}

?>
