<?php 

/**
 * 푸시 예약 크론
 * 자세한 내용은 /admin/mobile/appapi/pushService/request.php 참고.
 */

include("$DOCUMENT_ROOT/class/mysql.class");

$db = new MySQL();
$db2 = new MySQL();

include("../admin/mobile/appapi/pushService/androidpush.php");
include("../admin/mobile/appapi/pushService/iospush.php");

$app_div = "webapp";

include("../admin/mobile/appapi/pushService/push.ini.php");

$con = new androidPush($ios_pem,$android_apikey);
$icon = new iosPush($ios_pem,$android_apikey);


//보내지 않고 타입이 푸시인것만.
$sql = "SELECT * FROM mobile_push_reserve WHERE b_send = '0' AND reserve_type = 'P' AND reserve_push_os='i' and reserve_time <= '".date("Y-m-d H:i:s")."' order by sequence asc limit 0, 1";
$db->query($sql);
$db->fetch();

if($db->total > 0){
	$sql = "UPDATE mobile_push_reserve SET b_send = '9' WHERE sequence='".$db->dt[sequence]."'";
	$db2->query($sql);
}

$data = unserialize(urldecode($db->dt[reserve_data]));


if( $db->dt[reserve_push_os] === 'i') {
	$result = $icon->requestPush($data);
}

//발송이 성공했으면
if ($result === true) {
	$sql = "UPDATE mobile_push_reserve SET
				b_send = '1'
			WHERE sequence = '".$db->dt[sequence]."'";
	$db2->query($sql);

	$str_log = "========== [".date("Y-m-d H:i:s")."][SUCCESS][OS_TYPE : ".$db->dt[reserve_push_os]."][SEQUENCE : ".$db->dt[sequence]."] ===========". chr(13);
	//pushLogInsert($str_log);
}
else {
	$str_log = "========== [".date("Y-m-d H:i:s")."][FAIL][OS_TYPE : ".$db->dt[reserve_push_os]."][SEQUENCE : ".$db->dt[sequence]."] ===========". chr(13);
	//pushLogInsert($str_log);
}



function pushLogInsert($str_log)
{

	include_once($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

	//로그 패스
	$log_path = $_SERVER["DOCUMENT_ROOT"]."/data/daiso_data/_logs/push/";
	$log_file_name = "push_reserve_".date("Ymd").".log";

	$fp = fopen($log_path.$log_file_name, "a+");	
	fwrite($fp, $str_log);		
	fclose($fp);
}