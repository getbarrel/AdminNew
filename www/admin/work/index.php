<?

include("../class/layout.work.class");
include("work.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
//include("../class/calender.big.class");



$mdb = new Database;
WorkConfigSetting($mdb);

$Contents = "
<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation(" Dash Board", "업무관리 >  Dash Board ")."
	
	</td>
  </tr>
</table>";

$Contents .= "
<table width=100% cellpadding='0' cellspacing=0 border=0 style='table-layout:fixed;'>
	<col width='50%'>
	<col width='50%'>
	<tr>
		<td align='left' style='vertical-align:top;padding:0px 5px 20px 0px;'> 
	    	".WorkSummary()."
	    </td>
		<td align='left' style='vertical-align:top;padding:0px 0px 20px 5px;'> 
	    	".TodayWeather()."
	    </td>
	</tr>
	<tr>
		<td colspan=2 align='left' style='vertical-align:top;padding:0px 0px 20px 0px;'> 
	    	".MyProject()."
	    </td>
	</tr>
	<tr>
		<td align='left' style='vertical-align:top;padding:0px 5px 20px 0px;'> 
	    	".TodayWork()."
	    </td>
		<td align='left' style='vertical-align:top;padding:0px 0px 20px 5px;'> 
	    	".TodayMyWrok()."
	    </td>
	</tr>
	<tr>
		<td colspan=2 align='left' style='vertical-align:top;padding:0px 0px 20px 0px;'> 
	    	".TodayMySchedule()."
	    </td>
	</tr>
	<tr>
		<td align='left' colspan=2 style='vertical-align:top;padding:0px 5px 20px 0px;'> 
	    	".LiveComment_Main()."
	    </td>
	</tr>
	<tr>
		<td align='left' colspan=2 style='vertical-align:top;padding:0px 5px 20px 0px;'> 
	    	".LiveIssue_Main()."
	    </td>
	</tr>
</table><br><br><br>";

	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > Dash Board";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	$P->TitleBool = false;
	echo $P->PrintLayOut();
	
}else if($mmode == "inner_list"){
	echo $inner_view;
}else{
	$P = new LayOut();
	$P->addScript = "<script type='text/javascript' src='work.js'></script>".$Script;
	
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 >  Dash Board";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	$P->TitleBool = false;
	$P->footer_menu = footMenu()."".footAddContents();
	echo $P->PrintLayOut();
}

