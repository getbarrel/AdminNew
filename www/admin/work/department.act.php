<?
include("../../class/database.class");
ini_set('display_errors', 1);
session_start();


$db = new Database;

if ($act == "insert")
{


	$sql = "insert into shop_company_department (dp_ix,company_id, dp_name, dp_img, dp_level, disp,regdate) values('','".$admininfo["company_id"]."','$dp_name','".$_FILES['dp_img']['name']."','$dp_level','$disp',NOW())";
	$db->query($sql);

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/department/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/department/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/department/",0644);
	}

	if ($dp_img_size > 0){
		move_uploaded_file($_FILES['dp_img']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."".$admin_config['mall_data_root']."/images/department/".$_FILES['dp_img']['name']);
		//copy($dp_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/department/".$dp_img);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('부서가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='department.php';</script>");
}


if ($act == "update"){

	if ($dp_img_size > 0){
		$sql = "select dp_img from shop_company_department where dp_ix='$dp_ix' and company_id = '".$admininfo["company_id"]."' ";
		$db->query($sql);

		if($db->total){
			$db->fetch();
			if ($db->dt['dp_img'] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config['mall_data_root']."/images/department/".$db->dt['dp_img'])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config['mall_data_root']."/images/department/".$db->dt['dp_img']);
			}
		}
		move_uploaded_file($_FILES['dp_img']['tmp_name'], $_SERVER["DOCUMENT_ROOT"]."".$admin_config['mall_data_root']."/images/department/".$_FILES['dp_img']['name']);
	}

	if($basic == "Y"){
		$sql = "update shop_company_department set
						dp_name='$dp_name', dp_img='".$_FILES['dp_img']['name']."',
						disp='$disp'
						where dp_ix='$dp_ix' and company_id = '".$admininfo["company_id"]."' ";
	}else{
		$sql = "update shop_company_department set
						dp_name='$dp_name', dp_img='".$_FILES['dp_img']['name']."',
						dp_level='$dp_level',disp='$disp'
						where dp_ix='$dp_ix' and company_id = '".$admininfo["company_id"]."' ";
	}
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('부서가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'department.php';</script>");
}

if ($act == "delete"){
	$sql = "select dp_img from shop_company_department where dp_ix='$dp_ix' and company_id = '".$admininfo["company_id"]."'  ";
	$db->query($sql);

	if($db->total){
		$db->fetch();
		if ($db->dt[dp_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/department/".$db->dt[dp_img])){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/department/".$db->dt[dp_img]);
		}
	}
	$sql = "delete from shop_company_department where dp_ix='$dp_ix' and company_id = '".$admininfo["company_id"]."'  ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('부서가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='department.php';</script>");
}

?>
