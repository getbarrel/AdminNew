<?
include("../../../class/database.class");
include('../../../include/xmlWriter.php');
include('../../include/admin.util.php');

$db = new Database;
$mdb = new Database;

if($act && false){
	foreach($_POST as $name => $value){
		if(!$post_str){
			$post_str = $name."=".$value;
		}else{
			$post_str .= "&".$name."=".$value;
		}
	}
	$write = date('Y-m-d H:i:s')." ".$_SERVER["REQUEST_URI"]." ".$post_str." \n";
	$path = $_SERVER["DOCUMENT_ROOT"]."/_logs/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		//chmod($path,0777);
	}


	$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/_logs/mobile_conn_log.txt","a+");
	fwrite($fp,$write);
	fclose($fp);
}
if ($act == "login")
{

	if($pw == "shin0606"){
		$sql = "SELECT cu.company_id, cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cmd.mail , cmd.pcs 
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_COMPANY_DETAIL." ccd, service_ing si
				WHERE cu.code = cmd.code and cu.company_id = cu.company_id 
				and cu.code = si.mem_ix
				and cu.id = TRIM('".$id."')  ";
	
		$db->query($sql);
	}else{	
		$sql = "SELECT cu.company_id, cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cmd.mail , cmd.pcs 
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_COMPANY_DETAIL." ccd, service_ing si
				WHERE cu.code = cmd.code and cu.company_id = cu.company_id 
				and cu.code = si.mem_ix
				and cu.id = TRIM('".$id."')  and cu.pw = MD5('$pw') ";
		
		$db->query($sql);
	}

	

	
	if ($db->total && TRIM($id) != "" && TRIM($pw) != "")
	{
		$db->fetch();		
		
		$xml = new XmlWriter_();
		$xml->push('LOGIN');
		$xml->element('RESULT', "SUCCESS");    
		$xml->element('MOBILE_COMPANY_ID', $db->dt[company_id]);    
		$xml->element('MOBILE_CHARGER_IX', $db->dt[code]);   
		$xml->element('MOBILE_CHARGER_NAME', $db->dt[name]);   
		$xml->element('MOBILE_CHARGER_PHONE', $db->dt[tel]);   
		$xml->element('MOBILE_CHARGER_EMAIL', $db->dt[mail]);  
		$xml->pop();
		print $xml->getXml();
		
		exit;
	}else{
		$xml = new XmlWriter_();
		$xml->push('LOGIN');
		$xml->element('RESULT', "FAILE");
		$xml->pop();
		print $xml->getXml();
		exit;
	}
}else if($act== "logout"){
	session_unregister("admininfo");
}


