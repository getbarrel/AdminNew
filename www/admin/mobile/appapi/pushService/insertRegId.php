<?php 
/**
 * 앱에서 GCM 등록키 DB입력 호출 페이지
 * 
 * @author bgh 
 * @date 2013.07.19
 */
$receive_key = $_POST["regId"];
if(empty($receive_key)){
	$receive_key = $_GET["regId"];
}
include ("./androidpush.php");

$con = new androidPush();

if(!empty($receive_key)){
	$result = $con->setRegistId($receive_key);
}

echo $result;