function MyProject(){
	global $admininfo;
	$mstring = " 	
		<table width='100%' border='0' cellspacing='0' cellpadding='0' >			
			<col width='25%'>			
			<col width='25%'>		
			<col width='25%'>		
			<col width='25%'>		
			<tr height=25>
				<td colspan=2 ><img src='../images/dot_org.gif' align=absmiddle> <a href='work_project_list.php?mmode=&list_view_type=&list_type=project'><b class='middle_title'>프로젝트</b></a>  </td>
				<td colspan=2 align=right><a href='#'>more</a></td>
			</tr>
			<tr >
				
				";

$sql = 	"SELECT bdiv.*
			FROM work_group bdiv 
			where company_id ='".$admininfo["company_id"]."' and is_project = '1' and disp = 1
			group by group_ix order by regdate desc limit 4 ";
$mdb = new Database;	
$mdb->query($sql);

$projects = $mdb->fetchall();
for ($i = 0; $i < count($projects); $i++)
{
		if($projects[$i][group_depth] == 2 && false){
			$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$projects[$i][parent_group_ix]."' ");
			$mdb->fetch(0);
			//$group_name = $mdb->dt[group_name]." > ".$projects[$i][group_name];
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
		$mdb->query($sql);
		$mdb->fetch();

			$mstring .= "<td height='130' style='border:0px solid silver;".(count($projects) == $i ? "padding-left:3px;":"padding-right:3px;")."'>
					<table width=100%  cellpadding=0 cellspacing=0 style=''  class='input_table_box'>
					<tr height=20>
						<td class='input_box_title'>
						<img src='../images/ico_project.png' align=absmiddle  border=0>
						<a href=\"\" onclick=\"full_popup('work_project_architecture.php?list_type=search&group_ix=".$projects[$i][group_ix]."&mmode=pop');\" >
						<b class=blk>".cut_str($group_name,20)."</b></a>
						</td>
					</tr>
					
					<tr>
					<td class='input_box_item' style='padding:10px;'>
					<table cellpadding=0 cellspacing=0>
					<col width='150px'>
					<col width='*'>";
			if($projects[$i][project_sdate] && $projects[$i][project_edate]){
			$mstring .= "
					<tr height=20>
						<td class='ctr' colspan=2 nowrap><img src='../image/icon_list.gif' border=0 align=bottom ><span class=blk>".ChangeDate($projects[$i][project_sdate],"Y년 m월 d일")." ~ ".ChangeDate($projects[$i][project_edate],"Y년 m월 d일")."</span></td>
					</tr>";
			}

			$mstring .= "
					<tr height=20>
						<td ><img src='../image/icon_list.gif' border=0 align=bottom ><a href='work_list.php?&group_ix=".$projects[$i][group_ix]."&view_project_job=1'><span class=blk>할당된 업무 :</span></a></td>
						<td><a href='work_list.php?group_ix=".$projects[$i][group_ix]."&view_project_job=1'>".$mdb->dt[work_cnt]." 개</a></td><td width='10'></td>	
					</tr>
					<tr height=20>
						<td nowrap><img src='../image/icon_list.gif' border=0 align=bottom ><span class=blk><a href='work_list.php?list_type=myjob&group_ix=".$projects[$i][group_ix]."&view_project_job=1'>진행중</a>/<a href='work_list.php?list_type=search&work_status=WR&charger_ix=".$admininfo[charger_ix]."&group_ix=".$projects[$i][group_ix]."&view_project_job=1'>대기업무</a> : </span></td>
						<td><a href='work_list.php?list_type=myjob&group_ix=".$projects[$i][group_ix]."&group_ix=".$projects[$i][group_ix]."&view_project_job=1'>".$mdb->dt[work_ing_cnt]." 개</a>/<a href='work_list.php?list_type=search&work_status=WR&charger_ix=".$admininfo[charger_ix]."&group_ix=".$projects[$i][group_ix]."&view_project_job=1'>".$mdb->dt[work_ready_cnt]." 개</a></td>
					</tr>
					<tr height=20>
						<td ><img src='../image/icon_list.gif' border=0 align=bottom ><a href='work_list.php?list_type=&group_ix=".$projects[$i][group_ix]."&view_project_job=1'><span class=blk>미처리 업무 : </span></a></td>
						<td><a href='work_list.php?list_type=&group_ix=".$projects[$i][group_ix]."&view_project_job=1'>".$mdb->dt[work_noncomplete_cnt]." 개</a></td><td width='10'></td>
					</tr>
					<tr height=20>
						<td ><img src='../image/icon_list.gif' border=0 align=bottom ><a href='work_list.php?list_type=search&work_status=WC&group_ix=".$projects[$i][group_ix]."&view_project_job=1'><span class=blk>완료된 업무 : </span></a></td>
						<td><a href='work_list.php?list_type=search&work_status=WC&group_ix=".$projects[$i][group_ix]."&view_project_job=1'>".$mdb->dt[work_complete_cnt]." 개</a></td>	
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>";
}
			$mstring .= " 
			</tr>";

$mstring .= "</table>";

return $mstring;
}

