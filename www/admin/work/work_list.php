<?
include("../class/layout.work.class");
include("work.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../class/calender.big.class");
$script_time[start] = time();
//print_r($admininfo);
//auth(9);
//print_r($admininfo["work_confs"]);
//phpinfo();

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
WorkConfigSetting($mdb);
if($admininfo["charger_id"] == "sigi1074"){
	//$db->debug = true;
	//$mdb->debug = true;
}

$list_view_type = $_GET["list_view_type"];
if($list_view_type == ""){
//	$list_view_type = "calendar";
}
//print_r($_GET);
if ($sdate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));
	$firstdayofweek = mktime(0, 0, 0, date("m")  , date("d")-date("w"), date("Y"));

//	$sDate = date("Y/m/d");
	//$sdate = date("Ymd", $firstdayofweek);
//	$edate = date("Ymd");

	$startDate = date("Y/m/d", $firstdayofweek);
	$endDate = date("Y/m/d");
}else{
	$startDate = $FromYY."/".$FromMM."/".$FromDD;
	$endDate = $ToYY."/".$ToMM."/".$ToDD;
	//$sdate = $FromYY.$FromMM.$FromDD;
	//$edate = $ToYY.$ToMM.$ToDD;



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

	$where = " where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != ''
				and wl.group_ix = wg.group_ix
				 ";
	//if(!$parent_group_ix){
	//	$parent_group_ix = $parent_group_ix;
	//}
//	echo "view_complete_job : ".$_COOKIE[view_complete_job];
	if($_COOKIE[view_complete_job] != 1 && $list_type != "search"){
		$where .= " and (wl.status not in ('WC','WD') ) ";
	}

	if(!($_COOKIE[view_project_job] == 1 || $view_project_job == 1)){
		$where .= " and wl.depth = '0'  ";
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
		if($admininfo["work_confs"]["config_view_order1"] && $admininfo["work_confs"]["config_view_order2"]){
			if($admininfo["work_confs"]["config_view_order_type1"] && $admininfo["work_confs"]["config_view_order_type2"]){

					$orderby_string = " order by ".$admininfo["work_confs"]["config_view_order1"]." ".$admininfo["work_confs"]["config_view_order_type1"].", ".$admininfo["work_confs"]["config_view_order2"]."  ".$admininfo["work_confs"]["config_view_order_type2"]."";

			}else{
				if($admininfo["work_confs"]["config_view_order1"] != "edate" && $admininfo["work_confs"]["config_view_order2"] != "edate"){
					$orderby_string = " order by ".$admininfo["work_confs"]["config_view_order1"]." desc, ".$admininfo["work_confs"]["config_view_order2"]." desc";
				}else{
					$orderby_string = " order by edit_date desc, stime asc ";
				}
			}
		}else{
			$orderby_string = " order by edit_date desc, stime asc ";
		}
	}

	if($list_type == "mydepartment"){
		if(!empty($dp_ix)){
			$where .= " and cmd.department =  '".$dp_ix."' ";
		}else{
			$where .= " and cmd.department =  '".$admininfo[department]."' ";
		}
	}else{
		if($dp_ix != ""){
			$where .= " and (cmd.department =  '".$dp_ix."'  )  ";
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
			$where .= " and (wl.charger_ix =  '".$admininfo[charger_ix]."' ) ";
			$union_where .= " and cr.charger_ix =  '".$admininfo[charger_ix]."' ";
	}else if($list_type == "before"){
			$where .= " and dday < '".date("Ymd")."' and status not in ('WC', 'WD') and wl.charger_ix =  '".$admininfo[charger_ix]."' ";
			$union_where .= " and dday < '".date("Ymd")."' and status not in ('WC', 'WD') and wl.charger_ix =  '".$admininfo[charger_ix]."' ";
	}else if($list_type == "today"){
			$where .= " and  '".date("Ymd")."' between wl.sdate and wl.dday and wl.charger_ix =  '".$admininfo[charger_ix]."' ";
			$union_where .= " and  '".date("Ymd")."' between wl.sdate and wl.dday and wl.charger_ix =  '".$admininfo[charger_ix]."' ";
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
$script_time[aaa] = time();

if($list_view_type != "calendar" && $list_view_type != "weekly"){

	// 전체 갯수 불러오는 부분
		$sql = "SELECT count(*) as total FROM work_list wl, work_group wg, common_member_detail cmd  $where ";
		//echo $sql;
		$db->query($sql);

		$db->fetch();
		$total = $db->dt[total];
		$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&list_type=$list_type&group_ix=$group_ix&dp_ix=$dp_ix&charger_ix=$charger_ix&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");

	$sql = "SELECT  wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, '' as co_charger_ix, count(*) as co_chager_cnt
			FROM work_list wl
			left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
			left join work_group wg on wl.group_ix = wg.group_ix
			left join common_member_detail cmd on wl.charger_ix = cmd.code
			$where and wl.importance = 'E'
			GROUP BY wl.wl_ix ";
	$db->query($sql);
	$script_time[emergency_start] = time();
	$emergency = $db->fetchall();
	$script_time[emergency_end] = time();

	$sql = "SELECT  wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, '' as co_charger_ix, count(*) as co_chager_cnt
			FROM work_list wl
			left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
			left join work_group wg on wl.group_ix = wg.group_ix
			left join common_member_detail cmd on wl.charger_ix = cmd.code
			$where and wl.importance != 'E'
			GROUP BY wl.wl_ix ";
	

	$sql .= "
			union
			SELECT wl.* , wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix as co_charger_ix , (select count(*)  from work_charger_relation wcr where wcr.wl_ix = wl.wl_ix ) as co_chager_cnt
			FROM work_list wl
			left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
			left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd
			$union_where and wl.charger_ix != cmd.code
			GROUP BY wl.wl_ix
			$orderby_string LIMIT $start, $max ";
	//echo nl2br($sql);
	//exit;
	$script_time[group_info_start] = time();
	$db->query($sql);
	$script_time[group_info_end] = time();

	}


$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("업무 관리", "업무관리 > 업무 목록 ")."

	</td>
  </tr>
</table>
<script type='text/javascript' >
var list_view_type = '".$_GET["list_view_type"]."';
var list_type = '".$_GET["list_type"]."';
var parent_group_ix = '".$_GET["parent_group_ix"]."';
var group_ix = '".$_GET["group_ix"]."';
var department = '".$_GET["department"]."';
var charger_ix = '".($_GET["charger_ix"] ? $_GET["charger_ix"]:$admininfo[charger_ix])."';
var ss_charger_ix = '".$admininfo[charger_ix]."';
var dp_ix = '".$_GET["dp_ix"]."';
var sdate = '".$_GET["sdate"]."';
var edate = '".$_GET["edate"]."';
</script>
<style type='text/css'>
	#calendar {
		width: 100%;
		margin: 0 auto;
		padding:10px 10px 0px 10px;
		}
	.layerCon {position:absolute;left:50%;top:50%;z-index:1001;display:none;}
	#layerBg {position:absolute;width:100%;height:100%;left:0;top:0;background:#000000;filter:alpha(opacity=70);opacity:0.7;z-index:1000;display:none;}
</style>
";

if($list_view_type == "calendar"){

	$Contents .= "
	<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td colspan=2 style='padding:0px 0px 0px  0px;'>
		<link rel='stylesheet' type='text/css' href='./fullcalendar-1.5.4/fullcalendar.css' />
		<script type='text/javascript' src='./fullcalendar-1.5.4/fullcalendar.js'></script>
		<script type='text/javascript' src='work.calendar.js'></script>
		<script type='text/javascript'>

		//window.onload = loadCalendar;

		</script>

		<div id='calendar' style='height:1000px;border:0px solid red;padding:10px 0px;'></div>

		<!-- sign_layers start-->
		<div id='sign_in' class='layerCon memlogin png24' style='display:none;'>

			<form action='/global/profile/login.php?act=verify&URL=/global/recipes/list.php?cid=002001000000000&depth=1&login_type=pop' method='post' name='login_frm' onsubmit='return CheckFormValue(this);'>
			<div class='signin'>
				<p class='stit'>Sign In</p>
				<p class='pt_10'>
					User ID <br />
					<input type='text' name='id' validation='true' style='width:227px;height:19px;' title='User ID' class='input_txt' id='id' tabindex=1>
				</p>
				<p class='pt_10'>
					Password <br />
					<input type='password' name='pw' style='width:227px;height:19px;'  validation='true' title='Password'  id='pw' tabindex=2>
				</p>
				<p class='pt_10 des'>
					Forgot your username or password? Click <a href='/global/profile/sign_up.php'>here</a>
				</p>
				<p class='pt_10'>
					<input type=image src='/data/global/templet/global/images/common/btn_signin.gif' alt='Sign In' />
				</p>
			</div>
			</form>
			<div class='close'><a href=\"javascript:openLayer('sign_in');\"><img src='/data/global/templet/global/images/common/btn_login_close.png' alt='close' /></a></div>
		</div>
	</td>
</tr>
</table>
		";

//echo $Contents;
//exit;
}else if($list_type == "weekly"){

	$Script = "<script type='text/javascript' src='work.list.js'></script>
<script type='text/javascript' >
var list_view_type = '".$_GET["list_view_type"]."';
var list_type = '".$_GET["list_type"]."';
var parent_group_ix = '".$_GET["parent_group_ix"]."';
var group_ix = '".$_GET["group_ix"]."';
var department = '".$_GET["department"]."';
var charger_ix = '".($_GET["charger_ix"] ? $_GET["charger_ix"]:$admininfo[charger_ix])."';
var ss_charger_ix = '".$admininfo[charger_ix]."';
var dp_ix = '".$_GET["dp_ix"]."';
var sdate = '".$_GET["sdate"]."';
var edate = '".$_GET["edate"]."';
</script>";

 
	$Contents = "
	<table width=100% cellpadding=0 cellspacing=0 border=0 >
	<tr>
		<td align='left' colspan=4 style='padding:0px 0px 0px 0px;".($mmode == "print" ? "display:none;":"")."'>
	    	".WorkTab($total)."
	    </td>
	</tr>  
	<tr>
		<td id='result_area' align='left' colspan=4 style='padding:10px 0px 0px 0px;'>
		";
		//$works = $db->fetchall();
		//print_r($works);
//print_r($admininfo["work_confs"]["config_week_num"]);
if($weekly_type == "work_group" || true){
	for($week_i=0;$week_i < 2;$week_i++){
		if($vdate == ""){
			$thistime = time();
			$vdate = date("Ymd", time()+86400*((date("w")*-1)+($week_i*7)));
			$aweekago = date("Ymd", time()+86400*((date("w")*-1)+($week_i*7)-7));
			$aweekafter = date("Ymd", time()+86400*((date("w")*-1)+($week_i*7)+7));
			
		}else{
			//if($week_i == 1){
			//	$thistime = mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+($week_i*7),substr($vdate,0,4));//+86400*((date("w")*-1)+($week_i*7))
			//}else{
				$thistime = mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+86400*(($week_i*7));//+86400*((date("w")*-1)+($week_i*7))
			//}
			//if($week_i== 1){
				$vdate = date("Ymd", $thistime);
			//}
			$aweekago = date("Ymd", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+86400*((date("w",$thistime)*-1)+($week_i*7)-7));
			$aweekafter = date("Ymd", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+86400*((date("w",$thistime)*-1)+($week_i*7)+7));
			

		}
		
			//echo $vdate."::::".($week_i*7)."<br>";
		if($mmode == "print"){
			if($week_i == 0){
				$inner_view .= "<div class=blk style='float:left;text-align:left;color:#000000;padding:5px 5px 5px 5px;margin-top:5px;width:100px'><b>금주 주간계획</b></div>";
			}else{
				$inner_view .= "<div class=blk style='float:left;text-align:left;color:#000000;padding:5px 5px 5px 5px;margin-top:15px;width:100px'><b>차주 주간계획</b></div>";
			}

		}else{
			if($week_i == 0){
				$inner_view .= "<div class=blk style='float:left;text-align:center;background-color:#000000;color:#ffffff;padding:5px 5px 5px 5px;margin-top:5px;width:180px'><b>금주 주간계획</b></div>";
			}else{
				$inner_view .= "<div class=blk style='float:left;text-align:center;background-color:#000000;color:#ffffff;padding:5px 5px 5px 5px;margin-top:15px;width:180px'><b>차주 주간계획</b></div>";
			}
		}
		if($mmode == "print"){
			$inner_view .= "<div class=blk style='float:left;text-align:left;width:380px;margin-top:".($week_i == 0 ? "10px;":"20px;")."'>
			(".getNameOfWeekdayForWork(0,$vdate)." ~ ".getNameOfWeekdayForWork(6,$vdate)." )
			</div>";

		}else{
			$inner_view .= "<div class=blk style='float:left;text-align:center;width:380px;margin-top:".($week_i == 0 ? "10px;":"20px;")."'>
			<b><a href='?list_type=weekly&list_view_type=weekly&vdate=".$aweekago."'><img src='/admin/v3/images/btns//sub_arrowbox_left.png'></a> 
			".getNameOfWeekdayForWork(0,$vdate)." ~ ".getNameOfWeekdayForWork(6,$vdate)." 
			<a href='?list_type=weekly&list_view_type=weekly&vdate=".$aweekafter."'><img src='/admin/v3/images/btns//sub_arrowbox_right.png'></a></b>
			</div>";
		}
		$inner_view .= "
		<div style='float:right;'>";
		if($week_i == 0 && $mmode != "print"){
			$inner_view .= "<img src='../images/korea/btn_print.gif' onclick=\"PopSWindow('work_list.php?".$_SERVER["QUERY_STRING"]."&mmode=print',1150,500,'work_print');\" style='margin-right:3px;cursor:pointer;' align='absmiddle'/>";
		}
		$inner_view .= "
		</div>";

		$inner_view .= "
						<table width=100% cellpadding=0 cellspacing=0 border=0 class='list_table_box' style='table-layout:fixed;'>
							
							<tr height=24>";
				
			$inner_view .= "<td align=center class='s_td'>업무그룹</td>";
			
			
			for($i=0;$i < 7;$i++){
				$week_str =  strtoupper(date("D", $thistime+86400*($i-date("w",$thistime)+($week_i*7))));
				//echo $week_str."<br>";
				if(in_array($week_str,$admininfo["work_confs"]["config_week_num"])){
			$inner_view .= "<td align=center class='s_td' >".getNameOfWeekdayForWork($i,$vdate)."</td>";
				}
			}
			$inner_view .= "
							</tr>";



		//$start_date = date("Ymd", $thistime+86400*(-date("w",$thistime)));
		//$end_date = date("Ymd", $thistime+86400*(6-date("w",$thistime)));

		$start_date = getNameOfWeekdayForWork(0,$vdate,"Ymd");//date("Ymd", $thistime+86400*(-date("w",$thistime)));
		$end_date = getNameOfWeekdayForWork(6,$vdate,"Ymd");//date("Ymd", $thistime+86400*(6-date("w",$thistime)));

		//echo $vdate."::".$thistime.":::".$start_date.":::".$end_date.":::".date("w",$thistime)."<br>";

		if($_COOKIE["dynatree-work_group-select"]){
			$group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
			$union_group_ix_str = " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
		}else{
			if($group_ix != ""){
				$group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
				$union_group_ix_str = " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
			}
			if(!$_COOKIE["dynatree-user-select"]){
				$group_ix_str .= " and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' ";
				$union_group_ix_str .= "and  cr.charger_ix = '".$_SESSION['admininfo']['charger_ix']."' ";
			}
		}

		if($_COOKIE[view_complete_job] != '1' && $list_type != "search"){
			$status_where = " and (wl.status not in ('WC','WD') ) ";
			$status_union_where = " and (wl.status not in ('WC','WD') ) ";
		}

		if($charger_ix != "" && false){
			//echo "aaa";
			$charger_ix_where .= " and (wl.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
			$charger_ix_union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$charger_ix)."'))  ";
		}else if($_COOKIE["dynatree-user-select"]){
			$charger_ix_where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
			$charger_ix_union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))";
		}else{
			$charger_ix_where .= " and (wl.charger_ix = '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))";
			$charger_ix_union_where .= " and (cr.charger_ix = '".$admininfo[charger_ix]."')";
		}

		//if($group_infos != ""){
				$sql = "SELECT wl.group_ix , wg.parent_group_ix, wg.group_name, wg.group_depth 
							FROM work_list wl, work_group wg, common_member_detail cmd  
							where wl.charger_ix = cmd.code and wl.group_ix = wg.group_ix 
							and (sdate between ".$start_date." and ".$end_date." or dday between ".$start_date." and ".$end_date.") 
							".$group_ix_str." ".$charger_ix_where." ".$status_where."
							group by wl.group_ix
							 ";
				$sql .= "union 
							SELECT wl.group_ix , wg.parent_group_ix, wg.group_name , wg.group_depth
							FROM work_list wl 
							left join work_charger_relation cr on wl.wl_ix = cr.wl_ix
							left join work_group wg on wl.group_ix = wg.group_ix , common_member_detail cmd  
							where  (sdate between ".$start_date." and ".$end_date." or dday between ".$start_date." and ".$end_date.") 
							and wl.charger_ix != cmd.code ".$union_group_ix_str." ".$charger_ix_union_where." ".$status_union_where."
							group by wl.group_ix  limit 20 ";
				
				//echo nl2br($sql);
				$script_time["weekly_group_start_".$week_i] = time();
				$db->query($sql);
				$group_infos = $db->fetchall();
				$script_time["weekly_group_end_".$week_i] = time();
				if(count($group_infos) == 0){
					$sql = "SELECT wl.group_ix , wg.parent_group_ix, wg.group_name, wg.group_depth, count(*) as total 
							FROM work_list wl, work_group wg, common_member_detail cmd  			
							where wl.charger_ix = cmd.code and wl.group_ix = wg.group_ix 
							and wl.charger_ix =  '".$admininfo[charger_ix]."' 
							group by wl.group_ix  limit 10 ";
					//echo $sql;
					$db->query($sql);
					$group_infos = $db->fetchall();
				}
		//}
		//print_r($group_infos);
		//DateBySchedulePriod
		
		$week_schedules = DateBySchedulePriod($start_date,$end_date,"all");//,$group_infos[$x][group_ix]
		//print_r($week_schedules);
		for($x=0;$x < count($week_schedules);$x++){
			//echo $week_schedules[$x][sdate].":::".$week_schedules[$x][dday].":::".date_diff(ChangeDate($week_schedules[$x][sdate],"Y-m-d H:i:s"),ChangeDate($week_schedules[$x][dday],"Y-m-d H:i:s"))."<br>";
			$priod = date_diff2(ChangeDate($week_schedules[$x][sdate],"Y-m-d H:i:s"),ChangeDate($week_schedules[$x][dday],"Y-m-d H:i:s"));

			for($y=0; $y <= $priod;$y++){
				$work_date = date("Ymd", mktime(0,0,0,substr($week_schedules[$x][sdate],4,2),substr($week_schedules[$x][sdate],6,2),substr($week_schedules[$x][sdate],0,4))+86400*$y);
				$__week_schedules[$week_schedules[$x][group_ix]][$work_date][] = $week_schedules[$x];
			}
		}
		//print_r($__week_schedules[11][20130226]);

		for($x=0;$x < count($group_infos);$x++){
			$inner_view .= "
							<tr height=100>";
				if($group_infos[$x][group_depth] == 2){
					$db->query("SELECT group_name FROM work_group WHERE group_ix  = '".$group_infos[$x][parent_group_ix]."' ");
					$db->fetch(0);
					$group_name = $db->dt[group_name]." > ".$group_infos[$x][group_name];
				}else{
					$group_name = $group_infos[$x][group_name];
				}

				$inner_view .= "<td align=left class='s_td' style='text-align:left;padding-left:10px;' >".$group_name."</td>";
			for($i=0;$i < 7;$i++){
				$week_str =  strtoupper(date("D", $thistime+86400*($i-date("w",$thistime))));
				//$vdate_str =  strtoupper(date("Ymd", time()+86400*($i-date("w"))));
				//echo $i.":::".$vdate_str.":::".$week_str.":::".($i-date("w"))."<br>";
				if(in_array($week_str,$admininfo["work_confs"]["config_week_num"])){	
					if($_GET["vdate"] == ""){
						//$_vdate = date("Ymd", $thistime+86400*($i-date("w",$thistime)+($week_i*7)));
						$_vdate = date("Ymd", $thistime+86400*($i-date("w",$thistime)));
					}else{
						$_vdate = date("Ymd", $thistime+86400*$i);
					}

					$inner_view .= "<td align=center title='".$_vdate."' style='vertical-align:top;cursor:pointer;".($_vdate == date("Ymd") ? "padding:0px;":"")."padding-bottom:20px;' class= '".($_vdate == date("Ymd") ? "point":"")."' ondblclick=\"PopSWindow('work_add.php?mmode=weelky_pop&group_ix=".$group_infos[$x][group_ix]."&is_schedule=0&sdate=".$_vdate."&dday=".$_vdate."&stime=00:00&dtime=00:00',900,750,'work_add_".$group_infos[$x][group_ix]."');\">";

					//echo date("Ymd", time()+86400*($i-date("w")))."<br>";
					
					//echo $_vdate."<br>";
					//print_r($__week_schedules[$group_infos[$x][group_ix]][_vdate]);
					
					$schedules = $__week_schedules[$group_infos[$x][group_ix]][$_vdate];//DateBySchedule($_vdate,"all",$group_infos[$x][group_ix]);
					//$schedules = DateBySchedule($_vdate,"all",$group_infos[$x][group_ix]);
					
					//echo count($schedules)."<br>";

						for($j=0;$j < count($schedules);$j++){
							//print_r($schedules[$j]);
							//echo date("D", $j);
							if($schedules[$j][status] != "WC"){
								if($mmode != "print"){
									$status_color = "background-color:#efefef;";
								}
								if($_vdate < date("Ymd")){
									$status_font_color = "color:red;";
								}else{
									$status_font_color = "";
								}
							}else{
								$status_color = "";
								$status_font_color = "";
							}

							if($schedules[$j][status] == "WR"){
								$status_text = "<span class='small blk' style='".$status_font_color."'>[작업대기]</span>";
							}else if($schedules[$j][status] == "WC"){
								$status_text = "<span class='small blk' style='".$status_font_color."'>[작업완료]</span>";
							}else{
								$status_text = "";
							}
							
							$inner_view .= "<!--a href=\"../work/work_view.php?mmode=&wl_ix=".$schedules[$j][wl_ix]."\" align=absmiddle-->
												<!--a href=\"#\" align=absmiddle-->
												<table border=0 style='margin:3px;".$status_color."".$status_font_color."' width=98% id='work_".$week_i."_".$i."_".$j."' onmouseover=\"$(this).css('border','1px solid gray')\" onmouseout=\"$(this).css('border','1px solid #ffffff')\" onclick=\"PopSWindow('work_view.php?mmode=weelky_pop&wl_ix=".$schedules[$j][wl_ix]."&group_ix=".$schedules[$j][group_ix]."',900,750,'work_add_".$schedules[$j][wl_ix]."');\" >
												<col width='75px'>
												<col width='*'>
												<tr height='13px' style='".$status_color.";'>
													<td align='left' style='padding:4px 5px 0 4px;font-weight:normal;".$status_color."".$status_font_color."' class=small >".$schedules[$j][stime]."~".$schedules[$j][dtime]." ".$status_text."<span style='color:#000000;'>".$schedules[$j][name]."</span> </td>
												</tr>
												<tr height='18px' style='".$status_color."'>
													<td align=left style='padding:2px 0px 4px 4px;".$status_color."'>";
												if($_GET["mmode"] != "print"){
												$inner_view .= "
													".($schedules[$j][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
													".($schedules[$j][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
													".($schedules[$j][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."";
												}
												$inner_view .= "
													<span class='helpcloud'  help_html='".$schedules[$j][stime]."~".$schedules[$j][dtime]." ".strip_tags($status_text)." <br>
													".$schedules[$j][work_title]." '>".Cut_Str($schedules[$j][work_title],25)."</span>
													</td>
												</tr>
												</table>
												<!--/a-->";
							
						}
					$inner_view .= "</td>";
				}
			}
			$inner_view .= "
							</tr>";
		}
			$inner_view .= "
						</table><P CLASS=\"breakhere\"/>";
		
		unset($__week_schedules);
	}

	if(true){
	$inner_view .= "<table width=100% cellpadding=0 cellspacing=0 border=0 ><tr><td style='padding-top:10px;'>".LiveIssue("print")."</td></tr></table>";
	}
}



