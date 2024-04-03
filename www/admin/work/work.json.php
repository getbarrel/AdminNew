<?php
include("../class/layout.work.class");
include("work.lib.php");


$db = new Database;
$mdb = new Database;

//echo ($_SERVER["QUERY_STRING"]);
//print_r($_GET["charger_ix"]);

if ($sdate == ""){
	if(!$SelectReport){
		$SelectReport = "2";
		$firstdayofweek = mktime(0, 0, 0, date("m")  , date("d")-date("w"), date("Y"));
		$lastdayofweek = mktime(0, 0, 0, date("m")  , date("d")-date("w")+6, date("Y"));
		 
	//	$sDate = date("Y/m/d");
		$sdate = date("Ymd", $firstdayofweek);
		$edate = date("Ymd", $lastdayofweek);

		$dday_sdate = date("Ymd", $firstdayofweek);
	$dday_edate = date("Ymd", $lastdayofweek);
	}
	
}

	if(!isset($dp_ix)){
		$dp_ix == $admininfo[department];
	}

	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	
	if($_COOKIE["view_work_job"] == 1){
		$where = " where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' and wl.group_ix = wg.group_ix  ";
	}else{
		$where = " where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' and wl.group_ix = wg.group_ix  and wl.is_schedule = '1' ";
	}
	//if(!$parent_group_ix){
	//	$parent_group_ix = $parent_group_ix;
	//}
	//echo $_COOKIE["dynatree-work_group"]."<br>";
	if($_COOKIE["dynatree-work_group-select"]){
		$where .= " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
	}else if($parent_group_ix != "" && $group_ix == ""){
		$where .= " and (wg.group_ix = '".$parent_group_ix."' or wg.parent_group_ix = '".$parent_group_ix."') ";
	
	}else if($parent_group_ix != "" && $group_ix != ""){
		$where .= " and wg.parent_group_ix = '".$parent_group_ix."' ";
	
	}

		
	if($mail_yn == "Y"){	
		$where .= " and mail_yn =  '1' ";
	}else if($mail_yn == "N"){	
		$where .= " and mail_yn =  '0' ";
	}
	
	if($sms_yn == "Y"){	
		$where .= " and sms_yn =  '1' ";
	}else if($sms_yn == "N"){
		$where .= " and sms_yn =  '0' ";
	}
	
	
	if($search_type != "" && $search_text != ""){	
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}
	
		
	if($sdate != "" && $edate != ""){	
		$where .= " and  MID(replace(wl.sdate,'-',''),1,8) between  $sdate and $edate ";
	}
	
	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;
		
	if($dday_sdate != "" && $dday_edate != ""){	
		$where .= " and  MID(replace(dday,'-',''),1,8) between  $dday_sdate and $dday_edate ";
	}

	if($orderby && $ordertype){
		$orderby_string = " order by $orderby $ordertype, regdate desc";
	}else{
		$orderby_string = " order by start asc ";
	}