function TodayWeather(){
	//11: 눈
	//01: 맑음
	//02: 구름조금
	//03: 구름많음
	//04:흐림
	//
	$weather_icons = array("01"=>"../images/icon/sun.swf","02"=>"../images/icon/sun_cloud.swf","03"=>"../images/icon/cloud.swf","04"=>"../images/icon/cloud.swf","11"=>"../images/icon/cloud.swf");
	$doc = new DOMDocument();
	$doc->load("http://www.kma.go.kr/weather/forecast/mid-term-xml.jsp?stnId=109");


	$title =  $doc->getElementsByTagName("title")->item(0)->nodeValue;
	$wf =  $doc->getElementsByTagName("wf")->item(0)->nodeValue;

	$doc->load("http://www.kma.go.kr/XML/weather/sfc_web_map.xml");
	//$desc =  $doc->getElementsByTagName("local[*108]")->item(0)->nodeValue;
	//echo $desc;
	
	$xpath = new DOMXpath($doc);
	//$params = $xpath->query("*[@pcode='$pcode']");
	//$params = $xpath->query("*[@stn_id='108']");
	//$params = $xpath->query("*[@stn_id='108']");
	//echo $xpath->query("/current/weather/local[@stn_id='108']");
	$params = $xpath->query("//*[@stn_id='108']");
	//print_r($params);
	//echo $params->item(0)->nodeValue;
	if($params){
		//echo "test";
		foreach ($params as $param) {
		//	$nodes = $param->childNodes;
			$icon = $param->getAttribute('icon');
			$desc = $param->getAttribute('desc');
			$ta = $param->getAttribute('ta');
			$icon = $param->getAttribute('icon');
			//echo $icon;
		}
	}
	
$mstring = " 
	
		<table width='100%' border='0' cellspacing='0' cellpadding='0' >			
			<col width='60'>			
			<col width='*'>
			<col width='60'>
			<tr height=25>
				<td colspan=2 ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>오늘의 날씨</b> - ".$title." </td>
				<td  align=right><a href='#'>more</a></td>
			</tr>
			<tr >
				<td colspan=3  height='130' style='border:1px solid silver;'>
				<table cellpadding=0 cellspacing=0 style='table-layout:fixed;' height='130'>
					<col width='150'>
					<col width='*'>
					<tr>
						<td class='ctr'><script language='javascript'>generate_flash('".$weather_icons[$icon]."', 100, 100)</script></td>
						<td style='line-height:140%;padding:10px;'><b>".$desc."</b> <b>".$ta."</b><br>".cut_str(strip_tags($wf),120)."</td>
					</tr>
				</table>
				</td>
			</tr>";

$mstring .= "</table>";

return $mstring;
}

function WorkSummary(){
	global $admininfo, $admin_config;
	$mdb = new Database;
	
	$sql = "SELECT dp_name FROM shop_company_department where dp_ix = '".$admininfo["department"]."'   ";
	
	$mdb->query($sql);
	$mdb->fetch();
	$department_name = $mdb->dt[dp_name];


	$sql = "SELECT IFNULL(sum(case when status != 'WC' then 1 else 0 end),0) as work_cnt,
			IFNULL(sum(case when status = 'WC' then 1 else 0 end),0) as work_complete_cnt,
			IFNULL(sum(case when status != 'WC' and dday < ".date("Ymd")." then 1 else 0 end),0) as work_noncomplete_cnt,
			IFNULL(sum(case when status != 'WC' and ".date("Ymd")." between sdate and dday then 1 else 0 end),0) as work_ready_cnt,
			IFNULL(sum(case when status = 'WI' then 1 else 0 end),0) as work_ing_cnt
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and (wl.charger_ix =  '".$admininfo[charger_ix]."' )  			
			";	
	//echo nl2br($sql);
	$mdb->query($sql);
	$mdb->fetch($i);

$mstring = " 
	
		<table width='100%' border='0' cellspacing='0' cellpadding='0' >
			<tr height=25>
				<td ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>업무요약</b></td>
				<td ></td>
			</tr>
			<tr>
				<td height='130' style='border:1px solid silver;'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0'  >
					<col width=*>
					<col width=140>
					<col width=180>
					<tr height=21>
						<td class='ctr' rowspan=5  >";
						if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$admininfo[charger_ix].".jpg")){
							$mstring .= "<img src='".$admin_config[mall_data_root]."/work/profile/profile_".$admininfo[charger_ix].".jpg' width=90 height=90 style='margin:5px 15px;'>";
						}else{
							$mstring .= "<img src='../images/man.jpg' width=90 height=90 style='margin:5px 15px;'>";
						}
		$mstring .= "
						</td>
						<td  ><a href='work_list.php'><img src='../image/icon_list.gif' border=0 align=bottom >부서 / 이름</a></td>
						<td  style='font-weight:bold;background-color:#ffffff'> : ".$department_name." / ".$admininfo[charger]."</td>				
					</tr>
					<tr height=21>
						<td ><a href='work_list.php?view_project_job=1'><img src='../image/icon_list.gif' border=0 align=bottom >할당된 업무</a></td>
						<td style='font-weight:bold;background-color:#ffffff'> : <a href='work_list.php?list_type=myjob&view_project_job=1'>".$mdb->dt[work_cnt]." 개</a></td>		
					</tr>
					<tr height=21>
						<td ><img src='../image/icon_list.gif' border=0 align=bottom ><a href='work_list.php?list_type=myjob&view_project_job=1'>진행중</a>/<a href='work_list.php?list_type=search&work_status=WR&charger_ix=".$admininfo[charger_ix]."&view_project_job=1'>대기 업무</a></td>
						<td style='font-weight:bold;background-color:#ffffff'> : <a href='work_list.php?list_type=myjob&view_project_job=1'>".$mdb->dt[work_ing_cnt]." 개</a>/<a href='work_list.php?list_type=search&work_status=WR&charger_ix=".$admininfo[charger_ix]."&view_project_job=1'>".$mdb->dt[work_ready_cnt]." 개</a></td>				
					</tr>
					<tr height=21>
						<td ><img src='../image/icon_list.gif' border=0 align=bottom ><a href='work_list.php?list_type=before&view_project_job=1'>미처리 업무</a></td>
						<td style='font-weight:bold;background-color:#ffffff'> : <a href='work_list.php?list_type=before&view_project_job=1'>".$mdb->dt[work_noncomplete_cnt]." 개</a></td>				
					</tr>
					<tr height=21>
						<td ><img src='../image/icon_list.gif' border=0 align=bottom ><a href='work_list.php?list_type=search&work_status=WC'>완료된 업무</a></td>
						<td style='font-weight:bold;background-color:#ffffff'> : <a href='work_list.php?list_type=search&work_status=WC&view_project_job=1'>".$mdb->dt[work_complete_cnt]." 개</a></td>				
					</tr>
				</table>
				</td>
			</tr>
		</table>
				";