$inner_view .= "
	    </td>
	</tr>";

$inner_view .= "
</table>	";
$Contents = $Contents.$inner_view;
}else{


$Script = "
<script  id='dynamic'></script>
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<link type='text/css' href='./js/themes/demos.css' rel='stylesheet' />
<script type='text/javascript' src='./js/jquery.fadeSliderToggle.js'></script>
<script type='text/javascript' src='work.list.js'></script>
<script type='text/javascript'>
/*
$(document).ready(function() {
	//alert($('#result_area').droppable());
	$('DIV#external-event').draggable(); // 끄는 동안만 불투명도 주기
	$('#result_area').droppable({
		accept: 'DIV',
		drop: function(event, ui) {
			$(this).append('끌어서놓기 됨');
		}
	});
});
*/


</script>

 <script language='javascript'>

function init(){

	var frm = document.searchmember;

	";
if($regdate != "1"){
$Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}
if($dday != "1"){
$Script .= "
	frm.dday_sdate.disabled = true;
	frm.dday_edate.disabled = true;";
}
$Script .= "

}


</script>";

$Contents .= "

<style>
	P.breakhere {page-break-before: always}
	.fader{opacity:0;display:none;height:27px;}
</style>
<table width=100% cellpadding=0 cellspacing=0 border=0 >
	<tr>
		<td align='left' colspan=4 style='padding:0px 0px 0px 0px;'>
	    	".WorkTab($total)."
	    </td>
	</tr>
	<tr>
		<td style='padding:0 0 0 0;' >";
/*
if($list_type == "mydepartment"){
	$departments = makeDepartmentSelectBox($mdb,"department",$department,"array","부서", "onchange=\"document.location.href='?mmode=$mmode&list_type=mydepartment&department='+this.value\"");
	//print_r($departments);
	for($i=0;$i < count($departments);$i++){
	$Contents .= "<a href='?mmode=".$mmode."&list_type_type=".$list_type_type."&list_type=".$list_type."&dp_ix=".$departments[$i][dp_ix]."' style='padding:0 7px;".($departments[$i][dp_ix] == $dp_ix ? "font-weight:bold;":"")."'>".$departments[$i][dp_name]."</a>";
	}
}else if($list_type == "myjob" || $list_type == "today" || $list_type == "before" || $list_type == ""){
	$groups = getWorkGroupInfoSelect('parent_group_ix', '1 차그룹',$db->dt[parent_group_ix], $group_ix, "array", 1," onChange=\"loadWorkGroup(this,'group_ix')\" ");

	$Contents .= "<a href='?mmode=".$mmode."&list_type_type=".$list_type_type."&list_type=".$list_type."' style='padding:0 7px;".($group_ix == "" ? "font-weight:bold;":"")."'>전체</a>";
	for($i=0;$i < count($groups);$i++){
	$Contents .= "<a href='?mmode=".$mmode."&list_type_type=".$list_type_type."&list_type=".$list_type."&group_ix=".$groups[$i][group_ix]."' style='padding:0 7px;".($groups[$i][group_ix] == $group_ix ? "font-weight:bold;":"")."'>".$groups[$i][group_name]."</a>";
	}
}
*/
$Contents .= "
		</td>
		<td style='text-align:right;'>
		<!--a href='?mmode=".$mmode."&list_view_type=calendar&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&dp_ix=".$dp_ix."&charger_ix=".$charger_ix."&department=".$department."'>달력</a> | <a href='?mmode=".$mmode."&list_view_type=list&list_type=".$list_type."&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&dp_ix=".$dp_ix."&charger_ix=".$charger_ix."'>리스트</a--></td>
	</tr>
	<tr>
		<td colspan=2>
		<div  ".($list_type == "search" ? "style='display:block;'":"style='display:none;'").">
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
		<input type='hidden' name=act value='".$act."'>
		<input type='hidden' name=list_type value='".$list_type."'>
		<input type='hidden' name=list_view_type value='".$list_view_type."'>
		<input type='hidden' name=SelectReport value='".$SelectReport."'>
		<input type='hidden' name=vdate value='".$vdate."'>
		<table width=100% cellpadding=0 cellspacing=0>
			<tr>
				<td >";

		$Contents .= SearchBox();

		$Contents .= "
			</td>
		  </tr>
		  <tr height=50>
				<td style='padding:10 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  > <!--a href=\"javascript:mybox.service('addressbook_add.php?code_ix=','10','450','600', 4, [], Prototype.emptyFunction, [], 'HOME > 회원관리 > 업무대상추가');\">업무 대상추가</a--></td>
			</tr>
		</table><br>
		</form>
		</div>
		</td>
	</tr>


<tr>
	<td colspan=2>
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='kt_sms.act.php' target='act' style='display:inline;'><!---->
<input type='hidden' name='wl_ix[]' id='wl_ix'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<div id='result_area' style='display:inline;width:100%;float:left;'>";

$inner_view .= "
<table width='100%' border='0' cellpadding='0' cellspacing='1' class='list_table_box'  align='left' id='work_table' >
  <col width=4%'>
  <col width='8%'>
  <col width='8%'>
  <col width='*'>
  <col width='14%'>
  <col width='11%'>
  <col width='12%'>
  <tbody>
  <tr height='30' align=center>
    <td class='s_td' style='text-align:center;padding:0px;'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td class='m_td'><font color='#000000'><b>시작일</b></font></td>
	<td class='m_td'><font color='#000000'><b><a href='?mmode=".$mmode."&list_view_type=".$list_view_type."&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&charger_ix=".$charger_ix."&dp_ix=".$dp_ix."&orderby=dday&ordertype=".($ordertype == "desc" ? "asc":"desc")."'>기한</a></b></font></td>

    <td class='m_td'><font color='#000000'><font color='#000000'><b><a href='?mmode=".$mmode."&list_view_type=".$list_view_type."&list_type=".$list_type."&parent_group_ix=".$parent_group_ix."&group_ix=".$group_ix."&charger_ix=".$charger_ix."&dp_ix=".$dp_ix."&orderby=group_name&ordertype=".($ordertype == "desc" ? "asc":"desc")."'>업무구분</a></b></font>/<b>업무</b></font></td>
    <td class='m_td'><font color='#000000'><b>상태</b></font></td>
    <td class='m_td'><font color='#000000'><b>담당자</b></font></td>

    <td class='e_td'><font color='#000000'><b>관리</b></font></td>
  </tr>";

for ($i = 0; $i < count($emergency) ; $i++)
	{
		//$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		/*
		if ($db->dt[status] == "")	{ $status_str = ""; }
		if ($db->dt[status] == "WR")	{ $status_str = "등록/처리대기"; }
		if ($db->dt[status] == "WI")	{ $status_str = "진행중"; }
		if ($db->dt[status] == "WC")	{ $status_str = "작업완료"; }
		if ($db->dt[status] == "IS")	{ $status_str = "이슈"; }
		if ($db->dt[status] == "WH")	{ $status_str = "작업보류"; }
		*/
		if($emergency[$i][group_depth] == 2){
			$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$emergency[$i][parent_group_ix]."' ");
			$mdb->fetch(0);
			$group_name = $mdb->dt[group_name]." > ".$emergency[$i][group_name];
		}else{
			$group_name = $emergency[$i][group_name];
		}

		if($emergency[$i][sdate] <= date("Ymd") && date("Ymd") <= $emergency[$i][dday]){
			$work_title_css = "color:#383d41;";
		}else{
			$work_title_css = "color:gray;";
		}
		if($emergency[$i][dday] < date("Ymd") && $emergency[$i][status] != "WC"){
			$work_title_css = "color:red;";
		}

		if($emergency[$i][importance] == "H"){
			$work_title_css .= "font-weight:bold;";
		}else{
			$work_title_css .= "font-weight:normal;";
		}

		if($emergency[$i][sdate] == $emergency[$i][dday] && $emergency[$i][stime] == $emergency[$i][dtime]){
			$date_str = ChangeDate($emergency[$i][sdate],"Y-m-d")."  ".($emergency[$i][stime] == "00:00" ? "":$emergency[$i][stime] )."";
		}else{
			$date_str = ChangeDate($emergency[$i][sdate],"Y-m-d")."  ".(($emergency[$i][stime] == "00:00" && $emergency[$i][dtime] == "00:00") ? "":$emergency[$i][stime] )."<br>";
			$date_str .= ChangeDate($emergency[$i][dday],"Y-m-d")."  ".(($emergency[$i][stime] == "00:00" && $emergency[$i][dtime] == "00:00") ? "":$emergency[$i][dtime] )."";
		}

$inner_view .= "
  <tr height='33' align='center' id='row_".$emergency[$i][wl_ix]."' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor='#ffffff'\">
    <td class='list_box_td list_bg_gray' ><input type=checkbox name=wl_ix[] id='wl_ix' value='".$emergency[$i][wl_ix]."'></td>
    <td class='list_box_td' colspan=2 style='".$work_title_css."' >
		".$date_str."
	</td>
    <td class='list_box_td point'  align='left' style='padding:5px;line-height:160%;vertical-align:middle;text-align:left' onmouseover=\"$('IMG#magnifier_".$emergency[$i][wl_ix]."').show()\" onmouseout=\"$('IMG#magnifier_".$emergency[$i][wl_ix]."').hide()\">
		<div class=small style='font-weight:normal;vertical-align:middle;".$work_title_css."'>".$group_name." ".($emergency[$i][depth] != 0 ? "<a href=\"work_project_architecture.php?group_ix=".$emergency[$i][group_ix]."&wl_ix=".$emergency[$i][wl_ix]."&mmode=pop\" target=_blank><img src='../images/orange/ico_project_view.gif' border=0  title='프로젝트보기' style='margin-right:5px;'></a>":"")."</div>
		<div style='padding:3px 5px 0 0 ;height:20px;display:inline;".$work_title_css."' id='work_title_".$emergency[$i][wl_ix]."'  desc='".nl2br(str_replace("'","",$emergency[$i][work_detail]))."' >
		<a href=\"work_view.php?mmode=&wl_ix=".$emergency[$i][wl_ix]."\" style='".$work_title_css."' align=absmiddle>
		<b>".$emergency[$i][work_title]."</b>
		</a>
		</div>
		<table cellpadding=0 cellspacing=0 style='margin:3px 0px 3px 0px '>
			<tr>
			<td>".($emergency[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=absmiddle title='비밀글' style='margin-right:5px;'> ":"")."</td>
			<td>".($emergency[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄' style='margin-right:5px;'> ":"")."</td>
			<td>".($emergency[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무' style='margin-right:5px;'> ":"")."</td>
			<td style='padding-right:5px;'><img src='../images/orange/ico_comment.gif' border=0 align=absmiddle title='컴멘트'> (".$emergency[$i][comment_cnt].") </td>
			<td style='padding-right:5px;'><img src='../images/orange/ico_report.gif' border=0 align=absmiddle title='보고서'>  (".$emergency[$i][report_cnt].")</td>
			<td><img src='../images/orange/ico_magnifier.gif' border=0 align=absmiddle id='magnifier_".$emergency[$i][wl_ix]."' title='확대보기' onclick=\"ViewContents('".$emergency[$i][wl_ix]."','over')\" style='margin:0 0 0 3px;display:none;cursor:pointer'></td>
			</tr>
		</table>
	</td>
	<td class='list_box_td' style='text-align:center;padding:0px 0px 0px 10px;'>
		<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
			<col width='".($emergency[$i][complete_rate] == 0 ? 1:$emergency[$i][complete_rate])."%'>
			<col width='".((100-$emergency[$i][complete_rate]) == 100 ? 99:(100-$emergency[$i][complete_rate]))."%'>
			<tr height=8><td bgcolor='#ff7200' id='graph_".$emergency[$i][wl_ix]."'></td><td></td></tr>
		</table>
		<div  style='cursor:pointer;padding:0px 0px 3px 10px;text-align:left;'>
			<div id='work_status_text_".$emergency[$i][wl_ix]."' style='z-index:0;padding:0 0 0 5px' onclick=\"ToggleStatusSelect('".$emergency[$i][wl_ix]."')\">".$work_status[$emergency[$i][status]]."(".$emergency[$i][complete_rate]."%)</div>";

