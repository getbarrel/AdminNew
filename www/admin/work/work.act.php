<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("work.lib.php");
include('../../include/xmlWriter.php');

session_start();

$db = new Database;


if ($act == "insert" || $act == "todo_insert" ){

	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}

	if(isset($is_hidden)){
		$is_hidden = 0;
	}

	if($sdate == ""){ $sdate = date("Ymd");}
	if($dday == ""){ $dday = date("Ymd");}

	if($shour == ""){ $shour = "00";}
	if($sminute == ""){$sminute = "00";}
	if($dhour == ""){ $dhour = "00";}
	if($dminute == ""){$dminute = "00";}

	$stime = $shour.":".$sminute;
	$dtime = $dhour.":".$dminute;

	
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		if($charger_ix == ""){
			$charger_ix = $admininfo[charger_ix];
		}
	}

	if($mobile_charger_ix){
		$reg_charger_ix = $mobile_charger_ix;
	}else{
		$reg_charger_ix = $admininfo[charger_ix];
	}

	if($mobile_company_id){
		$company_id = $mobile_company_id;
	}else{
		if($company_id == ""){
			$company_id = $admininfo[company_id];
		}
	}

	

	if($complete_rate == ""){
		$complete_rate = "0";
	}

	if($is_schedule == ""){
		$is_schedule = "0";
	}

	if($is_hidden == ""){
		$is_hidden = "0";
	}

	if($is_report == ""){
		$is_report = "0";
	}

	if($group_ix == ""){
		$group_ix = "11";
	}

	if($co_charger_yn == ""){
		$co_charger_yn ="N";
	}

	if($co_charger_yn == "Y"){
		if(is_array($co_charger_ix)){
			$co_charger_cnt = count($co_charger_ix);
		}else{
			$co_charger_cnt = 0;
		}
	}else{
		$co_charger_cnt = 0;
	}

	$sql = "insert into work_list
			(wl_ix,group_ix,company_id,charger_ix,work_title,work_detail,status,complete_rate,is_schedule,is_hidden,is_report,is_html,importance, sdate,stime,dday,dtime,work_where, reg_name, reg_charger_ix, co_charger_yn, co_charger_cnt, update_date, edit_date, regdate)
			values
			('$wl_ix','$group_ix','$company_id','".$charger_ix."','$work_title','$work_detail','WR','$complete_rate','$is_schedule','$is_hidden','$is_report','1','$importance','$sdate','$stime','$dday','$dtime','$work_where','".$admininfo[charger]."','".$reg_charger_ix."','$co_charger_yn','$co_charger_cnt',NOW(),NOW(),NOW())";

	$db->query($sql);

	$db->query("SELECT wl_ix FROM work_list WHERE wl_ix=LAST_INSERT_ID()");
	$db->fetch();
	$wl_ix = $db->dt[wl_ix];

	if($co_charger_yn == "Y"){
		for($i=0;$i < count($co_charger_ix);$i++){
			if($co_charger_ix[$i] != ""){
				$sql = "insert into work_charger_relation(cr_ix,wl_ix,charger_ix,insert_yn,regdate) values('','$wl_ix','".$co_charger_ix[$i]."','Y',NOW())";
				$db->query($sql);
			}
		}
	}



	if($mmode == "pop" || $mmode == "weelky_pop"){
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "업무 작성");
		if($mmode == "weelky_pop"){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
		}else{
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');parent.self.close();</script>");
		}
	}else if($mmode == "mobile"){
		$db->query("delete from work_tmp where  wt_ix ='".$wt_ix."' and charger_ix = '".$charger_ix."' ");

		if($wl_ix){
			$xml = new XmlWriter_();
			$xml->push('INSERT');
			$xml->element('RESULT', "SUCCESS");
			$xml->pop();
			print $xml->getXml();
			exit;
		}else{
			$xml = new XmlWriter_();
			$xml->push('INSERT');
			$xml->element('RESULT', "FAIL");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}else if($mmode == "json"){
		$sql = "SELECT wl.wl_ix, wl.work_title, wl.work_detail,wl.sdate, wl.stime, wl.dday, wl.dtime, wl.charger_ix, wl.reg_charger_ix, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as charger FROM work_list wl, work_group wg, common_member_detail cmd  where wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code and wl_ix=LAST_INSERT_ID() ";
		//echo $sql;
		$db->query($sql);

		//$db->query("SELECT work_title, work_ FROM work_list WHERE wl_ix=LAST_INSERT_ID()");
		//$db->fetch();
		$events = $db->fetchall2("object");
		$datas = str_replace("\"true\"","true",json_encode($events));
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
		exit;
	}else{
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "업무 작성");

		//echo("<script>alert('정상적으로 입력 되었습니다.');parent.document.location.reload();</script>");
		echo("<script>parent.unblockLoading();</script>");
	}
}

