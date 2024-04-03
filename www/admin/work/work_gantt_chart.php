<? 
include("../class/layout.work.class");
include("work.lib.php");

//print_r($admininfo);
$db = new Database;
$mdb = new Database;

$db->query("SELECT min(sdate) as sdate, max(dday) as dday FROM work_list where charger_ix ='$charger_ix' and status != 'WC' order by architecture_code asc, sdate desc limit 30 ");
$db->fetch();
$project_sdate = $db->dt[sdate];
$project_edate = $db->dt[dday];


$innerview = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  >	  
	  <tr height=25>
		<td colspan=6 style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle onclick='CheckValue(document.group_form);'> <b>".$group_name." 공정단계 설정</b> 
		</td>
	  </tr>
	</table>
	<div style='width:100%;overflow-x:hidden;padding:0  ;' >
	
	<table cellpadding=2 cellspacing=0 border='0' style='border-bottom:2px solid silver;margin-bottom:2px;'>
	    <col width='360px;'>
	    <col width='80px;'>
		<col width='80px;'>
	    <col width='80px;'>
		<col width='80px;'>
	    <col width='90px;'>";
if($mmode == "pop"){
	  $innerview .= "<col width='*'>";
}
$innerview .= "
	  <tr ".($mmode == "pop" ? "height=48":"height=25")."  bgcolor=#efefef align=center style='font-weight:bold'>
		<td > <div style='width:360px;'>공정단계명</div></td>	    
	    <td > <div style='width:80px;'>담당자</div></td>
		<td > <div style='width:80px;'>시작일</div></td>
	    <td > <div style='width:80px;'>완료기한</div></td>
		<td > <div style='width:80px;'>진행율</div></td>
		<td > <div style='width:90px;'>완료일</div></td>";