$inner_view .= "<div id='quick_complate_rate_".$emergency[$i][wl_ix]."' style='position:absolute;z-index:1100;background-color:#efefef;padding:3px;border:1px solid silver;width:92px;display:none;' >";
		foreach($work_complet_rate  as $key => $value){
			if($key == "100"){
				$status_str = "작업완료".($value)."";
			}else if($key == "0"){
				$status_str = "작업대기".($value)."";
			}else if($key == "-1"){
				$status_str = "작업취소";
			}else{
				$status_str = "작업중".($value)."";
			}
			$inner_view .= "<div onmouseover=\"$(this).css('background-color','#ffffff')\" onmouseout=\"$(this).css('background-color','')\" onclick=\"updateWorkStatus('".$emergency[$i][wl_ix]."','".$key."');\" style='padding:3px;text-align:left;'>".$status_str."</div>";
		}

$inner_view .= "</div>
		</div>
	</td>
	<td class='list_box_td list_bg_gray' >
	<div id='charger_".$emergency[$i][wl_ix]."'>".$emergency[$i][name]." ".($emergency[$i][co_charger_yn] == 'Y' ? "외".$emergency[$i][co_chager_cnt]."명":"") ."</div>
	<div id='s_loading_".$emergency[$i][wl_ix]."' style='display:none;'><img src='/admin/images/indicator.gif' border=0></div>
	</td>

	<td class='list_box_td' align=center valign=middle >";
	if($admininfo[charger_ix] == $emergency[$i][charger_ix] || $admininfo[charger_ix] == $emergency[$i][reg_charger_ix] || $admininfo[charger_ix] == $emergency[$i][co_charger_ix] ){
	$inner_view .= "
		<a href=\"javascript:PopSWindow('work_add.php?mmode=pop&wl_ix=".$emergency[$i][wl_ix]."',800,750,'work_info_".$schedules[$j][wl_ix]."')\"><img src='../image/btc_modify.gif' border=0></a>";
	}else{
	$inner_view .= "-";
	}
	if($admininfo[charger_ix] == $emergency[$i][reg_charger_ix]){
$inner_view .= "
	<a href=\"javascript:DeleteWorkList('".$emergency[$i][wl_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
	}
$inner_view .= "
</td>
  </tr>
  ";

	}

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		/*
		if ($db->dt[status] == "")	{ $status_str = ""; }
		if ($db->dt[status] == "WR")	{ $status_str = "등록/처리대기"; }
		if ($db->dt[status] == "WI")	{ $status_str = "진행중"; }
		if ($db->dt[status] == "WC")	{ $status_str = "작업완료"; }
		if ($db->dt[status] == "IS")	{ $status_str = "이슈"; }
		if ($db->dt[status] == "WH")	{ $status_str = "작업보류"; }
		*/
		if($db->dt[group_depth] == 2){
			$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$db->dt[parent_group_ix]."' ");
			$mdb->fetch(0);
			$group_name = $mdb->dt[group_name]." > ".$db->dt[group_name];
		}else{
			$group_name = $db->dt[group_name];
		}

		if($db->dt[sdate] <= date("Ymd") && date("Ymd") <= $db->dt[dday]){
			$work_title_css = "color:#383d41;";
		}else{
			$work_title_css = "color:gray;";
		}
		if($db->dt[dday] < date("Ymd") && $db->dt[status] != "WC"){
			$work_title_css = "color:red;";
		}

		if($db->dt[importance] == "H"){
			$work_title_css .= "font-weight:bold;";
		}else{
			$work_title_css .= "font-weight:normal;";
		}

		if($db->dt[sdate] == $db->dt[dday] && $db->dt[stime] == $db->dt[dtime]){
			$date_str = ChangeDate($db->dt[sdate],"Y-m-d")."  ".($db->dt[stime] == "00:00" ? "":$db->dt[stime] )."";
		}else{
			$date_str = ChangeDate($db->dt[sdate],"Y-m-d")."  ".(($db->dt[stime] == "00:00" && $db->dt[dtime] == "00:00") ? "":$db->dt[stime] )."<br>";
			$date_str .= ChangeDate($db->dt[dday],"Y-m-d")."  ".(($db->dt[stime] == "00:00" && $db->dt[dtime] == "00:00") ? "":$db->dt[dtime] )."";
		}