if ($act == "update"){
	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}

	if($is_hidden == ""){
		$is_hidden = "0";
	}

	if($is_schedule == ""){
		$is_schedule = "0";
	}

	if($co_charger_yn == ""){
		$co_charger_yn ="N";
	}

	$stime = $shour.":".$sminute;
	$dtime = $dhour.":".$dminute;

	if($complete_rate > 0){
		if($complete_rate == 100){
			$_work_status = "WC";
		}else{
			$_work_status = "WI";
		}
	}else{
		$_work_status = $_POST["work_status"];
	}

	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		if($charger_ix == ""){
			$charger_ix = $admininfo[charger_ix];
		}
	}

	if($group_ix == ""){
		$group_ix = "11";
	}

	if($co_charger_yn == "Y"){
		if(is_array($co_charger_ix)){
			$co_charger_cnt = count($co_charger_ix);
		}else{
			$co_charger_cnt = 0;
		}
	}else{
		$co_charger_cnt = 0;
	}


	$sql = "update work_list set
					group_ix='$group_ix',company_id='$company_id',charger_ix='$charger_ix',work_title='$work_title',work_detail='$work_detail',status='".$_work_status."',is_hidden='$is_hidden',
					is_report='$is_report',is_html='1',importance='$importance',sdate='$sdate',stime='$stime',dday='$dday', dtime='$dtime', work_where='$work_where', complete_rate='$complete_rate', is_schedule='$is_schedule', co_charger_yn='$co_charger_yn',co_charger_cnt='$co_charger_cnt', google_sync_cnt = '0', update_date=NOW(), edit_date=NOW()
					where wl_ix='$wl_ix' ";

	//echo $sql;
	$db->query($sql);
	

	$sql = "update work_charger_relation set insert_yn = 'N' where wl_ix='$wl_ix' ";
	$db->query($sql);
	//print_r($_POST);
	//exit;
	//echo count($co_charger_ix);
	if($co_charger_yn == "Y"){
		for($i=0;$i < count($co_charger_ix);$i++){
			if($co_charger_ix[$i] != ""){
				$sql = "select cr_ix from work_charger_relation where wl_ix='$wl_ix' and charger_ix = '".$co_charger_ix[$i]."'";
				$db->query($sql);
				if($db->total){
					$sql = "update work_charger_relation set insert_yn = 'Y' where wl_ix='$wl_ix' and charger_ix = '".$co_charger_ix[$i]."' ";
				}else{
					$sql = "insert into work_charger_relation(cr_ix,wl_ix,charger_ix,insert_yn,regdate) values('','$wl_ix','".$co_charger_ix[$i]."','Y',NOW())";
				}
				$db->query($sql);
			}
		}
	}
	$sql = "delete from work_charger_relation  where wl_ix='$wl_ix' and insert_yn = 'N'  ";
	$db->query($sql);

	WorkHistory($db, $wl_ix, $admininfo[charger_ix], "U", "업무 변경");

	if($mmode == "pop" || $mmode == "weelky_pop"){
		if($mmode == "weelky_pop"){
			if($is_close){
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
			}else{
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');parent.opener.document.location.reload();parent.unblockLoading();</script>");
			}
		}else{
			if($is_close){
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');parent.self.close();</script>");
			}else{
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');parent.unblockLoading();</script>");
			}
		}
		
		
	}else if($mmode == "mobile"){
		$xml = new XmlWriter_();
		$xml->push('UPDATE');
		$xml->element('RESULT', "SUCCESS");
		$xml->pop();
		print $xml->getXml();
		exit;
	}else{
		//echo("<script>alert('정상적으로 수정 되었습니다. ');parent.document.location.reload();</script>");
		echo("<script>parent.unblockLoading();</script>");
	}
}