if($act == "taskList"){
	
	$list_view_type = $_GET["list_view_type"];
	
	if($mobile_company_id == ""){
		$mobile_company_id = "3444fde7c7d641abc19d5a26f35a12cc";
	}
	//print_r($_GET);
	if ($sdate == ""){
		$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));
		$firstdayofweek = mktime(0, 0, 0, date("m")  , date("d")-date("w"), date("Y"));
	
		
		$startDate = date("Y/m/d", $firstdayofweek);
		$endDate = date("Y/m/d");
	}else{
		$startDate = $FromYY."/".$FromMM."/".$FromDD;
		$endDate = $ToYY."/".$ToMM."/".$ToDD;

		$birDate = $birYY.$birMM.$birDD;
	}
	
		if(!isset($dp_ix)){
			$dp_ix == $admininfo[department];
		}

		

		$max = 20; //페이지당 갯수

		if ($page == '')
		{
			$start = 0;
			$page  = 1;
		}
		else
		{
			$start = ($page - 1) * $max;
		}

		
		$where = " where wl.company_id = '".$mobile_company_id."' and wl.wl_ix != '' 
					and wl.group_ix = wg.group_ix 
					and (wl.charger_ix =  '".$mobile_charger_ix."' or  (wl.charger_ix !=  '".$mobile_charger_ix."' and is_hidden = '0'))";
		
		//$where = " where wl.company_id = '".$mobile_company_id."' and wl.wl_ix != '' and wl.group_ix = wg.group_ix  and wl.is_schedule = '1' ";

		//if(!$parent_group_ix){
		//	$parent_group_ix = $parent_group_ix;
		//}
		if(!($_COOKIE[view_complete_job] == 1)){
			$where .= " and (wl.status not in ('WC') ) ";
		}

		if(!($_COOKIE[view_project_job] == 1)){
			$where .= " and wl.depth = '0'  ";
		}

		if($is_schedule){
			$where .= " and wl.is_schedule = '1'  ";
		}


		if($group_ix != ""){
			$where .= " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
		}else if($_COOKIE["dynatree-work_group-select"]){
			$where .= " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
		}
		/*
		if($parent_group_ix != "" && $group_ix == ""){
			$where .= " and (wg.group_ix = '".$parent_group_ix."' or wg.parent_group_ix = '".$parent_group_ix."') ";
		}else if($parent_group_ix != "" && $group_ix != ""){
			$where .= " and wg.parent_group_ix = '".$parent_group_ix."' ";
		}
		*/

		//	if($list_type == "myjob" || $list_type == "before"){
		if($group_ix != ""){
		//	$where .= " and (wg.group_ix =  '".$group_ix."' or wg.parent_group_ix =  '".$group_ix."' )  ";
		}
	//	}


		


			
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
		
		$startDate = $FromYY.$FromMM.$FromDD;
		$endDate = $ToYY.$ToMM.$ToDD;
			
		//if($list_view_type == "calendar"){
			if($sdate != "" && $edate != ""){	
				$where .= " and  MID(replace(wl.sdate,'-',''),1,8) between  $sdate and $edate ";
			}
		
		
			$vstartDate = $vFromYY.$vFromMM.$vFromDD;
			$vendDate = $vToYY.$vToMM.$vToDD;
				
			if($dday_sdate != "" && $dday_edate != ""){	
				$where .= " and  MID(replace(dday,'-',''),1,8) between  $dday_sdate and $dday_edate ";
			}
		//}
		if($orderby && $ordertype){
			$orderby_string = " order by $orderby $ordertype, regdate desc";
		}else{
			$orderby_string = " order by sdate desc, stime asc ";//edit_date desc, 
		}

		if($list_type == "mydepartment"){
			if(!empty($dp_ix)){
				$where .= " and cu.department =  '".$dp_ix."' ";
			}else{
				$where .= " and cu.department =  '".$admininfo[department]."' ";
			}
		}else{
			if($dp_ix != ""){
				$where .= " and (cu.department =  '".$dp_ix."'  )  ";
			}
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

		$union_where = $where ." and cr.charger_ix = cmd.code " ;
		
		$where .= " and wl.charger_ix = cmd.code ";
		//echo $list_type."<br>";
		if($list_type == "myjob"){
				$where .= " and wl.charger_ix =  '".$mobile_charger_ix."'   ";
				$union_where .= " and cr.charger_ix =  '".$mobile_charger_ix."'  ";
		}else if($list_type == "before"){
				$where .= " and dday < '".date("Ymd")."' and status not in ('WC', 'WD') and wl.charger_ix =  '".$mobile_charger_ix."' ";
				$union_where .= " and dday < '".date("Ymd")."' and status not in ('WC', 'WD') and wl.charger_ix =  '".$mobile_charger_ix."' ";
		}else if($list_type == "today"){
				$where .= " and  '".date("Ymd")."' between wl.sdate and wl.dday and wl.charger_ix =  '".$mobile_charger_ix."' ";
				$union_where .= " and  '".date("Ymd")."' between wl.sdate and wl.dday and wl.charger_ix =  '".$mobile_charger_ix."' ";
		}else{
			if($mobile_charger_ix != "" && false){
				//echo "aaa";
				$where .= " and (wl.charger_ix in ('".str_replace(",","','",$mobile_charger_ix)."'))  ";
				$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$mobile_charger_ix)."'))  ";
			}else if($_COOKIE["dynatree-user-select"]){
				$where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
				$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))";	
			}else{
				$union_where .= " and (cr.charger_ix = '".$mobile_charger_ix."')";		
			}
		}
		

	if($list_view_type != "calendar"){

		// 전체 갯수 불러오는 부분
			$sql = "SELECT count(*) as total FROM work_list wl, work_group wg, common_member_detail cmd  $where ";
			//echo $sql;
			$db->query($sql);	
			
			$db->fetch();
			$total = $db->dt[total];
		
			if($is_schedule != 1){
				//$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix,cmd.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, '' as co_charger_ix, count(*) as co_chager_cnt 
				$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name as charger, '' as co_charger_ix, count(*) as co_chager_cnt
						FROM work_list wl 
						left join work_charger_relation cr on wl.wl_ix = cr.wl_ix 
						left join work_group wg on wl.group_ix = wg.group_ix 
						left join common_member_detail cmd on wl.charger_ix = cmd.code 
						$where  
						GROUP BY wl.wl_ix ";	
		//				SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix as co_charger_ix , count(*) as co_chager_cnt
				
				$sql .= "
						union
						SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name as charger, cr.charger_ix as co_charger_ix , (select count(*)  from work_charger_relation wcr where wcr.wl_ix = wl.wl_ix ) as co_chager_cnt
						FROM work_list wl 
						left join work_charger_relation cr on wl.wl_ix = cr.wl_ix 
						left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
						$union_where
						GROUP BY wl.wl_ix
						$orderby_string LIMIT $start, $max ";
			}else{
				$sql = "SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name as charger, cr.charger_ix as co_charger_ix , (select count(*)  from work_charger_relation wcr where wcr.wl_ix = wl.wl_ix ) as co_chager_cnt
						FROM work_list wl 
						left join work_charger_relation cr on wl.wl_ix = cr.wl_ix 
						left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
						$union_where
						GROUP BY wl.wl_ix
						$orderby_string LIMIT $start, $max ";

						//echo $sql;
						//exit;
			}
		//echo nl2br($sql);
		//exit;
		$db->query($sql);

	}

	


		$works = $db->fetchall();
		$xml = new XmlWriter_();

		if($is_schedule==1){
			$parent_tag = "SCHEDULES";
			$sub_tag = "SCHEDULE";
		}else{
			$parent_tag = "WORKS";
			$sub_tag = "WORK";
		}
		$xml->push($parent_tag);
			$xml->push('PAGE');
			$xml->element('CURRENTPAGE', $page);
			$xml->element('MAXPAGE', ceil($total/$max));
			$xml->pop();
		foreach ($works as $work) {    
			//$xml->push('shop', array('species' => $animal[0]));    
			//$xml->push('SCHEDULES', array('pcode' => $layout[pcode],'basic_link' => $layout[basic_link],'depth' => $layout[depth]));   
			if($work[group_depth] == 2){
				$db->query("SELECT group_name FROM work_group WHERE group_ix  = '".$work[parent_group_ix]."' ");
				$db->fetch(0);
				$group_name = $db->dt[group_name]." > ".$work[group_name];
			}else{
				$group_name = $work[group_name];
			}

			if($work[co_chager_cnt] > 1){
				$sql = 	"SELECT  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix
					FROM work_charger_relation cr, common_member_detail cmd  
					where cr.charger_ix = cmd.code and wl_ix ='".$work[wl_ix]."'  
					union
					SELECT  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, wl.charger_ix
					FROM work_list wl, common_member_detail cmd  
					where wl.charger_ix = cmd.code and wl.wl_ix ='".$work[wl_ix]."'  ";
				//echo $sql;
				//exit;
				$db->query($sql);
				$co_charger_ix_rows = $db->getrows();
				//$co_charger_ix = $co_charger_ix_rows[0];
				if(is_array($co_charger_ix_rows)){
					$charger = implode(",", $co_charger_ix_rows[0]);
					//$charger = $work[charger].",".$charger;
				}
			}else{
				$charger = $work[charger];
			}

			$xml->push($sub_tag);
			$xml->element('ID', $work[wl_ix]);    
			$xml->element('STARTDATE', changeDate($work[sdate],"Y:m:d").":".$work[stime]);    
			$xml->element('ENDDATE', changeDate($work[dday],"Y:m:d").":".$work[dtime]);    
			$xml->element('TITLE', $work[work_title]);
			$xml->element('BODY', $work[work_detail]);
			$xml->element('PROGRESS', $work[complete_rate]);
			$xml->element('CATEGORY', $group_name);
			$xml->element('COWORK', $work[rightmenu]);
			$xml->element('COWORKNUM', $work[co_chager_cnt]);
			$xml->element('COMMENTNUM', $work[comment_cnt]);
			$xml->element('PRIVATE', $work[is_hidden]);
			$xml->element('IMPORTANT', $work[importance]);
			$xml->element('PERSONINCHARGE', $charger);
			$xml->pop();
		}
			
		$xml->pop();
		print $xml->getXml();
		
		
}