$inner_view .= "
  <tr height='33' align='center' id='row_".$db->dt[wl_ix]."' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor='#ffffff'\">
    <td class='list_box_td list_bg_gray' ><input type=checkbox name=wl_ix[] id='wl_ix' value='".$db->dt[wl_ix]."'></td>
    <td class='list_box_td' colspan=2 style='".$work_title_css."' >
		".$date_str."
	</td>
    <td class='list_box_td point'  align='left' style='padding:5px;line-height:160%;vertical-align:middle;text-align:left' onmouseover=\"$('IMG#magnifier_".$db->dt[wl_ix]."').show()\" onmouseout=\"$('IMG#magnifier_".$db->dt[wl_ix]."').hide()\">
		<div class=small style='font-weight:normal;vertical-align:middle;".$work_title_css."'>".$group_name." ".($db->dt[depth] != 0 ? "<a href=\"work_project_architecture.php?group_ix=".$db->dt[group_ix]."&wl_ix=".$db->dt[wl_ix]."&mmode=pop\" target=_blank><img src='../images/orange/ico_project_view.gif' border=0  title='프로젝트보기' style='margin-right:5px;'></a>":"")."</div>
		<div style='padding:3px 5px 0 0 ;height:20px;display:inline;".$work_title_css."' id='work_title_".$db->dt[wl_ix]."'  desc='".nl2br(str_replace("'","",$db->dt[work_detail]))."' >
		<a href=\"work_view.php?mmode=&wl_ix=".$db->dt[wl_ix]."\" style='".$work_title_css."' align=absmiddle>
		<b>".$db->dt[work_title]."</b>
		</a>
		</div>
		<table cellpadding=0 cellspacing=0 style='margin:3px 0px 3px 0px '>
			<tr>
			<td>".($db->dt[is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=absmiddle title='비밀글' style='margin-right:5px;'> ":"")."</td>
			<td>".($db->dt[is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄' style='margin-right:5px;'> ":"")."</td>
			<td>".($db->dt[co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무' style='margin-right:5px;'> ":"")."</td>
			<td style='padding-right:5px;'><img src='../images/orange/ico_comment.gif' border=0 align=absmiddle title='컴멘트'> (".$db->dt[comment_cnt].") </td>
			<td style='padding-right:5px;'><img src='../images/orange/ico_report.gif' border=0 align=absmiddle title='보고서'>  (".$db->dt[report_cnt].")</td>
			<td><img src='../images/orange/ico_magnifier.gif' border=0 align=absmiddle id='magnifier_".$db->dt[wl_ix]."' title='확대보기' onclick=\"ViewContents('".$db->dt[wl_ix]."','over')\" style='margin:0 0 0 3px;display:none;cursor:pointer'></td>
			</tr>
		</table>
	</td>
	<td class='list_box_td' style='text-align:center;padding:0px 0px 0px 10px;'>
		<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
			<col width='".($db->dt[complete_rate] == 0 ? 1:$db->dt[complete_rate])."%'>
			<col width='".((100-$db->dt[complete_rate]) == 100 ? 99:(100-$db->dt[complete_rate]))."%'>
			<tr height=8><td bgcolor='#ff7200' id='graph_".$db->dt[wl_ix]."'></td><td></td></tr>
		</table>
		<div  style='cursor:pointer;padding:0px 0px 3px 10px;text-align:left;'>
			<div id='work_status_text_".$db->dt[wl_ix]."' style='z-index:0;padding:0 0 0 5px' onclick=\"ToggleStatusSelect('".$db->dt[wl_ix]."')\">".$work_status[$db->dt[status]]."(".$db->dt[complete_rate]."%)</div>";

