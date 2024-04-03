<?
include("sellertool.lib.php");
include("../openapi/openapi.lib.php");

$act = $_POST['act'];


if($act == "getPrdDivCode"){
    $site_code = $_POST['site_code'];
    $result = getSellerToolProductDiv($site_code,$selected);
    echo $result;

}else if($act == "getInAddress"){
    $site_code = $_POST['site_code'];
	$result = getInAddress($site_code);
	echo $result;
}else if($act == "getOutAddress"){
    $site_code = $_POST['site_code'];
	$result = getOutAddress($site_code);
    echo $result;
}
?>