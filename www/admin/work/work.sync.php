<?
include("../class/layout.work.class");
include("./google/zend_lib_get.php");
$db = new Database;
//echo "SELECT google_mail, google_pass, google_sync_yn FROM work_userinfo WHERE charger_ix='".$admininfo[charger_ix]."'";
$db->query("SELECT google_mail, google_pass, google_sync_yn, google_sync_time, UNIX_TIMESTAMP(google_sync_updatetime) as google_sync_updatetime FROM work_userinfo WHERE charger_ix='".$admininfo[charger_ix]."'");
$db->fetch();
//echo $db->dt[google_sync_yn];
//	exit;

if($db->total && $db->dt[google_sync_yn] == '1'){
	
	$google_mail = $db->dt[google_mail];
	$google_pass = $db->dt[google_pass];
	$google_sync_time = $db->dt[google_sync_time];

	//echo $db->dt[google_sync_updatetime]."==".mktime(date("H"), date("i")-$google_sync_time, date("s"), date("m")  , date("d"), date("Y"));
	if($db->dt[google_sync_updatetime] < mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"))){
		//echo "update";
		GoogleSync($google_mail, $google_pass, $admininfo);
		$db->query("update work_userinfo set google_sync_updatetime = NOW() WHERE charger_ix='".$admininfo[charger_ix]."'");
	}
	//exit;
	
}
?>