$inner_view .= "<div id='quick_complate_rate_".$db->dt[wl_ix]."' style='position:absolute;z-index:1100;background-color:#efefef;padding:3px;border:1px solid silver;width:92px;display:none;' >";
		foreach($work_complet_rate  as $key => $value){
			if($key == "100"){
				$status_str = "작업완료".($value)."";
			}else if($key == "0"){
				$status_str = "작업대기".($value)."";
			}else if($key == "-1"){
				$status_str = "작업취소";
			}else{
				$status_str = "작업중".($value)."";
			}
			$inner_view .= "<div onmouseover=\"$(this).css('background-color','#ffffff')\" onmouseout=\"$(this).css('background-color','')\" onclick=\"updateWorkStatus('".$db->dt[wl_ix]."','".$key."');\" style='padding:3px;text-align:left;'>".$status_str."</div>";
		}

$inner_view .= "</div>
		</div>
	</td>
	<td class='list_box_td list_bg_gray' >
	<div id='charger_".$db->dt[wl_ix]."'>".$db->dt[name]." ".($db->dt[co_charger_yn] == 'Y' ? "외".$db->dt[co_chager_cnt]."명":"") ."</div>
	<div id='s_loading_".$db->dt[wl_ix]."' style='display:none;'><img src='/admin/images/indicator.gif' border=0></div>
	</td>

	<td class='list_box_td' align=center valign=middle >";
	if($admininfo[charger_ix] == $db->dt[charger_ix] || $admininfo[charger_ix] == $db->dt[reg_charger_ix] || $admininfo[charger_ix] == $db->dt[co_charger_ix] ){
	$inner_view .= "
		<a href=\"javascript:PopSWindow('work_add.php?mmode=pop&wl_ix=".$db->dt[wl_ix]."',900,750,'work_info_".$schedules[$j][wl_ix]."')\"><img src='../image/btc_modify.gif' border=0></a>";
	}else{
	$inner_view .= "-";
	}
	if($admininfo[charger_ix] == $db->dt[reg_charger_ix]){
$inner_view .= "
	<a href=\"javascript:DeleteWorkList('".$db->dt[wl_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
	}
$inner_view .= "
</td>
  </tr>
  ";

	}

