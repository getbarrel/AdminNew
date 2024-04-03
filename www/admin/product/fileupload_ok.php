<?
include("../../class/database.class");

$backUpDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id'];

if($_POST['mode'] == 'del'){
	if (file_exists($backUpDir . "/" .$_POST['imgName'])) {
		unlink($backUpDir . "/" .$_POST['imgName']);
	}
	
	/*if (file_exists($_SERVER["DOCUMENT_ROOT"] . $_POST['imgTemp'] . "/" .$_POST['imgName'])) {
		unlink($_SERVER["DOCUMENT_ROOT"] . $_POST['imgTemp'] . "/" .$_POST['imgName']);
	}

	$imgAdd = str_replace('basic_','',$_POST['imgName']);
	$pathAdd = str_replace('/productNew/','/addimgNew/',$_POST['imgTemp']);

	if (file_exists($pathAdd . "/list_" .$imgAdd)) {
		unlink($pathAdd . "/list_" .$imgAdd);
	}

	if (file_exists($pathAdd . "/over_" .$imgAdd)) {
		unlink($pathAdd . "/over_" .$imgAdd);
	}

	if (file_exists($pathAdd . "/slist_" .$imgAdd)) {
		unlink($pathAdd . "/slist_" .$imgAdd);
	}

	if (file_exists($pathAdd . "/nail_" .$imgAdd)) {
		unlink($pathAdd . "/nail_" .$imgAdd);
	}

	if (file_exists($pathAdd . "/patt_" .$imgAdd)) {
		unlink($pathAdd . "/patt_" .$imgAdd);
	}*/

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