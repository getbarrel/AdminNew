<?
include("../../class/database.class");
include("./google/zend_lib_get.php");
$db = new Database;

$sql = "SELECT AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as  charger, cu.company_id, wu.charger_ix, wu.google_mail, wu.google_pass, wu.google_sync_yn, wu.google_sync_time, UNIX_TIMESTAMP(wu.google_sync_updatetime) as google_sync_updatetime  
		FROM work_userinfo wu , common_member_detail cmd, common_user cu
		WHERE wu.google_sync_yn='1' and wu.charger_ix = cmd.code and cmd.code = cu.code and wu.google_mail != '' and wu.google_pass != '' ";

echo $sql;
$db->query($sql);
$google_sync_lists = $db->fetchall();

for($i=0;$i < count($google_sync_lists);$i++){
	/*
	$sql = "SELECT google_mail, google_pass, google_sync_yn, google_sync_time, UNIX_TIMESTAMP(google_sync_updatetime) as google_sync_updatetime 
			FROM work_userinfo 
			WHERE charger_ix='".$google_sync_lists[$i][charger_ix]."'";
	
	$db->query($sql);
	$db->fetch();
	*/
	//echo $google_sync_lists[$i][google_sync_yn];
	//	exit;

	if($google_sync_lists[$i][google_sync_yn] == '1'){
		
		$google_mail = $google_sync_lists[$i][google_mail];
		$google_pass = $google_sync_lists[$i][google_pass];
		$google_sync_time = $google_sync_lists[$i][google_sync_time];

		//echo $google_sync_lists[$i][google_sync_updatetime]."==".mktime(date("H"), date("i")-$google_sync_time, date("s"), date("m")  , date("d"), date("Y"));
		if($google_sync_lists[$i][google_sync_updatetime] < mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"))){
			//echo "update";
			$chargerinfo[company_id] = $google_sync_lists[$i][company_id];
			$chargerinfo[charger_ix] = $google_sync_lists[$i][charger_ix];
			$chargerinfo[charger] = $google_sync_lists[$i][charger];

			GoogleSync($google_mail, $google_pass, $chargerinfo);
			$db->query("update work_userinfo set google_sync_updatetime = NOW() WHERE charger_ix='".$google_sync_lists[$i][charger_ix]."'");
		}
		//exit;
		
	}
}
?>