if (!$db->total){

$inner_view .= "
  <tr height=350>
    <td colspan='7' class='list_box_td' bgcolor='#ffffff'>등록된 데이타가 없습니다.</td>
  </tr>";

}

$inner_view .= "</table>
<table width='100%' border='0' cellpadding='0' cellspacing='1'  >
  <tr height='40'>
    <td colspan=2 align=left bgcolor='#ffffff' style='padding:5px;'>
	<a href=\"javascript:alert('기능 준비중입니다.');\"><img src='/admin/image/bt_all_del.gif'></a>
    </td>
    <td  colspan='5' align='right' bgcolor='#ffffff' style='padding-right:10px;'>&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
  </tbody>
</table></form>


";
$Contents .= $inner_view."	</div></td>
	</tr>
</table><br><br><br>";
$Contents .= "
<div id='contents_box' style='display:none;vertical-align:top'>
<table class='tooltip' border=0 cellpadding=0 cellspacing=0 style='width:350px;height:0px;display:block;' >
	<col width='6px'>
	<col width='*'>
	<col width='14px'>
	<tr>
		<th class='tooltip_01'></th>
		<td class='tooltip_02' ></td>
		<th class='tooltip_03'></th>
	</tr>

	<tr>
		<th class='tooltip_04' style='vertical-align:top'><div style='position:absolute'><div style='position:relative;z-index:10;left:-14px;'><img src='../images/common/tooltip01/bg-tooltip_04_la.png'></div></div></th>
		<td class='tooltip_05' rowspan=2 valign=top style='padding:5px 5px 0px 5px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >
			<table style='width:100%;margin-bottom:5px;'>
				<col width='30%'>
				<col width='*'>
				<col width='30%'>
				<tr><td><!--img src='../image/btc_del.gif' border=0--></td><td align=center><b style='color:#ffffff;font-size:12px;'>업무 상세보기</b></td><td align=right><img src='../images/orange/btn_close.png' border=0 onclick=\"$('#contents_box').hide();\" style='cursor:pointer;'></td></tr>
			</table>
			<table class='box_12' cellspacing=0 cellpadding=0 style='margin-bottom:5px;width:100%;height:100%;display:block;' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:5px 5px 5px 5px;background-color:#ffffff;color:gray' id='contents_desc'>
					loading...
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
		</td>
		<th class='tooltip_06'></th>
	</tr>
	<tr>
		<th class='tooltip_04'></th>
		<th class='tooltip_06'></th>
	</tr>
	<tr>
		<th class='tooltip_07'></th>
		<td class='tooltip_08'></td>
		<th class='tooltip_09'></th>
	</tr>