/*
	if($list_type == "mydepartment"){
		if(isset($dp_ix)){
			$where .= " and cmd.department =  '".$dp_ix."' ";
		}else{
			$where .= " and cmd.department =  '".$admininfo[department]."' ";
		}
	}
*/
//print_r($admininfo);
	if($list_type == "before"){
			$where .= " and dday < '".date("Ymd")."' and status != 'WC' ";
	}

	if($list_type == "today"){
			$where .= " and  '".date("Ymd")."' between wl.sdate and wl.dday ";
	}

	

	if($department != ""){
			$where .= " and (cmd.department =  '".$department."'  )  ";
	}

	if(is_array($_GET["work_status"])){
		for($i=0;$i < count($_GET["work_status"]);$i++){	
		
			
			if($_GET["work_status"][$i]){	
				if($work_status_str == ""){
					$work_status_str .= "'".$_GET["work_status"][$i]."'";
				}else{	
					$work_status_str .= ", '".$_GET["work_status"][$i]."' ";
				}
			}
		}
		
		if($work_status_str != ""){
			$where .= "and wl.status in ($work_status_str) ";
		}
	}else{		
		if($_GET["work_status"]){
			$where .= "and wl.status = '".$_GET["work_status"]."' ";
		}
		
	}

	$union_where = $where ." AND (cr.charger_ix = '".$admininfo[charger_ix]."') and cr.charger_ix = cmd.code " ;
	$where .= " and wl.charger_ix = cmd.code ";
	if($_COOKIE["dynatree-user-select"]){
		$where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
	}else if($charger_ix != ""){
			$where .= " and (wl.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
	}else{
		if($charger_ix != "" && false){
			//echo "aaa";
			$where .= " and (wl.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
			$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
		}else if($_COOKIE["dynatree-user-select"]){
			$where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
			$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))";	
		}else{
			$where .= " and (wl.charger_ix = '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))";		
			$union_where .= " and (cr.charger_ix = '".$admininfo[charger_ix]."')";		
			 
		}
	}

	// 전체 갯수 불러오는 부분
	$sql = "SELECT count(*) as total FROM work_list wl, work_group wg, common_member_detail cmd  $where ";
	//echo $sql;
	$db->query($sql);	
	
	$db->fetch();
	$total = $db->dt[total];
	//$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&list_type=$list_type&group_ix=$group_ix&dp_ix=$dp_ix&charger_ix=$charger_ix&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");
	
	
	if($list_view_type == "calendar"){
		if($SelectReport == "2"){
			$orderby_string = " order by sdate asc,stime asc ";
			$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);

			$sql = "SELECT wl.wl_ix as id, concat('[',AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),']',work_title) as title , concat(substring(sdate,1,4),'-',substring(sdate,5,2),'-',substring(sdate,7,2),'T',stime,':00') as start ,										concat(substring(dday,1,4),'-',substring(dday,5,2),'-',substring(dday,7,2),'T',dtime,':00') as end, 
			'custom' as className,
			case when sdate != dday or stime = '00:00' and dtime = '00:00' then 'true' else 'false' end as allDay
					FROM work_list wl, work_group wg, common_member_detail cmd   
					$where  $orderby_string ";	//546,553,520,
			//echo $sql;
			$db->query($sql);

		}else{
			/*
			$sql = "SELECT wl_ix as id, concat('[',AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),']',work_title,' ',sdate,stime,'~',dday,dtime) as title , concat(substring(sdate,1,4),'-',substring(sdate,5,2),'-',substring(sdate,7,2),'T',stime,':00') as start , concat(substring(dday,1,4),'-',substring(dday,5,2),'-',substring(dday,7,2),'T',dtime,':00') as end, case when sdate != dday or stime = '00:00' and dtime = '00:00' then 'true' else 'false' end as allDay
					FROM work_list wl, work_group wg, common_member_detail cmd   
					$where and substring(sdate,1,6) between '".date("Ym",mktime(0, 0, 0, date("m")-1  , date("d"), date("Y")))."' and '".date("Ym")."' $orderby_string ";	 //
			*/
			$sql = "SELECT wl.wl_ix as id, concat('[',AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),']',work_title) as title , concat(substring(sdate,1,4),'-',substring(sdate,5,2),'-',substring(sdate,7,2),'T',stime,':00') as start , 
			concat(substring(dday,1,4),'-',substring(dday,5,2),'-',substring(dday,7,2),'T',dtime,':00') as end, 
			case when sdate != dday or stime = '00:00' and dtime = '00:00' then 'true' else 'false' end as allDay, 
			case when is_schedule  = '1' then 'eventColorBasic' else 'event_green' end  as className
					FROM work_list wl, work_group wg, common_member_detail cmd   
					$where  ";	 //and substring(sdate,1,6) between '".date("Ym",mktime(0, 0, 0, date("m")-1  , date("d"), date("Y")))."' and '".date("Ym")."'
					// and substring(sdate,1,6) = '".date("Ym")."' 
			$sql .= "union
					SELECT wl.wl_ix as id, concat('[',AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),']',work_title) as title , concat(substring(sdate,1,4),'-',substring(sdate,5,2),'-',substring(sdate,7,2),'T',stime,':00') as start ,								concat(substring(dday,1,4),'-',substring(dday,5,2),'-',substring(dday,7,2),'T',dtime,':00') as end, case when sdate != dday or stime = '00:00' and dtime = '00:00' then 'true' else 'false' end as allDay
					, 'eventColorBasic' as className
					FROM  work_list wl , work_group wg, common_member_detail cmd,work_charger_relation cr 
					$union_where and wl.wl_ix = cr.wl_ix					
					GROUP BY wl.wl_ix
					$orderby_string  ";	
			//echo nl2br($sql)."<br><br>";
			//exit;
			$db->query($sql);
		}
	}else{
		$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name 
					FROM work_list wl, work_group wg, common_member_detail cmd   
					$where $orderby_string LIMIT $start, $max";	
		//echo $sql;
		$db->query($sql);

	}
	//echo $sql;
//exit;
	$events = $db->fetchall2("object");
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($events));
	//$datas = str_replace("\"true\"","true",$events);
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
?>