if ($act == "date_update"){

	$sql = "update work_list set
					sdate='$sdate',stime='$stime',dday='$dday', dtime='$dtime', google_sync_cnt = '0', update_date=NOW()
					where wl_ix='$wl_ix' ";

	//echo $sql;
	$db->query($sql);

	WorkHistory($db, $wl_ix, $admininfo[charger_ix], "U", "날짜 변경");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다. ');parent.document.location.reload();</script>");
}

if ($act == "complete_rate_update"){

	if($complete_rate == "100"){
		$status = "WC";
	}else if($complete_rate == "0"){
		$status = "WR";
	}else if($complete_rate == "-1"){
		$status = "WD";
	}else{
		$status = "WI";
	}
	$sql = "update work_list set
					complete_rate='$complete_rate', google_sync_cnt = '0', update_date=NOW() ,status='".$status."'
					where wl_ix='$wl_ix' ";

	//echo $sql;
	$db->query($sql);
	//include("work.lib.php");
	if($complete_rate == "100"){
		echo $work_status["WC"]."(".$complete_rate."%)";
	}else if($complete_rate == "0"){
		echo $work_status["WR"]."(".$complete_rate."%)";
	}else if($complete_rate == "-1"){
		echo $work_status["WD"]."";
	}else{
		echo $work_status["WI"]."(".$complete_rate."%)";
	}

	if($status == "WD"){
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "U", "업무 취소  ");
	}else{
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "U", "업무 진행율 변경 ".$complete_rate."% ");
	}
	//echo("<script>alert('정상적으로 수정 되었습니다. ');parent.document.location.reload();</script>");
}


if ($act == "delete"){
	$db->query("delete from work_list where  wl_ix ='$wl_ix' ");
	$db->query("delete from work_charger_relation where  wl_ix ='$wl_ix' ");

	$db->query("select count(*) as total from work_report where  wl_ix ='$wl_ix' ");
	$db->fetch();
	$report_cnt = $db->dt[total];
	$db->query("delete from work_report where  wl_ix ='$wl_ix' ");

	$db->query("select count(*) as total from work_comment where  wl_ix ='$wl_ix' ");
	$db->fetch();
	$comment_cnt = $db->dt[total];
	$db->query("delete from work_comment where  wl_ix ='$wl_ix' ");

	$db->query("select count(*) as total from work_issue where  wl_ix ='$wl_ix' ");
	$db->fetch();
	$issue_cnt = $db->dt[total];
	$db->query("delete from work_issue where  wl_ix ='$wl_ix' ");


	$sql = "update work_list set edit_date=NOW(), report_cnt = report_cnt - ".$report_cnt.", comment_cnt = comment_cnt - ".$comment_cnt." , issue_cnt = issue_cnt - ".$issue_cnt." where wl_ix='$wl_ix' ";
	$db->query("$sql");

	WorkHistory($db, $wl_ix, $admininfo[charger_ix], "D", "업무 삭제  ");
	if($mmode == "pop" || $mmode == "weelky_pop"){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다...');try{parent.opener.document.location.reload();parent.self.close();}catch(e){};parent.self.close();</script>");
	}else if($mmode == "mobile"){
		$xml = new XmlWriter_();
		$xml->push('DELETE');
		$xml->element('RESULT', "SUCCESS");
		$xml->pop();
		print $xml->getXml();
		exit;

	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다. [일반]');parent.document.location.reload();</script>");
	}

}