</table>
</div>

<div id='contents_box' style='display:none;vertical-align:top'>
<table class='tooltip' border=0 cellpadding=0 cellspacing=0 style='width:350px;height:0px;display:block;' >
	<col width='6px'>
	<col width='*'>
	<col width='14px'>
	<tr>
		<th class='tooltip_01'></th>
		<td class='tooltip_02' ></td>
		<th class='tooltip_03'></th>
	</tr>

	<tr>
		<th class='tooltip_04' style='vertical-align:top'></th>
		<td class='tooltip_05' rowspan=2 valign=top style='padding:5px 5px 0px 5px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >
			asdf
		</td>
		<th class='tooltip_06'></th>
	</tr>
	<tr>
		<th class='tooltip_04'></th>
		<th class='tooltip_06'></th>
	</tr>
	<tr>
		<th class='tooltip_07'></th>
		<td class='tooltip_08'></td>
		<th class='tooltip_09'></th>
	</tr>
</table>
</div>

";
}


//$Contents .= $inner_view;
/*
<!--div>
	<div id='classification8' onmouseover='view8(true)' onmouseout='view4(false)' style=' display:block; position:relative; '>
		<div class='tooltip_200'>
			<div class='top png24'></div>
			<div class='con png24'>이 페이지를 딜리셔스와<br />공유합니다.</div>
			<div class='bot png24'></div>
		</div>
	</div>
</div-->
*/

$script_time[end] = time();
if($admininfo[charger_id] == "sigi1074"){
	//print_r($script_time);
}

$Contents .="<object id='factory' style='display:none' viewastext classid='clsid:1663ed61-23eb-11d2-b92f-008048fdd814'
codebase='http://".$_SERVER["HTTP_HOST"]."/admin/order/scriptx/smsx.cab#Version=7,1,0,60'>
</object>";

$Script .= "

<style type = \"text/css\">
	P.breakhere {page-break-before: always}
</style>
<script language='javascript'>

var initBody ;

function beforePrint() {
	initBody = document.body.innerHTML;
	document.body.innerHTML = document.getElementById('contents_frame').innerHTML;
}

function afterPrint() {
	document.body.innerHTML = initBody;
}

window.onbeforeprint = beforePrint;
window.onafterprint = afterPrint;

function printArea() {
	//window.focus(); window.print();
	//window.print();
	//beforePrint();
	printPage();
}";

if($mmode == "print"){
	$Script .= "
	$(document).ready(function() {
		printArea();
	});";
}

$Script .= "
function printPage() {
		//alert(1);
		factory.printing.header = ''; // Header에 들어갈 문장
		factory.printing.footer = ''; // Footer에 들어갈 문장
		factory.printing.portrait = false // true 면 세로인쇄, false 면 가로인쇄
		factory.printing.leftMargin = 10 // 왼쪽 여백 사이즈
		factory.printing.topMargin = 15 // 위 여백 사이즈
		factory.printing.rightMargin = 10 // 오른쪽 여백 사이즈
		factory.printing.bottomMargin = 15 // 아래 여백 사이즈
		factory.printing.preview();
		//factory.printing.Print(false,window) // 출력하기
	}

</script>
";


if($mmode == "pop" || $mmode == "print"){
	$P = new ManagePopLayOut();

	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.4.min.js'></script>
<script type='text/javascript' src='./js/jquery-ui-1.8.6.custom.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script type='text/javascript' src='work.js'></script>".$Script;
if($list_view_type != "calendar" && $list_view_type != "weekly"){
	$P->OnloadFunction = "init();";
}
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 업무 목록";
	$P->NaviTitle = "업무 목록";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	echo $P->PrintLayOut();

}else if($mmode == "inner_list"){
	echo $inner_view;
}else{
	$P = new LayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-ui-1.8.6.custom.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script type='text/javascript' src='work.js'></script>".$Script;
if($list_view_type != "calendar" && $list_view_type != "weekly"){
	$P->OnloadFunction = "init();";
}else{
	//$P->OnloadFunction = "loadCalendar();";
}
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 업무 목록";
	$P->title = "업무 목록";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	if($list_view_type == "calendar"){
		$P->ContentsDefaultPadding = "1% 0";
	}

	$P->prototype_use = false;
	echo $P->PrintLayOut();
}

function getNameOfWeekdayForWork($WeekNum, $vdate,$type="datename"){
	$WeekName = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일");


	if($type == "datename"){
		return date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$WeekNum)." (".$WeekName[$WeekNum].")";
	}else if($type == "dayname"){
		return date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$WeekNum);//." (".$WeekName[0].")";
	}else if($type == "monthname"){
		return substr(date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),1,substr($vdate,0,4))),0,7)." 월";
	}else if($type == "priodname"){
		return date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))." (".$WeekName[$WeekNum].")";
	}else{
		return date($type,mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$WeekNum);
		//return $WeekName[$WeekNum];
	}
}


