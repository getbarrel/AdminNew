<?

include("../class/layout.work.class");
include("work.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../class/calender.big.class");


$db = new Database;
WorkConfigSetting($db);
$sql = 	"SELECT bdiv.*
			FROM work_group bdiv 
			where company_id ='".$admininfo["company_id"]."' and is_project = '1' and disp = 1
			group by group_ix order by regdate desc";
	
$db->query($sql);
$total = $db->total;
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


$Contents = "
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("업무 관리", "업무관리 > 프로젝트 목록 ")."
	
	</td>
  </tr>
</table>

";

$Contents .= "
<table width=100% cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align='left' colspan=4 style='padding:0px 0px 0px 0px;'> 
	    	".WorkTab($total)."
	    </td>
	</tr>
	<tr>
		<td colspan=2>
		<div id='result_area' style='display:inline;width:100%;float:left;padding:0px;'>";

	$inner_view .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff'  align='left' >";


	$projects = $db->fetchall();
	//print_r($projects);
	
	for ($i = 0; $i < count($projects); $i++)
	{
	
		if($projects[$i][group_depth] == 2){
			$db->query("SELECT group_name FROM work_group WHERE group_ix  = '".$projects[$i][parent_group_ix]."' ");
			$db->fetch(0);
			$group_name = $db->dt[group_name]." > ".$projects[$i][group_name];
		}else{
			$group_name = $projects[$i][group_name];
		}

		$sql = "SELECT IFNULL(sum(case when status != '' then 1 else 0 end),0) as work_cnt,
				IFNULL(sum(case when status = 'WC' then 1 else 0 end),0) as work_complete_cnt,
				IFNULL(sum(case when status != 'WC' and dday < ".date("Ymd")." then 1 else 0 end),0) as work_noncomplete_cnt,
				IFNULL(sum(case when status != 'WC' and ".date("Ymd")." between sdate and dday then 1 else 0 end),0) as work_ready_cnt,
				IFNULL(sum(case when status = 'WI' then 1 else 0 end),0) as work_ing_cnt
				FROM work_list wl , work_group wg, common_member_detail cmd
				where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
				and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code  and (wl.charger_ix = '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))
				and (wl.group_ix =  '".$projects[$i][group_ix]."' )  			
				";	
		//echo nl2br($sql);
		$db->query($sql);
		$db->fetch();

		$inner_view .= "
		
			<tr>
				
				<td style='padding:0px'>
				<div style='border:1px solid silver;padding:10px;margin-bottom:5px;'>
				<table width='90%' border='0' cellpadding=0 cellspacing=0 >
				<tr>
				<td rowspan=3 width=90><img src='../images/project_file.png' align=absmiddle height='50' border=0></td>
				<td colspan=3><div class=small style='font-size:12px;font-weight:bold;'>
					<table cellpadding=0 cellspacing=0><tr><td class=blk>".$group_name."</td><td style='padding-left:10px;'><a href=\"#\" onclick=\"javascript:full_popup('work_project_architecture.php?list_type=search&group_ix=".$projects[$i][group_ix]."&mmode=pop');\"><img src='../images/orange/ico_project_view.gif' border=0  title='프로젝트보기' style='margin-right:5px;'></a></td></tr></table></div></td></tr>
				  <tr height='25' bgcolor='#ffffff'  >   
					<td align='left' style='padding:0px 0 0px 0px;line-height:130%'>
						<div style='padding:0 0 0 0px;' class=blk>
						".ChangeDate($projects[$i][project_sdate],"Y년 m월 d일")." ~ ".ChangeDate($projects[$i][project_edate],"Y년 m월 d일")." <br>		
						<a href=\"work_view.php?mmode=&wl_ix=".$projects[$i][wl_ix]."\" class='".($projects[$i][dday] < date("Ymd") && $projects[$i][status] != "WC"? "gray":"")."' >
						
						</a>
						<div style='color:gray;'>".nl2br($projects[$i][project_detail])."</div>
						</div>
					</td>
					<td align='center' nowrap>".$projects[$i][charger]."</td>
				</tr>
				<tr>
					<td colspan=3>
					<table cellpadding=0 cellspacing=0>
					<tr>
						<td colspan=2><a href='work_list.php?&group_ix=".$projects[$i][group_ix]."'><span class=blk>할당된 업무 :</span></a></td>
						<td><a href='work_list.php?group_ix=".$projects[$i][group_ix]."'>".$db->dt[work_cnt]." 개</a></td><td width='10'></td>	
						<td colspan=2><span class=blk><a href='work_list.php?list_type=myjob&group_ix=".$projects[$i][group_ix]."'>진행중</a>/<a href='work_list.php?list_type=search&work_status=WR&charger_ix=".$admininfo[charger_ix]."&group_ix=".$projects[$i][group_ix]."'>대기 업무</a> : </span></td>
						<td><a href='work_list.php?list_type=myjob&group_ix=".$projects[$i][group_ix]."&group_ix=".$projects[$i][group_ix]."'>".$db->dt[work_ing_cnt]." 개</a>/<a href='work_list.php?list_type=search&work_status=WR&charger_ix=".$admininfo[charger_ix]."&group_ix=".$projects[$i][group_ix]."'>".$db->dt[work_ready_cnt]." 개</a></td><td width='10'></td>	
						<td colspan=2><a href='work_list.php?list_type=before&group_ix=".$projects[$i][group_ix]."'><span class=blk>미처리 업무 : </span></a></td>
						<td><a href='work_list.php?list_type=before&group_ix=".$projects[$i][group_ix]."'>".$db->dt[work_noncomplete_cnt]." 개</a></td><td width='10'></td>
						<td colspan=2><a href='work_list.php?list_type=search&work_status=WC&group_ix=".$projects[$i][group_ix]."'><span class=blk>완료된 업무 : </span></a></td>
						<td><a href='work_list.php?list_type=search&work_status=WC&group_ix=".$projects[$i][group_ix]."'>".$db->dt[work_complete_cnt]." 개</a></td>	
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
		   ";

	}


$inner_view .= "
 
</table>

	
";

$Contents .= $inner_view."	</div>
		</td>
	</tr>
</table>


";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	$P->OnloadFunction = "";
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
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 프로젝트 목록";
	$P->title = "프로젝트 목록";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	$P->prototype_use = false;
	echo $P->PrintLayOut();
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