if ($act == "work_tmp_insert"){
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}

	$db->query("insert into work_tmp(wt_ix,charger_ix,work_tmp_title, regdate) values('','".$charger_ix."','".$work_tmp_title."',NOW()) ");
	$db->query("SELECT wt_ix FROM work_tmp WHERE wt_ix=LAST_INSERT_ID()");
	$db->fetch();
	if($db->total){
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('INSERT');
			$xml->element('RESULT', "SUCCESS");
			$xml->pop();
			print $xml->getXml();
			exit;
		}else{
			echo $db->dt[wt_ix];
		}
	}else{
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('INSERT');
			$xml->element('RESULT', "FAIL");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}
	//echo("<script>alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");
}

if ($act == "work_tmp_delete"){
	//echo "delete from work_tmp where  wt_ix ='$wt_ix' ";
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}
	$db->query("select * from work_tmp where  wt_ix ='$wt_ix' and charger_ix = '".$charger_ix."' ");
	if($db->total){
		$db->query("delete from work_tmp where  wt_ix ='$wt_ix' and charger_ix = '".$charger_ix."' ");

		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "SUCCESS");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}else{
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "FAIL");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}
	//echo("<script>alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");

}

if ($act == "report_insert"){

	
	
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}

	$db->query("insert into work_report(wr_ix,company_id, wl_ix,report_type,report_title, report_desc,report_file, charger_ix, regdate) values('','".$admininfo[company_id]."','$wl_ix','$report_type','$report_title','$report_desc','".$_FILES[report_file][name]."','".$charger_ix."',NOW()) ");
	$db->query("SELECT wr_ix FROM work_report WHERE wr_ix=LAST_INSERT_ID()");
	$db->fetch();
	$wr_ix = $db->dt[wr_ix];

	$sql = "update work_list set edit_date=NOW(), report_cnt = report_cnt + 1 where wl_ix='$wl_ix' ";
	//echo $sql;
	$db->query($sql);


	if ($_FILES["report_file"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/";
		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/report/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}



		$path .= $wr_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}
	}

	if ($_FILES["report_file"][size] > 0){
		move_uploaded_file($_FILES[report_file][tmp_name], $path."/".iconv('UTF-8','EUC-KR',$_FILES[report_file][name]));
		chmod($path."/".iconv('UTF-8','EUC-KR',$_FILES[report_file][name]),0777);
	}
	
	if($mmode == "mobile"){
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "보고서 작성");

		$xml = new XmlWriter_();
		$xml->push('INSERT');
		$xml->element('RESULT', "SUCCESS");
		$xml->pop();
		print $xml->getXml();
		exit;
	}else{
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "보고서 작성");
		
		echo("<script>parent.document.location.reload();</script>");
		if($mmode == "weekly_pop"){
			echo("<script>self.close();</script>");
		}
	}
}


if ($act == "report_update"){

	
	
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}
	
	if($admininfo[master] == "Y"){
	$sql = "update work_report set report_type='$report_type',report_title='$report_title',report_desc='$report_desc',report_file='".$_FILES[report_file][name]."'
			where wr_ix='$wr_ix' and wl_ix='$wl_ix' ";
	}else{
	$sql = "update work_report set report_type='$report_type',report_title='$report_title',report_desc='$report_desc',report_file='".$_FILES[report_file][name]."'
			where wr_ix='$wr_ix' and wl_ix='$wl_ix' and charger_ix='$charger_ix' ";
	}
	//echo $sql;
	//exit;
	$db->query($sql);
	



	if ($_FILES["report_file"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/";
		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/report/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}



		$path .= $wr_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}
	}

	if ($_FILES["report_file"][size] > 0){
		move_uploaded_file($_FILES[report_file][tmp_name], $path."/".iconv('UTF-8','EUC-KR',$_FILES[report_file][name]));
		chmod($path."/".iconv('UTF-8','EUC-KR',$_FILES[report_file][name]),0777);
	}
	
	if($mmode == "mobile"){
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "U", "보고서 수정");

		$xml = new XmlWriter_();
		$xml->push('UPDATE');
		$xml->element('RESULT', "SUCCESS");
		$xml->pop();
		print $xml->getXml();
		exit;
	}else{
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "U", "보고서 수정");

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보고서가 정상적으로 수정되었습니다.');</script>");
		if($close_yn == "Y"){
			echo("<script>parent.self.close();</script>");
		}else{
			echo("<script>parent.location.reload();</script>");
		}

		if($mmode == "weekly_pop"){
			echo("<script>self.close();</script>");
		}
	}
}