return $mstring;
}



function TodayWork(){
	global $admininfo;
	$mdb = new Database;
	
	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
			and wl.status != 'WC'
			order by regdate desc
			limit 5";	
	//echo nl2br($sql);
	$mdb->query($sql);

$mstring = " 
	
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			<tr height=25>
				<td ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>최근 등록업무</b></td>
				<td align=right><a href='work_list.php?mmode=&list_view_type=&list_type=myjob'>more</a></td>
			</tr>";
if($mdb->total == 0){
$mstring .= "<tr ><td colspan=2 class='dot-x' style='height:75px;text-align:center;'>등록된 최근등록 업무가 없습니다.</td></tr>";
//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
	$mstring .= "<tr><td colspan=2 >
		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>
			";
		for ($i = 0; $i < $mdb->total; $i++)
		{
			$mdb->fetch($i);
$mstring .= "<tr>
					<td>
						<table cellpadding=0 cellspacing=0 width=100% border=0 >
							<col width='*'>
							<col width='10%'>
							<col width='30%'>
							<tr height='25px'>
							<td style='padding-left:10px;'>
							".($mdb->dt[is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
							".($mdb->dt[is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
							".($mdb->dt[co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
							<a href=\"work_view.php?mmode=&wl_ix=".$mdb->dt[wl_ix]."\" align=absmiddle><b style='color:#000000;'>".Cut_Str($mdb->dt[work_title],25)."</b></a></td>
							<td>".$mdb->dt[name]."</td>
							<td>
								<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
									<col width='".($mdb->dt[complete_rate] == 0 ? 1:$mdb->dt[complete_rate])."%'>
									<col width='".((100-$mdb->dt[complete_rate]) == 100 ? 99:(100-$mdb->dt[complete_rate]))."%'>
									<tr height=8><td bgcolor='#ff7200' id='graph_".$mdb->dt[wl_ix]."'></td><td></td></tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>";
//$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
		}
		$mstring .= "</table></td></tr>";
}

$mstring .= "</table>
		";

return $mstring;
}