if($mmode == "pop"){
	$innerview .= "
	<td style='width:auto;text-align:left;' >";

	$project_sdate_num = date("z",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
	$project_edate_num = date("z",mktime(0,0,0,substr($project_edate,4,2),substr($project_edate,6,2),substr($project_edate,0,4)));
	$first_week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
	$project_sdate = date("Ymd",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)- $first_week_num,substr($project_sdate,0,4)));

	$project_sdate_num = $project_sdate_num - $first_week_num;
	$innerview .= "<table bgcolor=#efefef cellspacing=1 style='table-layout:fixed;width:100%'>";
	//echo $project_sdate_num;
	for($i=0;$i < $project_edate_num-$project_sdate_num;$i++){
		
		$col_str .= "<col width='30px'>";
		$week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
		$day = date("d",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
		$this_day = date("Y년 m월 d일",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
		$innerview1 .= "<td width='30px' height=20 align=center title='".$this_day."' class=small>".$week_name[$week_num]."".$day." </td>";

		if($i == 0){
			$innerview2 .= "<td height=20 align=center colspan='".(7-$week_num)."' class=small>".$this_day."".$day."</td>";
		}else if($week_num == 0){
			$innerview2 .= "<td height=20 align=center colspan='7' class=small>".$this_day."".$day."</td>";
		}
	}
	if($project_edate_num-$project_sdate_num > 28){
		$end_date_num = $project_edate_num-$project_sdate_num + (7-(($project_edate_num-$project_sdate_num)%7));
	}else{
		$end_date_num = 28;
	}
	//echo $end_date_num;
	for($i=$i;$i < $end_date_num;$i++){
		$week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
		$day = date("d",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
		$this_day = date("Y년 m월 d일",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
		$innerview1 .= "<td width='30' height=20 align=center title='".$this_day."' class=small>".$week_name[$week_num]."".$day."</td>";
		$col_str .= "<col width='30px'>";
		if($i == 0){
			$innerview2 .= "<td height=20 align=center colspan='".(7-$week_num)."' class=small>".$this_day."".$day."</td>";
		}else if($week_num == 0){
			$innerview2 .= "<td height=20 align=center colspan='7' class=small>".$this_day."".$day."</td>";
		}
	}
	//echo $i%7;
	$innerview .= $col_str;
	$innerview .= "<tr bgcolor=#ffffff>".$innerview2."</tr>";
	$innerview .= "<tr bgcolor=#ffffff>".$innerview1."</tr>";
	$innerview .= "</table>";

	$innerview .= "</td>";
}
$innerview .= "
	  </tr>
	 </table>
	 <table cellpadding=2 cellspacing=0 border='0' id='project_architecture' onselectstart='return false;' style='margin-bottom:20px;width:100%;'>	  
		
		<col width='360px;'>
	    <col width='80px;'>
		<col width='80px;'>
	    <col width='80px;'>
		<col width='80px;'>
	    <col width='90px;'>";
if($mmode == "pop"){
$innerview .= "
		<col style='width:auto;'>";
}
if($_COOKIE["view_all_group"] == "1"){
	//$where .= " and (wg.disp = '".$_COOKIE["view_all_group"]."' )  ";
}else{
	$where .= " and (wg.disp = '1' )  ";
}

$sql = "select wl.* 
		from work_list wl where company_id ='".$admininfo["company_id"]."' 
		and wl.group_ix = '$group_ix' and depth != '0' order by architecture_code asc  ";
$db->query($sql);


if($db->total || true){
	$act = "project_update";

	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center id='architecture_row_0' class='listTR' depth='1' rno='1.' onclick=\"if($(this).css('background-color') == '#ffffff'){ $(this).css('background-color','#efefef'); }else{ $(this).css('background-color','#ffffff'); }\"><!--onmouseover=\"$(this).find('div[id^=button_area]').css('display','inline');\" onmouseout=\"$(this).find('div[id^=button_area]').css('display','none');\"-->
			<td align='left' id='tdWork_name' onselect='return true;'>
				<span id=rno style='padding-left:10px;width:30px;'>1.</span>
				<input type='text' class='textbox' name='architecture[0][architecture_name]' id='architecture_name' style='width:270px;' value='' onselectstart='return true;' readonly >
				<div style='display:inline;' id='button_area'>
				<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄' style='display:none;margin-right:0px;' id='architecture_is_schedule'> 
				
				</div>
				<input type='hidden' name='architecture[0][architecture_wl_ix]' id='architecture_wl_ix' class='architecture_wl_ix' value='' style='width:40px;' >				
				<input type='hidden' name='architecture[0][architecture_code]' id='architecture_code' class='architecture_code' value='1.' style='width:40px;' >
				<input type='hidden' name='architecture[0][architecture_depth]' id='architecture_depth' class='architecture_depth' style='width:40px;' value='1' >
				<input type='hidden' name='architecture[0][sub_archietcture_cnt]' id='sub_archietcture_cnt' class='sub_archietcture_cnt' style='width:40px;' value='0' >
			</td>	
		    <td>".projectUserList($admininfo["company_id"],"architecture[0][architecture_charger_ix]", 'architecture_charger_ix', $dp_ix, $charger_ix)."	<!--input type='hidden' name='architecture[0][architecture_charger_ix]' class='textbox architecture_charger_ix' id='architecture_charger_ix' value='' --></td>
			<td><input type='text' name='architecture[0][architecture_sdate]' class='textbox architecture_sdate' id='architecture_sdate' value='' style='width:75px;text-align:center;' ></td>
		    <td><input type='text' name='architecture[0][architecture_edate]' class='textbox architecture_edate' id='architecture_edate' value='' style='width:75px;text-align:center;' ></td>
		    <td id='architecture_status'>-</td>
			<td id='architecture_complete_date'>-</td>";
if($mmode == "pop"){
	$innerview .= "
	<td style='width:auto;text-align:left;' >";
	//$project_sdate_num = date("z",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
	//$project_edate_num = date("z",mktime(0,0,0,substr($project_edate,4,2),substr($project_edate,6,2),substr($project_edate,0,4)));
		$innerview .= "<div style='width:20px;border:1px solid gray;height:6px;cursor:pointer;' id='architecture_scedule_bar' ><div style='background:#ff7200;width:1px;'></div></div>";
	$innerview .= "</td>";
}
$innerview .= "
		  </tr>";
/*
	for($i=0;$i < $db->total;$i++){
		
	$db->fetch($i);
	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center id='architecture_row_".$i."' class='listTR' depth='".$db->dt[depth]."'>	    
		    <td id='tdSeq'>-</td>
			<td width='*' align='left' id='tdWork_name' style='padding-left:".(20*$db->dt[depth])."' onselect='return false;'>
				<span id=rno>".$db->dt[architecture_code]."</span> 
				<input type='text' name='architecture[".$i."][wl_ix]' id='architecture_wl_ix' class='architecture_wl_ix' value='".$db->dt[wl_ix]."'>
				<input type='hidden' name='architecture[".$i."][architecture_code]' id='architecture_code' class='architecture_code' style='width:40px;' value='".$db->dt[architecture_code]."'>
				<input type='hidden' name='architecture[".$i."][depth]' id='architecture_depth' class='architecture_depth' style='width:40px;' value='".$db->dt[depth]."'>
				<input type='text' class='textbox' name='architecture[".$i."][architecture_name]' id='architecture_name' style='width:220px;' value='".$db->dt[work_title]."'> 
				<a onclick=\"SubAddRow('project_architecture',$(this).parent().parent())\" class=small>하위작업추가</a>
			</td>	
		    <td><input type='text' name='architecture[".$i."][architecture_sdate]' class='textbox architecture_sdate' id='architecture_sdate".$db->dt[architecture_code]."' value='".changeDate($db->dt[sdate],"Y-m-d")."' style='width:80px;text-align:center;' ></td>
		    <td><input type='text' name='architecture[".$i."][architecture_edate]' class='textbox architecture_edate' id='architecture_edate".$db->dt[architecture_code]."' value='".changeDate($db->dt[dday],"Y-m-d")."' style='width:80px;text-align:center;' ></td>
		    <td>-</td>
		  </tr>";
	}
*/
}else{
	$act = "project_update";
	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center id='architecture_row_0' class='listTR' depth='1'>	    
		    
			<td width='*' align='left' id='tdWork_name' onselect='return false;'>
				<span id=rno>1.</span> 
				<input type='hidden' name='architecture[0][architecture_wl_ix]' id='architecture_wl_ix' class='architecture_wl_ix' style='width:40px;' value=''>
				<input type='hidden' name='architecture[0][architecture_code]' id='architecture_code' class='architecture_code' value='1.'>
				<input type='hidden' name='architecture[0][architecture_depth]' id='architecture_depth' class='architecture_depth' style='width:40px;' value='1'>
				<input type='hidden' name='architecture[0][sub_archietcture_cnt]' id='sub_archietcture_cnt' class='sub_archietcture_cnt' style='width:40px;' value='0' >
				<input type='text' class='textbox' name='architecture[0][architecture_name]' id='architecture_name' style='width:180px;' value=''> 
				<a onclick=\"SubAddRow('project_architecture',$(this).parent().parent())\" class=small>추가</a> | <a onclick=\"$(this).parent().parent().remove();\" class=small>삭제</a>
			</td>	
		    <td><input type='text' name='architecture[0][architecture_sdate]' class='textbox architecture_sdate' id='architecture_sdate' value='' style='width:75px;text-align:center;' ></td>
		    <td><input type='text' name='architecture[0][architecture_edate]' class='textbox architecture_edate' id='architecture_edate' value='' style='width:75px;text-align:center;' ></td>
		    <td id='architecture_status'>-</td>
			<td id='architecture_complete_date'>-</td>
		  </tr>";
}

$innerview .= "</table>";
$innerview .= "</div>";
 

/*
$ButtonString = "
<table cellpadding=5 cellspacing=0 border='0' align='left'>
";
if($db->dt[pm_charger_ix] == $admininfo[charger_ix] || $db->dt[pm_charger_ix] == "" || true){
	$ButtonString .= "<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td> <td colspan=4 align=center><img src='../image/b_del.gif' border=0 onclick=\"DeleteProject('".$group_ix."')\" style='cursor:pointer;'></td></tr>";
	}
$ButtonString .= "
</table>
";
*/	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='group_form' action='work_gantt_chart.act.php' method='post' onsubmit=\"return CheckArchitectureInfo(this,'project_architecture')\" target=act><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='".$act."'><input name='charger_ix' type='hidden' value='$charger_ix'>";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."";
$Contents = $Contents."</td></tr>";

$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td id='result_area'>".$innerview."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td align=left style='text-align:left;padding-left:600px;'>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
//$Contents = $Contents."<tr><td style='padding-top:20px;'><textarea style='width:100%;height:100px;' id='debug_text'></textarea></td></tr>";
$Contents = $Contents."</table >";


$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >개인별 업무 진행상태 설정은 <b>4단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>개인별 업무 진행상태명 수정</u>을 원하실 경우는 공정단계명을 클릭하시면  수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >스케줄 바를 drag 하여 이동하시면 스케줄이 자동으로 변경됩니다. 변경된 스케줄은 저장버튼을 클릭해서 저장 할수 있습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >저장된후 스케줄 바를 더블 클릭하시면 해당 업무를 확인하실 수 있습니다.</td></tr>
	</table>
	";

	
	$help_text = HelpBox("개인별 업무 진행상태 관리", $help_text)."<br><br>";				
$Contents = $Contents.$help_text;	

 $Script = "<script type='text/javascript' src='./js/jquery-ui-1.8.6.custom.min.js'></script>
 <script type='text/javascript' src='./js/ui/ui.core.js'></script>
 <script language='javascript' src='./js/jquery.cookie.js'></script>
 <script type='text/javascript' src='./js/ui/jquery.ui.droppable.js'></script>
 <script language='javascript' src='work_gantt_chart.js'></script>
 <style>
/* css for timepicker */
#ui-timepicker-div dl{ text-align: left; }
#ui-timepicker-div dl dt{ height: 25px; }
#ui-timepicker-div dl dd{ margin: -25px 0 10px 65px; }
.test
{
    background-color:#efefef;
}


</style>
<link rel='stylesheet' media='all' type='text/css' href='css/jquery-ui-1.8.custom.css' />
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<link type='text/css' href='./js/themes/demos.css' rel='stylesheet' />
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script type='text/javascript' src='./js/jquery.tablednd_0_5.js'></script>

 <script language='javascript'>
var charger_ix = '".$charger_ix."';
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		$('#end_datepicker').datepicker('setDate','+0d');
	}

	});

	
	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});

	$(\"#architecture_sdate\").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			$(\"#architecture_edate\").datepicker('setDate','+1d');
		}

   });

   $(\"#architecture_edate\").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

   });

/*
	 $(\".architecture_sdate\").each(function(){
	   //alert($(this).parent().html());
		$(this).datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});
   });

  
   $(\".architecture_edate\").each(function(){
	   //alert($(this).parent().html());
		$(this).datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});
   });
*/	

});
</script>";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 개인별 업무 진행상태 관리";
	$P->NaviTitle = "개인별 업무 진행상태";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}else if($mmode == "inner_list"){
	echo $innerview;
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 개인별 업무 진행상태 관리";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	echo $P->PrintLayOut();
}

function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT bdiv.*
			FROM work_group bdiv 
			where group_depth = 1 and company_id ='".$admininfo["company_id"]."'
			group by group_ix ";
	
	$mdb->query($sql);
	
	$mstring = "<select name='parent_group_ix' id='parent_group_ix' disabled>";
	$mstring .= "<option value=''>1차프로젝트</option>";
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

/*

CREATE TABLE `shop_address_group` (
  `group_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_group_ix` int(4) unsigned DEFAULT NULL,
  `group_name` varchar(20) DEFAULT NULL,
  `group_depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`group_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>