if ($act == "report_delete"){
	//echo "delete from work_tmp where  wt_ix ='$wt_ix' ";
	if($wr_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/work/report/".$wr_ix)){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/work/report/".$wr_ix);
	}

	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}
	$db->query("select * from work_report where  wr_ix ='$wr_ix' and charger_ix = '".$charger_ix."' ");
	if($db->total){
		$db->query("delete from work_report where  wr_ix ='$wr_ix' and charger_ix = '".$charger_ix."' ");

		$sql = "update work_list set edit_date=NOW(), report_cnt = report_cnt - 1 where wl_ix='$wl_ix' ";
		//echo $sql;
		$db->query($sql);

		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "D", "보고서 삭제");
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "SUCCESS");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}else{
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "FAIL");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}

	if($mmode == "weekly_pop"){
		echo("<script>self.close();</script>");
	}
	//echo("<script>alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");

}

if ($act == "comment_insert"){
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}
	
	$db->query("insert into work_comment(wc_ix,wl_ix,comment,comment_file,charger_ix , regdate) values('','$wl_ix','$comment','".$_FILES[comment_file][name]."','".$charger_ix."',NOW()) ");
	$db->query("SELECT wc_ix FROM work_comment WHERE wc_ix=LAST_INSERT_ID()");
	$db->fetch();
	$wc_ix = $db->dt[wc_ix];
	$sql = "update work_list set edit_date=NOW(), comment_cnt = comment_cnt + 1 where wl_ix='$wl_ix' ";
	//echo $sql;
	$db->query($sql);

	// 협업자가 있을시 협업자에게 이메일보내기 S : 이현우(2013-05-16)		
	$sql = 	"SELECT  AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
				FROM work_charger_relation cr, common_member_detail cmd  
				where cr.charger_ix = cmd.code and wl_ix ='$wl_ix'  ";
	$db->query($sql);
	$relation_arr = $db->fetchall();
	$relation_cnt = $db->total;

	// 이메일 발송
	if ($relation_cnt){
		// 업무관리 정보조회
		$sql = "SELECT work_title FROM work_list WHERE wl_ix='$wl_ix' ";
		$db->query($sql);
		$db->fetch();
		$work_title = $db->dt[work_title];

		// 이메일발송
		include_once("../../include/email.send.php");
		$subject = "[업무관리] ".$work_title." 업무에 코멘트가 작성되었습니다.";
		$link = "/admin/work/work_view.php?mmode=weekly_pop&wl_ix=".$wl_ix;
		$comment.= "코멘트 내용 <BR><BR>".$comment."<BR><BR>업무관리 LINK - <a href='".$link."' target='_blank'>".$link."</a>";
		$from_name = "포비즈";
		$from = "cs@forbiz.co.kr";
		$minfo = array();
		//error_reporting(E_ALL);
		for ($i=0; $i<$relation_cnt; $i++){
			$minfo[mem_mail] = $relation_arr[$i]["mail"];
			$minfo[mem_name] = $relation_arr[$i]["name"];		
			$to = "hidejj@forbiz.co.kr";
			//echo $to." ".$to_name." ".$from." ".$from_name." ".$subject." ".$comment."<BR>";
			//shop_sendmail("html", $to, $to_name, $from, $from_name, $subject, $comment);
			SendMail($minfo, $subject, $comment);
			echo $i."<BR>";
		}		
		exit;
	}
	// 협업자가 있을시 협업자에게 이메일보내기 E : 이현우(2013-05-16)

	if ($_FILES["comment_file"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/comment/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path .= $wc_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}
	}


	if ($_FILES["comment_file"][size] > 0){
		move_uploaded_file($_FILES[comment_file][tmp_name], $path."/".iconv('UTF-8','EUC-KR',$_FILES[comment_file][name]));
		chmod($path."/".iconv('UTF-8','EUC-KR',$_FILES[comment_file][name]),0777);
	}

	if($mmode == "mobile"){
		$xml = new XmlWriter_();
		$xml->push('INSERT');
		$xml->element('RESULT', "SUCCESS");
		$xml->pop();
		print $xml->getXml();
		exit;
	}
	WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "컴멘트 작성");

	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "comment_delete"){
	if($wc_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/work/comment/".$wc_ix)){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/work/comment/$wc_ix/");
	}
	//echo "delete from work_tmp where  wt_ix ='$wt_ix' ";
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}

	$db->query("select * from work_comment where  wc_ix ='$wc_ix' and charger_ix = '".$charger_ix."' ");
	if($db->total){
		$db->query("delete from work_comment where  wc_ix ='$wc_ix' and charger_ix = '".$charger_ix."' ");

		$sql = "update work_list set edit_date=NOW(), comment_cnt = comment_cnt - 1 where wl_ix='$wl_ix' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		print_r($db->dt);

		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "SUCCESS");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "D", "컴멘트 삭제");
	}else{
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "FAIL");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}
	//echo("<script>alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");

}





