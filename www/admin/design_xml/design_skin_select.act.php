<?
include("../../class/database.class");
include('./design.common.php');
include_once("../lib/imageResize.lib.php");


$db = new Database;

//print_r($_POST);
if ($act == "skin_update"){


	$sql = "update ".TBL_SHOP_SHOPINFO." set 
			mall_use_templete='$mall_use_templete' 
			where mall_ix='".$admininfo[mall_ix]."'	";
	//echo $sql;
	//exit;
	$db->query($sql);
	$admin_config[mall_use_templete] = $mall_use_templete;

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('디자인 스킨이 정상적으로 수정 되었습니다..');parent.document.location.reload();</script>");
	exit;
}


if ($act == "skin_mobile_update"){


	$sql = "update ".TBL_SHOP_SHOPINFO." set 
			mall_use_mobile_templete='$mall_use_mobile_templete' 
			where mall_ix='".$admininfo[mall_ix]."'	";
	//echo $sql;
	//exit;
	$db->query($sql);
	$admin_config[mall_use_templete] = $mall_use_templete;

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('모바일 디자인 스킨이 정상적으로 수정 되었습니다..');parent.document.location.reload();</script>");
	exit;
}
?>