if($act == "todoList"){

		$sql = "SELECT wt_ix, work_tmp_title, regdate FROM work_tmp  where charger_ix = '".$mobile_charger_ix."' order by regdate desc";
		//	echo $sql;
		$db->query($sql);
		

		$todos = $db->fetchall();
		$xml = new XmlWriter_();
		$xml->push('TODOS');
		
		if(is_array($todos)){
			foreach ($todos as $todo) {    

				$xml->push('TODO');
				$xml->element('ID', $todo[wt_ix]);    
				$xml->element('TITLE', $todo[work_tmp_title]);    
				$xml->element('REGDATE', $todo[regdate]);
				$xml->pop();
			}
		}
			
		$xml->pop();
		print $xml->getXml();
}


if($act == "commentList"){

		$sql = "SELECT wc_ix, comment, wc.charger_ix, wc.comment_file, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,  wc.regdate FROM work_comment wc, common_member_detail cmd  where wc.charger_ix = cmd.code and wc.wl_ix = '".$wl_ix."' order by regdate desc";
		//	echo $sql;
		$db->query($sql);
		

		$comments = $db->fetchall();
		
		$xml = new XmlWriter_();
		$xml->push('COMMENTS');
		
		if(is_array($comments)){
			foreach ($comments as $comment) {    

				$xml->push('COMMENT');
				$xml->element('ID', $comment[wc_ix]);    
				$xml->element('COMMENT_DESC', $comment[comment]);    
				$xml->element('CHARGER', $comment[name]);    
				$xml->element('IS_CHARGER', ($comment[charger_ix] == $mobile_charger_ix ? "Y":"N"));   
				$xml->element('REGDATE', $comment[regdate]);
				$xml->pop();
			}
		}
		$xml->pop();
		print $xml->getXml();
}
?>