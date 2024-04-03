<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

if(!$admininfo){//$admininfo[admin_level] < 8 || 
	echo "<script>alert('관리자 로그인후 사용해주세요');parent.document.location.href='/admin/admin.php'</script>";
	exit;
}

$db = new Database;
// 조건절 셋팅



if ($act == "project_insert"){

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

	

	if($company_id == ""){
		$company_id = $admininfo[company_id];
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
		$group_ix = "54";
	}

	if($co_charger_yn == ""){
		$co_charger_yn ="N";
	}


	for($i=0;$i < count($architecture);$i++){
		if($architecture[$i][architecture_charger_ix] == ""){
			$charger_ix = $admininfo[charger_ix];
		}

		$sql = "insert into work_list
			(wl_ix,group_ix,company_id,charger_ix,work_title,status,complete_rate,is_schedule,is_hidden,is_report,importance, sdate,stime,dday,dtime,reg_name, reg_charger_ix, co_charger_yn, depth, architecture_code, update_date, edit_date, regdate)
			values
			('$wl_ix','$group_ix','".$company_id."','".$charger_ix."','".$architecture[$i][architecture_name]."','WR','$complete_rate','$is_schedule','$is_hidden','$is_report','$importance','".str_replace("-","",$architecture[$i][architecture_sdate])."','$stime','".str_replace("-","",$architecture[$i][architecture_edate])."','$dtime','".$admininfo[charger]."','".$admininfo[charger_ix]."','$co_charger_yn','".$architecture[$i][architecture_depth]."','".$architecture[$i][architecture_code]."',NOW(),NOW(),NOW())";

		echo $sql."<br><br>";

		$db->query($sql);
	}
 
	$db->query("select min(sdate) as project_sdate, max(dday) as project_edate from work_list wl where wl.group_ix = '".$group_ix."' and depth != '0' group by group_ix ");
	$db->fetch();
	$project_sdate = $db->dt[project_sdate];
	$project_edate = $db->dt[project_edate];

	$sql = "update work_group set group_name='$group_name',project_sdate='$project_sdate',project_edate='$project_edate',pm_charger_ix='$pm_charger_ix',disp='$disp' where company_id = '".$admininfo["company_id"]."' and group_ix='$group_ix'  ";
	
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('프로젝트가 정상적으로 등록 되었습니다.');parent.self.close();</script>");

}




if ($act == "project_update"){
	
//print_r($_POST);
//exit;
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

	

	if($company_id == ""){
		$company_id = $admininfo[company_id];
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
		$group_ix = "54";
	}

	if($co_charger_yn == ""){
		$co_charger_yn ="N";
	}

	//$db->query("delete from work_list where group_ix = '$group_ix' and depth != '0'  ");
	$_architecture_code[0] = 0;
	$_architecture_code[1] = 0;
	$_architecture_code[2] = 0;
	$_architecture_code[3] = 0;
	$_architecture_code[4] = 0;
	$_architecture_code[5] = 0;

	//print_r($architecture);
	//exit;
	//$db->debug = true;
	$db->query("update work_list wl set insert_yn = 'N' where group_ix = '$group_ix' and depth != '0' ");
	for($i=0;$i < count($architecture);$i++){
		if($architecture[$i][architecture_charger_ix] == ""){
			$charger_ix = $admininfo[charger_ix];
		}
		//echo "wl_ix:".$architecture[$i][wl_ix]."<br><br>";

		
		if($architecture[$i][architecture_name] != ""){
			$_architecture_code[$architecture[$i][architecture_depth]-1]++;
			$_architecture_code_str = "";
			for($j=0;$j < $architecture[$i][architecture_depth];$j++){
				$_architecture_code_str .= str_pad($_architecture_code[$j], 2, "0", STR_PAD_LEFT)."_";
			}
			if($architecture[$i][architecture_charger_ix]){
				$charger_ix = $architecture[$i][architecture_charger_ix];
			}

			if($architecture[$i][architecture_wl_ix] == ""){
			$sql = "insert into work_list
				(wl_ix,group_ix,company_id,charger_ix,work_title,status,complete_rate,is_schedule,is_hidden,is_report,importance, sdate,stime,dday,dtime,reg_name, reg_charger_ix, co_charger_yn, depth, sub_archietcture_cnt, architecture_code, insert_yn, update_date, edit_date, regdate)
				values
				('','$group_ix','".$company_id."','".$charger_ix."','".$architecture[$i][architecture_name]."','WR','$complete_rate','$is_schedule','$is_hidden','$is_report','$importance','".str_replace("-","",$architecture[$i][architecture_sdate])."','$stime','".str_replace("-","",$architecture[$i][architecture_edate])."','$dtime','".$admininfo[charger]."','".$admininfo[charger_ix]."','$co_charger_yn','".$architecture[$i][architecture_depth]."','0','".$_architecture_code_str."','Y',NOW(),NOW(),NOW())";
			}else{
			$sql = "update work_list set
					group_ix='$group_ix',charger_ix='".$charger_ix."',work_title='".$architecture[$i][architecture_name]."',is_hidden='$is_hidden',
					is_report='$is_report',importance='$importance',sdate='".str_replace("-","",$architecture[$i][architecture_sdate])."',stime='$stime',dday='".str_replace("-","",$architecture[$i][architecture_edate])."', sub_archietcture_cnt='0', architecture_code='".$_architecture_code_str."',insert_yn='Y',update_date=NOW(), edit_date=NOW()
					where wl_ix='".$architecture[$i][architecture_wl_ix]."' and company_id='$company_id' ";
			}

			if($before_architecture_depth != $architecture[$i][architecture_depth]){
				$_architecture_code[$architecture[$i][architecture_depth]] = 0;
			}
			$before_architecture_depth = $architecture[$i][architecture_depth];
			
		}

		//echo $sql."<br><br>\n\r";
		//exit;
		$db->query($sql);
	}
	//exit;
	$db->query("delete from work_list where group_ix = '".$group_ix."' and depth != '0' and insert_yn = 'N'  ");

	$db->query("select min(sdate) as project_sdate, max(dday) as project_edate, max(depth) as project_depth from work_list wl where wl.group_ix = '".$group_ix."' and depth != '0' and sdate != '' and dday != '' group by group_ix ");
	$db->fetch();
	$project_sdate = $db->dt[project_sdate];
	$project_edate = $db->dt[project_edate];
	$project_depth = $db->dt[project_depth];

	$sql = "update work_group set group_name='$group_name',project_sdate='$project_sdate',project_edate='$project_edate',pm_charger_ix='$pm_charger_ix',disp='$disp' 
			where company_id = '".$admininfo["company_id"]."' and group_ix='$group_ix'  ";

	$db->query($sql);

	$sql = "select depth, architecture_code
			from work_list wl 
			where group_ix = '".$group_ix."' and depth != '0' and depth <= ".($project_depth-1)." group by depth , architecture_code ";
	//echo $sql."<br>";;
	$db->query($sql);
	$summary_archietcture_info = $db->fetchall('object');
	for($i=0;$i < count($summary_archietcture_info);$i++){

		$sql = "select '".$summary_archietcture_info[$i][architecture_code]."' as parent_architecture_code , MIN(sdate) as sub_architecture_sdate, MAX(dday) as sub_architecture_edate , count(*) as sub_archietcture_cnt  
				from work_list where architecture_code LIKE '".$summary_archietcture_info[$i][architecture_code]."%' and architecture_code != '".$summary_archietcture_info[$i][architecture_code]."' 
				and group_ix = '".$group_ix."' and depth != '0' group by parent_architecture_code ";
		//echo $sql."<br><br><br>";
		$db->query($sql);
		if($db->total){
			$db->fetch();
			//echo $db->dt["sub_architecture_sdate"]."<br><br>";
			$sql = "update work_list set 
					sub_archietcture_cnt = '".$db->dt[sub_archietcture_cnt]."', 
					sdate = '".$db->dt["sub_architecture_sdate"]."', 
					dday = '".$db->dt[sub_architecture_edate]."' 
					where architecture_code ='".$db->dt[parent_architecture_code]."' and group_ix = '".$group_ix."'";
			$db->query($sql);
			//echo $sql."<br><br><br>";
		}
	}
	

	//exit;
	//echo("<script>parent.document.location.reload();</script>");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('프로젝트가 정상적으로 등록 되었습니다.','parent_reload');</script>");//parent.document.location.reload();

}