if ($act == "issue_insert"){
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}
	
	$db->query("insert into work_issue(wi_ix,wl_ix,issue,issue_file,issue_level,charger_ix , regdate) values('','$wl_ix','$issue','".$_FILES[issue_file][name]."','$issue_level','".$charger_ix."',NOW()) ");
	$db->query("SELECT wi_ix FROM work_issue WHERE wi_ix=LAST_INSERT_ID()");
	$db->fetch();
	$wi_ix = $db->dt[wi_ix];
	$sql = "update work_list set edit_date=NOW(), issue_cnt = issue_cnt + 1 where wl_ix='$wl_ix' ";
	//echo $sql;
	$db->query($sql);

	if ($_FILES["issue_file"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/issue/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path .= $wi_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}
	}


	if ($_FILES["issue_file"][size] > 0){
		move_uploaded_file($_FILES[issue_file][tmp_name], $path."/".iconv('UTF-8','EUC-KR',$_FILES[issue_file][name]));
		chmod($path."/".iconv('UTF-8','EUC-KR',$_FILES[issue_file][name]),0777);
	}

	if($mmode == "mobile"){
		$xml = new XmlWriter_();
		$xml->push('INSERT');
		$xml->element('RESULT', "SUCCESS");
		$xml->pop();
		print $xml->getXml();
		exit;
	}
	WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "컴멘트 작성");

	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "issue_delete"){
	if($wi_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/work/issue/".$wi_ix)){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/work/issue/$wi_ix/");
	}
	//echo "delete from work_tmp where  wt_ix ='$wt_ix' ";
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}

	$db->query("select * from work_issue where  wi_ix ='$wi_ix' and charger_ix = '".$charger_ix."' ");
	if($db->total){
		$db->query("delete from work_issue where  wi_ix ='$wi_ix' and charger_ix = '".$charger_ix."' ");

		$sql = "update work_list set edit_date=NOW(), issue_cnt = issue_cnt - 1 where wl_ix='$wl_ix' ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		//print_r($db->dt);

		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "SUCCESS");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
		WorkHistory($db, $wl_ix, $admininfo[charger_ix], "D", "컴멘트 삭제");
	}else{
		if($mmode == "mobile"){
			$xml = new XmlWriter_();
			$xml->push('DELETE');
			$xml->element('RESULT', "FAIL");
			$xml->pop();
			print $xml->getXml();
			exit;
		}
	}
	//echo("<script>alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");

}

if ($act == "issue_close"){
	
	if($mobile_charger_ix){
		$charger_ix = $mobile_charger_ix;
	}else{
		$charger_ix = $admininfo[charger_ix];
	}
	//echo "update work_issue set status = 'C' where  wi_ix ='".$wi_ix."' and charger_ix = '".$charger_ix."' ";

	$db->query("update work_issue set status = 'C' where  wi_ix ='".$wi_ix."'  ");
	//and charger_ix = '".$charger_ix."'

	WorkHistory($db, $wl_ix, $admininfo[charger_ix], "C", "이슈 상태변경 [close] - ".$wi_ix." ");

	echo("<script>parent.document.location.reload();</script>");

}

function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}


?>