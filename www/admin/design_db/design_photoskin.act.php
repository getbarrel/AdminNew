<?
include("../../class/database.class");
include('./design.common.php');
include_once("../lib/imageResize.lib.php");


$db = new Database;

//print_r($_POST);
if ($act == "photoskin_update"){
	for($i=1;$i < count($_POST[skininfo])+1;$i++){
		foreach ($_POST[skininfo][$i] as $key => $val) {
			echo $key."::::".$val."<br>";
			$sql = "REPLACE INTO shop_design_skin set photoskin_type='".($i)."' , photoskin_name='".$key."',photoskin='".$val."'  ";
			$db->query($sql);

				//$work_confs[$key] = $val;
		}
	}

	$sql = "update ".TBL_SHOP_SHOPINFO." set 
			photoskin_type='$photoskin_type' 
			where mall_ix='".$admininfo[mall_ix]."'	";
	//echo $sql;
	//exit;
	$db->query($sql);

	updateLayoutXML($admininfo[mall_ix]);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('매직스킨 설정이 정상적으로 저장되었습니다..');parent.document.location.reload();</script>");
	exit;
}

if($act == "resize"){
	$file_name = "clouds16.jpg";
	$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/photoskin/".$file_name;
	Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/photoskin/s_".$file_name, MIRROR_NONE);
	resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/photoskin/s_".$file_name,200,134,'W');
	@chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/photoskin/s_".$file_name, 0777);
}
?>
