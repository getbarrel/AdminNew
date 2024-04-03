<?
include("../../class/database.class");
session_start();


$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_company_position (ps_ix,company_id, ps_name, ps_img, ps_level, disp,regdate) values('','".$admininfo[company_id]."','$ps_name','".$_FILES[ps_img][name]."','$ps_level','$disp',NOW())";
	$db->query($sql);

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/",0644);
	}

	if ($ps_img_size > 0){
		copy($_FILES[ps_img][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$_FILES[ps_img][name]);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('직급이 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='position.php';</script>");
}


if ($act == "update"){
	if ($ps_img_size > 0){
		$sql = "select ps_img from shop_company_position where ps_ix='$ps_ix' ";
		$db->query($sql);

		if($db->total){
			$db->fetch();
			if ($db->dt[ps_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img]);
			}
		}
		copy($_FILES[ps_img][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$_FILES[ps_img][name]);
	}

	if($basic == "Y"){
		$sql = "update shop_company_position set
						ps_name='$ps_name', ps_img='".$_FILES[ps_img][name]."',
						disp='$disp'
						where ps_ix='$ps_ix' and company_id = '".$admininfo[company_id]."' ";
	}else{
		$sql = "update shop_company_position set
						ps_name='$ps_name', ps_img='".$_FILES[ps_img][name]."',
						ps_level='$ps_level',disp='$disp'
						where ps_ix='$ps_ix'  and company_id = '".$admininfo[company_id]."'  ";
	}
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('직급이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'position.php';</script>");
}

if ($act == "delete"){
	$sql = "select ps_img from shop_company_position where ps_ix='$ps_ix'  and company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);

	if($db->total){
		$db->fetch();
		if ($db->dt[ps_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img])){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/position/".$db->dt[ps_img]);
		}
	}
	$sql = "delete from shop_company_position where ps_ix='$ps_ix'  and company_id = '".$admininfo[company_id]."'  ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('직급이 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='position.php';</script>");
}

?>