function TodayMyWrok(){
	global $admininfo;
	$mdb = new Database;
	
	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and wl.charger_ix =  '".$admininfo[charger_ix]."' and wl.status != 'WC'

			order by regdate desc
			limit 5";	
	//echo nl2br($sql);
	$mdb->query($sql);

$mstring = " 
	
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			<tr height=25>
				<td ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>나의업무</b></td>
				<td align=right><a href='work_list.php?mmode=&list_view_type=&list_type=myjob'>more</a></td>
			</tr>";
if($mdb->total == 0){
$mstring .= "<tr ><td colspan=2 class='dot-x' style='height:75px;text-align:center;'>등록된 나의 업무가 없습니다.</td></tr>";
//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
	$mstring .= "<tr><td colspan=2 >
		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>";

		for ($i = 0; $i < $mdb->total; $i++)
		{
			$mdb->fetch($i);
$mstring .= "<tr>
					<td>
						<table cellpadding=0 cellspacing=0 width=100% border=0 >
							<col width='*'>
							<col width='10%'>
							<col width='30%'>
							<tr height='25px'>
							<td style='padding-left:10px;'>
							".($mdb->dt[is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
							".($mdb->dt[is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
							".($mdb->dt[co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
							<a href=\"work_view.php?mmode=&wl_ix=".$mdb->dt[wl_ix]."\" align=absmiddle><b style='color:#000000;'>".Cut_Str($mdb->dt[work_title],25)."</b></a></td>
							<td>".$mdb->dt[name]."</td>
							<td>
								<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
									<col width='".($mdb->dt[complete_rate] == 0 ? 1:$mdb->dt[complete_rate])."%'>
									<col width='".((100-$mdb->dt[complete_rate]) == 100 ? 99:(100-$mdb->dt[complete_rate]))."%'>
									<tr height=8><td bgcolor='#ff7200' id='graph_".$mdb->dt[wl_ix]."'></td><td></td></tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
				</tr>";
//$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
		}
		$mstring .= "</table></td></tr>";
}

$mstring .= "</table>
		";

return $mstring;
}

/*
function DateBySchedule($date){
	$mdb = new Database;
	
	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			FROM work_list wl , work_group wg, common_member_detail cmd
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code
			and wl.charger_ix =  '".$_SESSION['admininfo']['charger_ix']."' and is_schedule = '1'
			and ".$date." between sdate and dday 
			order by sdate desc, stime asc
			limit 5";	
	//echo nl2br($sql);
	$mdb->query($sql);
	return $mdb->fetchall();
}
*/

