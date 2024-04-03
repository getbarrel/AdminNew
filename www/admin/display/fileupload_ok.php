<?
include("../../class/database.class");

if($_POST['content_type'] == '2'){
    $backUpDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/style/".$_SESSION['admininfo']['charger_id'];
}else if($_POST['content_type'] == '3'){
    $backUpDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/player/".$_SESSION['admininfo']['charger_id'];
}

if($_POST['mode'] == 'del'){
	if (file_exists($backUpDir . "/" .$_POST['imgName'])) {
		unlink($backUpDir . "/" .$_POST['imgName']);
	}

	$result = array('mode' => 'delOk');
}else{
	$imgName	= $_FILES['productImg']['name'];
	$imgTmpName	= $_FILES['productImg']['tmp_name'];

	if(!is_dir($backUpDir)){
		mkdir($backUpDir);
		chmod($backUpDir,0777);
	}

	copy($imgTmpName, $backUpDir."/".$imgName);

	$result = array('dir' => $backUpDir, 'img' => $imgName);
}
echo json_encode($result);
exit;
?>