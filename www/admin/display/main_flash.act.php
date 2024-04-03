<?
include("../../class/database.class");
include('../../include/xmlWriter.php');




$db = new Database;

if ($act == "insert")
{


	//$sql = "insert into ".TBL_OAASYS_BANKINFO." (bank_ix,bank_name,bank_number,bank_owner,disp,regdate) values('','$bank_name','$bank_number','$bank_owner','$disp',NOW())";
	$sql = "insert into ".TBL_SHOP_MANAGE_FLASH." (mf_ix,mf_type,mf_effect,mf_name,disp,regdate) values('$mf_ix','$mf_type','$mf_effect','$mf_name','$disp',NOW())";
	$db->sequences = "SHOP_MANAGE_FLASH_SEQ";
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$mf_ix = $db->last_insert_id;
	}else{
		$db->query("Select mf_ix from ".TBL_SHOP_MANAGE_FLASH." where mf_ix = LAST_INSERT_ID()");
		$db->fetch(0);
		$mf_ix = $db->dt[mf_ix];
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images";

	if(!is_dir($path."/flash_data/")){
		if(is_writable($path)){
			mkdir($path."/flash_data/", 0777, true);
			chmod($path."/flash_data/", 0777);
		}
	}

	$path = $path."/flash_data";
	if(!is_dir($path."/".$mf_type)){
		if(is_writable($path)){
			mkdir($path."/".$mf_type, 0777, true);
			chmod($path."/".$mf_type, 0777);
		}
	}

	if(is_array($_FILES['mf_file']['size'])){
		foreach($_FILES['mf_file']['size'] as $_key=>$_val)	{
			if($_val > 0)	{
				copy($_FILES['mf_file']['tmp_name'][$_key], $path."/".$mf_type."/".$_FILES['mf_file']['name'][$_key]);
				chmod($path."/".$mf_type."/".$_FILES['mf_file']['name'][$_key], 0777);//추가 kbk 13/09/04

				//$sql = "insert into shop_manage_flash_detail set mf_ix = '$mf_ix', mf_file='".$_FILES['mf_file']['name'][$_key]."',mf_link='".$mf_link[$_key]."',mf_title='".$mf_title[$_key]."', tmp_update='1', regdate = NOW() ";
				$sql = "insert into shop_manage_flash_detail(mfd_ix,mf_ix,mf_title,mf_link,mf_file,regdate,tmp_update) values('','$mf_ix','".$mf_title[$_key]."','".$mf_link[$_key]."','".$_FILES['mf_file']['name'][$_key]."', NOW(),'1')";
				$db->sequences = "SHOP_MANAGE_FLASH_DETAIL_SEQ";
				$db->query($sql);
			}
		}
	}

	//header('Content-Type: text/xml');


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('플래쉬 정보가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='main_flash.php?SubID=SM22464243Sub';</script>");
}


if ($act == "update"){

	$sql = "update ".TBL_SHOP_MANAGE_FLASH." set mf_type='$mf_type',mf_effect='$mf_effect',mf_name='$mf_name',disp='$disp' where mf_ix='$mf_ix' ";
	$db->query($sql);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images";

	if(!is_dir($path."/flash_data/")){
		if(is_writable($path)){
			mkdir($path."/flash_data/", 0777, true);
			chmod($path."/flash_data/", 0777);
		}
	}

	$path = $path."/flash_data";
	if(!is_dir($path."/".$mf_type)){
		if(is_writable($path)){
			mkdir($path."/".$mf_type, 0777, true);
			chmod($path."/".$mf_type, 0777);
		}
	}

	$sql = "update shop_manage_flash_detail set tmp_update='0' where mf_ix='".$mf_ix."' ";
	$db->query($sql);

	if(is_array($_FILES['mf_file']['size'])){
		foreach($_FILES['mf_file']['size'] as $_key=>$_val)	{
			if($mfd_ix[$_key] != "" && $nondelete[$mfd_ix[$_key]] == 1){
				$sql = "update shop_manage_flash_detail set tmp_update='1' where mfd_ix='".$mfd_ix[$_key]."' ";
				$db->query($sql);
			}
			if($_val > 0)	{
				copy($_FILES['mf_file']['tmp_name'][$_key], $path."/".$mf_type."/".$_FILES['mf_file']['name'][$_key]);
				chmod($path."/".$mf_type."/".$_FILES['mf_file']['name'][$_key], 0777);//추가 kbk 13/09/04

				if($mfd_ix[$_key] == ""){
					//$sql = "insert into shop_manage_flash_detail set mf_ix = '$mf_ix', mf_file='".$_FILES['mf_file']['name'][$_key]."',mf_link='".$mf_link[$_key]."',mf_title='".$mf_title[$_key]."', tmp_update='1', regdate = NOW() ";
					$sql = "insert into shop_manage_flash_detail(mfd_ix,mf_ix,mf_title,mf_link,mf_file,regdate,tmp_update) values('','$mf_ix','".$mf_title[$_key]."','".$mf_link[$_key]."','".$_FILES['mf_file']['name'][$_key]."', NOW(),'1')";
					$db->sequences = "SHOP_MANAGE_FLASH_DETAIL_SEQ";
					$db->query($sql);
				} else {
					$sql = "update shop_manage_flash_detail set mf_file='".$_FILES['mf_file']['name'][$_key]."',mf_link='".$mf_link[$_key]."',mf_title='".$mf_title[$_key]."', tmp_update='1' where
					mfd_ix='".$mfd_ix[$_key]."' ";
					$db->query($sql);
				}
			}
		}
	}


	$sql = "delete from shop_manage_flash_detail where mf_ix='".$mf_ix."' and tmp_update = '0' ";
	$db->query($sql);

	$ix_cnt=count($mfd_ix);
	for($i=0;$i<$ix_cnt;$i++) {
		if($mfd_ix[$i]!="") {
			$sql = "update shop_manage_flash_detail set mf_link='".$mf_link[$i]."',mf_title='".$mf_title[$i]."' where mfd_ix='".$mfd_ix[$i]."' ";
			$db->query($sql);
		}
	}

	//exit;



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('이미지 정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'main_flash.php?SubID=SM22464243Sub';</script>");
}

if ($act == "delete"){

	$sql = "select mf_type from ".TBL_SHOP_MANAGE_FLASH." where mf_ix='$mf_ix'";
	$db->query($sql);
	$db->fetch();
	$mf_type = $db->dt[mf_type];

	$mfdArr = array();
	$db->query("SELECT mf_file FROM shop_manage_flash_detail  where mf_ix = '".$mf_ix."' ");
	$mfdArr = $db->fetchall();

	if(is_array($mfdArr)){
		foreach($mfdArr as $_key=>$_val){
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/flash_data/$mf_type/".$mfdArr[$_key][mf_file])){
				@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/flash_data/$mf_type/".$mfdArr[$_key][mf_file]);
			}
		}
	}

	$sql = "delete from ".TBL_SHOP_MANAGE_FLASH." where mf_ix='$mf_ix'";
	$db->query($sql);
	$sql = "delete from shop_manage_flash_detail where mf_ix='$mf_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('이미지 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='main_flash.php?SubID=SM22464243Sub';</script>");
}


?>