function TodayMySchedule(){
	global $admininfo;
	$mdb = new Database;
	
	

$mstring = "<table cellpadding=0 cellspacing=0 width=100%>
		<col width='33%'>
		<col width='34%'>
		<col width='33%'>
		<tr height=25>
				<td ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>나의 스케줄</b></td>
				<td style='text-align:right;padding:3px;' colspan=2><a href='work_list.php?list_view_type=calendar'>more</a></td>
			</tr>
		<tr>
			<td style='padding:0px;vertical-align:top;'>";
$yesterday_schedules = DateBySchedule(date("Ymd",time()-84600));
$mstring .= "	<table width=100% border='0' cellspacing='0' cellpadding='0' height='25' style='margin:3px 0px;float:left;' class='list_table_box'> 
				 <tr ><td class='m_td' style='height:25px;text-align:center;background-color:#efefef;font-weight:bold;'>어제 스케줄</td></tr>
				 <tr ><td class='m_td' style='height:25px;text-align:center;background-color:#efefef;font-weight:bold;'>".date("Y.m.d",time()-84600)." </td></tr>";

if(count($yesterday_schedules) == 0){
	$mstring .= "<tr ><td class='list_box_td'  style='height:120px;text-align:center;padding:5px;'>등록된 스케줄 정보가 없습니다.</td></tr>";
	//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
		$mstring .= "<tr>
						<td colspan=2 class='list_box_td lft' style='vertical-align:top;min-height:120px;padding:5px;'>";
		for ($i = 0; $i < count($yesterday_schedules); $i++)
		{
			
	$mstring .= "
						<table width=100% cellpadding=0 cellspacing=0>
						<col width='100px'>
						<col width='*'>
						<tr height='25px'>
							<td  align='center' style='padding:0 5px 0 0' nowrap><img src='../image/icon_list.gif' border=0 align=bottom >".$yesterday_schedules[$i][stime]." ~ ".$yesterday_schedules[$i][dtime]."</td>
							<td align=left style='padding:0 5px 0 10px' >
							".($yesterday_schedules[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
							".($yesterday_schedules[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
							".($yesterday_schedules[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
							<a href=\"work_view.php?mmode=&wl_ix=".$yesterday_schedules[$i][wl_ix]."\" align=absmiddle>".Cut_Str($yesterday_schedules[$i][work_title],15)."</a></td>					
						</tr>
						</table>
						";
		}
	$mstring .= "	</td>
					</tr>";
}
$mstring .= "	</table>";
$mstring .= "	</td>";
$mstring .= "	<td style='padding:0px 2px;vertical-align:top;'>";

$today_schedules = DateBySchedule(date("Ymd"));
$mstring .= "	<table width=100% border='0' cellspacing='0' cellpadding='0' height='25' style='margin:3px 0px;float:left;' class='list_table_box'> 
				 <tr ><td colspan=2 style='height:25px;text-align:center;background-color:#efefef;font-weight:bold;'>오늘 스케줄</td></tr>
				 <tr ><td colspan=2 style='height:25px;text-align:center;background-color:#efefef;font-weight:bold;'> ".date("Y.m.d")." </td></tr>";

if(count($today_schedules) == 0){
	$mstring .= "<tr ><td class='list_box_td point'  colspan=2 class='dot-x' style='height:120px;text-align:center;padding:5px;'>등록된 스케줄 정보가 없습니다.</td></tr>";
	//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
		$mstring .= "<tr>
						<td colspan=2 class='list_box_td point' style='vertical-align:top;height:120px;padding:5px;'>";
		for ($i = 0; $i < count($today_schedules); $i++)
		{
			
	$mstring .= "
						<table width=100% cellpadding=0 cellspacing=0>
						<col width='100px'>
						<col width='*'>
						<tr height='25px'>
							<td  align='center' style='padding:0 5px 0 0' nowrap><img src='../image/icon_list.gif' border=0 align=bottom >".$today_schedules[$i][stime]." ~ ".$today_schedules[$i][dtime]."</td>
							<td align=left style='padding:0 5px 0 10px' >
							".($today_schedules[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
							".($today_schedules[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
							".($today_schedules[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
							<a href=\"work_view.php?mmode=&wl_ix=".$today_schedules[$i][wl_ix]."\" align=absmiddle>".Cut_Str($today_schedules[$i][work_title],15)."</a></td>					
						</tr>
						</table>
						";
		}
	$mstring .= "	</td>
					</tr>";
}
$mstring .= "	</table>";
$mstring .= "	</td>";
$mstring .= "	<td style='padding:0px;vertical-align:top;'>";

$tommorow_schedules = DateBySchedule(date("Ymd",time()+84600));
$mstring .= "	<table width=100% border='0' cellspacing='0' cellpadding='0' height='25' style='margin:3px 0px;float:left;' class='list_table_box'> 
				 <tr ><td colspan=2 style='height:25px;text-align:center;background-color:#efefef;font-weight:bold;'>내일 스케줄</td></tr>
				 <tr ><td colspan=2 style='height:25px;text-align:center;background-color:#efefef;font-weight:bold;'>".date("Y.m.d",time()+84600)."</td></tr>";

if(count($tommorow_schedules) == 0){
	$mstring .= "<tr ><td colspan=2 style='height:120px;text-align:center;padding:5px;'>등록된 스케줄 정보가 없습니다.</td></tr>";
//	$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
		$mstring .= "<tr>
						<td colspan=2 class='list_box_td lft' style='vertical-align:top;min-height:120px;padding:5px;'>";
		for ($i = 0; $i < count($tommorow_schedules); $i++)
		{
			
	$mstring .= "
						<table width=100% cellpadding=0 cellspacing=0>
						<col width='100px'>
						<col width='*'>
						<tr height='25px'>
							<td  align='center' style='padding:0 5px 0 0' nowrap><img src='../image/icon_list.gif' border=0 align=bottom >".$tommorow_schedules[$i][stime]." ~ ".$tommorow_schedules[$i][dtime]."</td>
							<td align=left style='padding:0 5px 0 10px' >
							".($tommorow_schedules[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
							".($tommorow_schedules[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
							".($tommorow_schedules[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
							<a href=\"work_view.php?mmode=&wl_ix=".$tommorow_schedules[$i][wl_ix]."\" align=absmiddle>".Cut_Str($tommorow_schedules[$i][work_title],15)."</a></td>					
						</tr>
						</table>
						";
		}
	$mstring .= "	</td>
					</tr>";
}
$mstring .= "	</table>";

$mstring .= "
				</td>
			</tr>";


$mstring .= "		</table>";
//$mstring .= "	</td>";
//$mstring .= "</tr>";
//$mstring .= "</table>";
return $mstring;
}