function getFirstDIV($mdb, $selected, $object_id='parent_group_ix', $depth=1, $property="disabled"){
	$mdb = new Database;

	$sql = 	"SELECT wg.*
			FROM work_group wg
			where group_depth = 1
			group by group_ix ";
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$object_id' id='$object_id' $property>";
	$mstring .= "<option value=''>1차그룹</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


function SearchBox(){
	global $admininfo, $mdb, $work_status, $charger_ix, $dp_ix;
	global $sdate , $edate, $dday_sdate, $dday_edate, $regdate, $dday;

$mstring .= "

<table class='box_shadow' cellspacing=0 cellpadding=0 style='width:100%;height:100%;display:block;' >
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='width:100%;' >
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='input_table_box'>";

$mstring .= "
		    <tr>
				<td class='input_box_title'>업무 그룹 </td>
				<td class='input_box_item' style='padding-left:5px;'>
				".getWorkGroupInfoSelect('parent_group_ix', '1 차그룹',$parent_group_ix, $parent_group_ix, "select", 1, " onChange=\"loadWorkGroup(this,'group_ix')\" ")."
				".getWorkGroupInfoSelect('group_ix', '2 차그룹',$parent_group_ix, $group_ix, "select", 2)."

				</td>
				<td class='input_box_title'>담당자 : </td>
				<td class='input_box_item' style='padding-left:5px;'>".workCompanyList($admininfo["company_id"])."
					".makeDepartmentSelectBox($mdb,"dp_ix",$dp_ix,"select","부서", "onchange=\"loadWorkUser(this,'charger_ix')\"")."
					".workCompanyUserList($admininfo["company_id"],"charger_ix", $dp_ix, $charger_ix)."
				</td>
			</tr>
		    <tr>
		      <td class='input_box_title'>조건검색 </td>
		      <td class='input_box_item' style='padding-left:5px;' colspan=3>
			  <table cellpadding=0 cellspacing=0>
				<tr>
					<td>
					  <select name=search_type>
								<option value='work_title' ".CompareReturnValue("mobile",$search_type,"selected").">업무내용 + 업무상세</option>
								<option value='charger' ".CompareReturnValue("user_name",$search_type,"selected").">담당자명</option>
					  </select>
					 </td>
					 <td style='padding:0px 3px;'>
					  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:250px' >
					 </td>
				</tr>
			  </table>
		      </td>
		    </tr>
		   <tr>
				<td class='input_box_title'> 업무상태 : </td>
				<td class='input_box_item' colspan=3>
				";
				$mstring .= "<input type='checkbox' name='work_status[]' id='work_status_' value='".($key)."' ><label for='work_status_'>전체업무</label>";

				foreach($work_status  as $key => $value){
					$mstring .= "<input type='checkbox' name='work_status[]' id='work_status_".($key)."' value='".($key)."' ".CompareReturnValue($key,$_GET["work_status"],' checked')."><label for='work_status_".($key)."'>".$value."</label>";
				}

			$mstring .= "
				</td>

			  </tr>
		    ";

$selectd_date = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-86400);
$voneweekago = date("Ymd", time()-86400*7);
$vtwoweekago = date("Ymd", time()-86400*14);
$vfourweekago = date("Ymd", time()-86400*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-1,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-2,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-3,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));

 $mstring .= "
		    <tr height=27>
		      <td class='input_box_title'><label for='regdate'>시작일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td class='input_box_item' colspan=3 style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff>
					<tr>
						<TD width=80 nowrap>
						<input type='text' name='sdate' class='textbox' style='width:80px;text-align:center;' id='start_datepicker' value='$sdate'>
						</TD>
						<TD width=20 align=center> ~ </TD>
						<TD width=80 nowrap>
						<input type='text' name='edate' class='textbox' style='width:80px;text-align:center;' id='end_datepicker' value='$edate'>
						</TD>
						<TD style='padding:4px 0 0 5px'>
							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../image/b_btn_s_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../image/b_btn_s_yesterday.gif'></a>
							<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../image/b_btn_s_1week01.gif'></a>
							<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../image/b_btn_s_15day01.gif'></a>
							<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../image/b_btn_s_1month01.gif'></a>
							<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../image/b_btn_s_2month01.gif'></a>
							<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../image/b_btn_s_3month01.gif'></a>
						</TD>
					</tr>
				</table>
		      </td>
		    </tr>
		    <tr>
		      <td class='input_box_title'><label for='dday'>완료일자</label><input type='checkbox' name='dday' id='dday' value='1' onclick='ChangeDday(document.searchmember);' ".CompareReturnValue("1",$dday,"checked")."></td>
		      <td class='input_box_item' colspan=3  style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff>
					<tr>
						<TD width=80 nowrap>
						<input type='text' name='dday_sdate' class='textbox' style='width:80px;text-align:center;' id='dday_sdate_datepicker' value='$dday_sdate'>
						</TD>
						<TD width=20 align=center> ~ </TD>
						<TD width=80 nowrap>
						<input type='text' name='dday_edate' class='textbox' style='width:80px;text-align:center;' id='dday_edate_datepicker' value='$dday_edate'>
						</TD>
						<TD style='padding:4px 0 0 5px'>
							<a href=\"javascript:select_date('$today','$today',2);\"><img src='../image/b_btn_s_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',2);\"><img src='../image/b_btn_s_yesterday.gif'></a>
							<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../image/b_btn_s_1week01.gif'></a>
							<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../image/b_btn_s_15day01.gif'></a>
							<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../image/b_btn_s_1month01.gif'></a>
							<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../image/b_btn_s_2month01.gif'></a>
							<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../image/b_btn_s_3month01.gif'></a>
						</TD>
					</tr>
				</table>
		      </td>
		    </tr>

		    </table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>

	";

	return $mstring;
}
/*

CREATE TABLE `work_config` (
  `wc_ix` int(8) unsigned NOT NULL auto_increment ,
  `company_id` varchar(32) default '',
  `charger_ix` int(8) unsigned default NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`cr_ix`)
) TYPE=MyISAM DEFAULT CHARSET=utf8


CREATE TABLE `work_charger_relation` (
  `cr_ix` int(8) unsigned NOT NULL auto_increment ,
  `wl_ix` int(8) DEFAULT NULL,
  `charger_ix` varchar(100) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`cr_ix`)
) TYPE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE `work_userinfo` (
  `code` varchar(32) NOT NULL ,
  `google_mail` varchar(100) DEFAULT NULL,
  `google_pass` varchar(100) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`code`)
) TYPE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE IF NOT EXISTS `work_list` (
  `wl_ix` int(8) unsigned NOT NULL auto_increment,
  `group_ix` varchar(30) default '',
  `company_id` varchar(32) default '',
  `charger_ix` int(8) unsigned default NULL,
  `work_title` varchar(50) default NULL,
  `work_detail` mediumtext,
  `status` char(2) default 'WR',
  `complete_rate` int(8) unsigned default '0',
  `is_schedule` enum('1','0') NOT NULL default '0',
  `is_hidden` enum('1','0') default '1',
  `is_report` enum('1','0') default '1',
  `importance` varchar(1) NOT NULL,
  `sdate` varchar(8) NOT NULL,
  `stime` varchar(5) NOT NULL default '00:00',
  `dday` varchar(8) NOT NULL,
  `dtime` varchar(5) NOT NULL default '00:00',
  `reg_name` varchar(20) NOT NULL,
  `reg_charger_ix` int(8) unsigned default NULL,
  `co_charger_yn` enum('Y','N') NOT NULL default 'N',
  `report_cnt` int(4) NOT NULL default '0' COMMENT '보고서 갯수',
  `comment_cnt` int(4) NOT NULL default '0',
  `google_event_id` varchar(255) default NULL COMMENT '구글 캘린더 이벤트 키값',
  `google_updated` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `edit_date` datetime NOT NULL,
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`wl_ix`),
  KEY `regdate` (`regdate`),
  KEY `company_id` (`company_id`,`charger_ix`),
  KEY `group_ix` (`group_ix`),
  KEY `is_schedule` (`is_schedule`),
  KEY `is_hidden` (`is_hidden`),
  KEY `IDX_WL_SDATE` (`sdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2277 ;

*/
?>