if ($act == "project_json"){


			$db->query("SELECT project_sdate, dayofyear(project_sdate) as project_sdate_num, dayofyear(project_sdate) - dayofweek(project_sdate)+1 as project_sdate_display_num FROM work_group where group_ix ='$group_ix' ");
		$db->fetch();
		
		$project_sdate = $db->dt[project_sdate];
		$project_sdate_num = $db->dt[project_sdate_num];
		$project_sdate_display_num = $db->dt[project_sdate_display_num];

	

	$sql = "select wl.wl_ix, wl.work_title,wl.charger_ix,  wl.work_detail,wl.sdate,wl.dday, wl.depth, wl.sub_archietcture_cnt, wl.status, wl.complete_rate, wl.is_schedule, wl.architecture_code, 
		dayofyear(wl.sdate) as sdate_num, dayofyear(wl.dday) as edate_num, '".$project_sdate."' as project_sdate, '".$project_sdate_num."' as project_sdate_num, '".$project_sdate_display_num."' as project_sdate_display_num
		from work_list wl where company_id ='".$admininfo["company_id"]."' 
		and wl.group_ix = '".$group_ix."' and depth != '0' order by architecture_code asc  ";
		$db->query($sql);
		
	//echo $sql;
	$events = $db->fetchall2("object");
	$datas = str_replace("\"true\"","true",json_encode($events));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
	exit;
}



if ($act == "personal_job_json"){
	$db->query("SELECT sdate, dayofyear(sdate) as project_sdate_num, dayofyear(sdate) - dayofweek(sdate)+1 as project_sdate_display_num FROM work_list where charger_ix ='$charger_ix' and status != 'WC' order by architecture_code asc, sdate desc limit 30 ");
	$db->fetch();
	
	$project_sdate = $db->dt[project_sdate];
	$project_sdate_num = $db->dt[project_sdate_num];
	$project_sdate_display_num = $db->dt[project_sdate_display_num];

	$sql = "select wl.wl_ix, wl.work_title,wl.charger_ix,  wl.work_detail,wl.sdate,wl.dday, wl.depth, wl.sub_archietcture_cnt, wl.status, wl.complete_rate, wl.is_schedule, wl.architecture_code, 
		dayofyear(wl.sdate) as sdate_num, dayofyear(wl.dday) as edate_num, '".$project_sdate_num."' as project_sdate_num, '".$project_sdate_display_num."' as project_sdate_display_num
		from work_list wl where company_id ='".$admininfo["company_id"]."' 
		and wl.charger_ix = '".$charger_ix."' and status != 'WC' order by architecture_code asc, sdate desc limit 30  ";
		$db->query($sql);
		
	//echo $sql;
	$events = $db->fetchall2("object");
	$datas = str_replace("\"true\"","true",json_encode($events));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
	exit;
}


?>