function LiveComment_Main(){
	global $admininfo;
	$mdb = new Database;
	
	$sql = "select wl.wl_ix , is_hidden, is_schedule, co_charger_yn, work_title, complete_rate, count(*) as comment_cnt from work_list wl , work_comment wc 
				where  wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
				and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  and wl.wl_ix = wc.wl_ix 
				and wl.status != 'WC' 
				group by wl.wl_ix order by wl.regdate desc 
				limit 10  ";

	$sql = "SELECT wl.*, wc.comment, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						FROM work_list wl , work_group wg, common_member_detail cmd, work_comment wc
						where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
						and wl.group_ix = wg.group_ix and wc.charger_ix = cmd.code and wl.wl_ix = wc.wl_ix 
						and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
						and wl.status != 'WC' 
						group by wl.wl_ix
						order by wl.regdate desc limit 10
						";	
	
	//echo nl2br($sql);
	$mdb->query($sql);
	$works = $mdb->fetchall();
	//echo count($works);
$mstring = " 
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			<tr height=25>
				<td colspan=3 ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>라이브 컴멘트</b></td>
				<td align=right><a href='work_comment_list.php'>more</a></td>
			</tr>";
if($mdb->total == 0){
$mstring .= "<tr ><td colspan=4 class='dot-x' style='height:75px;text-align:center;'>등록된 comment 가 없습니다.</td></tr>";
//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
	$mstring .= "<tr><td colspan=4>
		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>";
		for ($i = 0; $i < count($works); $i++)
		{
			//$mdb->fetch($i);
			$mstring .= "<tr>
								<td>
								<table width=100% cellpadding=0 cellspacing=0 border=0>";
					$mstring .= "<tr>
										<td>
											<table cellpadding=0 cellspacing=0 width=100% border=0>
												<col width='*'>
												<col width='5%'>
												<col width='20%'>
												<col width='5%'>
												<tr height='25px'>							
												<td style='padding-left:10px;'>
												".($works[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
												".($works[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
												".($works[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
												<a href=\"work_view.php?mmode=&wl_ix=".$works[$i][wl_ix]."\" align=absmiddle><b style='color:#000000;'>".$works[$i][work_title]."</b>(".$works[$i][comment_cnt].")</a></td>
												<td></td>
												<td>
													<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
														<col width='".($works[$i][complete_rate] == 0 ? 1:$works[$i][complete_rate])."%'>
														<col width='".((100-$works[$i][complete_rate]) == 100 ? 99:(100-$works[$i][complete_rate]))."%'>
														<tr height=8><td bgcolor='#ff7200' id='graph_".$works[$i][wl_ix]."'></td><td></td></tr>
													</table>
												</td>
												<td>".$works[$i][complete_rate]." % </td>
											</tr>
											</table>
										</td>
									</tr>";
								//$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
					
			

			$sql = "SELECT wl.*, wc.comment, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						FROM work_list wl , work_group wg, common_member_detail cmd, work_comment wc
						where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
						and wl.group_ix = wg.group_ix and wc.charger_ix = cmd.code and wl.wl_ix = wc.wl_ix and wl.wl_ix = '".$works[$i][wl_ix]."'
						and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
						and wl.status != 'WC' 
						order by regdate desc
						";	
			//echo nl2br($sql);
			$mdb->query($sql);
			$comments = $mdb->fetchall();
			//echo count($comments)."<br>";
			for ($j = 0; $j < count($comments); $j++)
			{
			$mstring .= "<tr height='25px'>
								<td style='padding:0px 10px;'>
								[comment] <a href=\"work_view.php?mmode=&wl_ix=".$comments[$j][wl_ix]."\" align=absmiddle class=small>".$comments[$j][comment]."</a> | <b style='color:#000000;'>".$comments[$j][name]."</b>
								</td>
							</tr>
							
							
							";
			}
				$mstring .= "</table>
							</td>
						</tr>";
		}
		$mstring .= "</table>
						</td>
					</tr>";
}


$mstring .= "</table>
		";

return $mstring;
}



function LiveIssue_Main(){
	global $admininfo;
	$mdb = new Database;
	


	$sql = "SELECT wl.*, wi.issue, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
						where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
						and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix 
						and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
						and wl.status != 'WC' 
						group by wl.wl_ix
						order by wl.regdate desc limit 10
						";	
	
	//echo nl2br($sql);
	$mdb->query($sql);
	$works = $mdb->fetchall();
	//echo count($works);
$mstring = " 
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			<tr height=25>
				<td colspan=3 ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>최근 이슈리스트</b></td>
				<td align=right><a href='work_comment_list.php'>more</a></td>
			</tr>";
if($mdb->total == 0){
$mstring .= "<tr ><td colspan=4 class='dot-x' style='height:75px;text-align:center;border:1px solid silver;'>등록된 ISSUE 가 없습니다.</td></tr>";
//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
	$mstring .= "<tr><td colspan=4>
		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>";
		for ($i = 0; $i < count($works); $i++)
		{
			//$mdb->fetch($i);
			$mstring .= "<tr>
								<td>
								<table width=100% cellpadding=0 cellspacing=0 border=0>";
					$mstring .= "<tr>
										<td>
											<table cellpadding=0 cellspacing=0 width=100% border=0>
												<col width='*'>
												<col width='5%'>
												<col width='20%'>
												<col width='5%'>
												<tr height='25px'>							
												<td style='padding-left:10px;'>
												".($works[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
												".($works[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
												".($works[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
												<a href=\"work_view.php?mmode=&wl_ix=".$works[$i][wl_ix]."\" align=absmiddle><b style='color:#000000;'>".$works[$i][work_title]."</b>(".$works[$i][comment_cnt].")</a></td>
												<td></td>
												<td>
													<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
														<col width='".($works[$i][complete_rate] == 0 ? 1:$works[$i][complete_rate])."%'>
														<col width='".((100-$works[$i][complete_rate]) == 100 ? 99:(100-$works[$i][complete_rate]))."%'>
														<tr height=8><td bgcolor='#ff7200' id='graph_".$works[$i][wl_ix]."'></td><td></td></tr>
													</table>
												</td>
												<td>".$works[$i][complete_rate]." % </td>
											</tr>
											</table>
										</td>
									</tr>";
								//$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
					
			

			$sql = "SELECT wl.*, wi.issue, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
						where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
						and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix and wl.wl_ix = '".$works[$i][wl_ix]."'
						and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
						and wl.status != 'WC' 
						order by regdate desc
						";	
			//echo nl2br($sql);
			$mdb->query($sql);
			$comments = $mdb->fetchall();
			//echo count($comments)."<br>";
			for ($j = 0; $j < count($comments); $j++)
			{
			$mstring .= "<tr height='25px'>
								<td style='padding:0px 10px;'>
								[comment] <a href=\"work_view.php?mmode=&wl_ix=".$comments[$j][wl_ix]."\" align=absmiddle class=small>".$comments[$j][issue]."</a> | <b style='color:#000000;'>".$comments[$j][name]."</b>
								</td>
							</tr>
							
							
							";
			}
				$mstring .= "</table>
							</td>
						</tr>";
		}
		$mstring .= "</table>
						</td>
					</tr>";
}


$mstring .= "</table>
		";

return $mstring;
}

/*
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

CREATE TABLE `shop_addressbook` (
  `wl_ix` int(8) unsigned NOT NULL auto_increment,
  `com_div` varchar(20) default '',
  `div` varchar(30) default '',
  `url` varchar(255) default NULL,
  `page` int(8) default '0',
  `com_name` varchar(50) default NULL,
  `charger` varchar(50) default NULL,
  `phone` varchar(50) default NULL,
  `fax` varchar(20) default NULL,
  `mobile` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `homepage` varchar(50) default NULL,
  `com_address` varchar(50) default NULL,
  `mail_yn` enum('0','1') default '1',
  `marketer` varchar(100) default '',
  `memo` text,
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`wl_ix`),
  KEY `regdate` (`regdate`)
) TYPE=MyISAM DEFAULT CHARSET=utf8


CREATE TABLE `shop_sms_address` (
  `sa_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_ix` int(8) DEFAULT NULL,
  `sa_name` varchar(25) NOT NULL DEFAULT '0',
  `sa_mobile` varchar(15) DEFAULT '',
  `sa_sex` enum('M','F')  DEFAULT NULL,
  `sa_etc` varchar(255) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sa_ix`),
  KEY `regdate` (`regdate`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

CREATE TABLE `shop_sms_history` (
  `sh_ix` int(8) NOT NULL AUTO_INCREMENT,
  `send_phone` varchar(50) DEFAULT NULL,
  `dest_mobile` varchar(15) DEFAULT '',
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}
*/
?>