<?
include("../class/layout.work.class");
include("work.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

//auth(9);
//print_r($admininfo);
$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
$list_view_type = $_GET["list_view_type"];
if($list_view_type == ""){
//	$list_view_type = "calendar";
}
//print_r($_GET);
if ($sdate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));
	$firstdayofweek = mktime(0, 0, 0, date("m")  , date("d")-date("w"), date("Y"));
	 
//	$sDate = date("Y/m/d");
	$sdate = date("Ymd", $firstdayofweek);
	$edate = date("Ymd");
	
	$startDate = date("Y/m/d", $firstdayofweek);
	$endDate = date("Y/m/d");
}



	if(!isset($dp_ix)){
		$dp_ix == $admininfo[department];
	}

	$max = 150; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	$where = " where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl_ix != '' and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code ";
	//if(!$parent_group_ix){
	//	$parent_group_ix = $parent_group_ix;
	//}
		
	if($parent_group_ix != "" && $group_ix == ""){
		$where .= " and (wg.group_ix = '".$parent_group_ix."' or wg.parent_group_ix = '".$parent_group_ix."') ";
	}else if($parent_group_ix != "" && $group_ix != ""){
		$where .= " and wg.parent_group_ix = '".$parent_group_ix."' ";
	}

	if($group_ix != ""){
		//$where .= " and wg.group_ix = '".$group_ix."' ";
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
	
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
		
	
	if($sdate != "" && $edate != ""){	
		$where .= " and ((wl.sdate between  $sdate and $edate) or (dday between  $sdate and $edate) ) ";
	}


	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;
	

	if($orderby && $ordertype){
		$orderby_string = " order by group_ix desc, $orderby $ordertype, regdate desc";
	}else{
		if($report_style == "work_group"){
			$orderby_string = " order by group_ix desc,  sdate asc, stime asc ";
		}else if($report_style == "date"){
			$orderby_string = " order by sdate desc, group_ix desc, stime asc ";
		}else if($report_style == "personal"){
			$orderby_string = " order by charger_ix desc, group_ix desc, sdate desc, stime asc ";
		}
	}

	if($list_type == "mydepartment"){
		if(isset($dp_ix)){
			$where .= " and cmd.department =  '".$dp_ix."' ";
		}else{
			$where .= " and cmd.department =  '".$admininfo[department]."' ";
		}
	}

//	if($list_type == "myjob" || $list_type == "before"){
	if($group_ix != ""){
		$where .= " and (wg.group_ix =  '".$group_ix."' or wg.parent_group_ix =  '".$group_ix."' )  ";
	}
//	}



	if($charger_ix != ""){
			$where .= " and (wl.charger_ix =  '".$charger_ix."'  )  ";
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

	// 전체 갯수 불러오는 부분
	$sql = "SELECT count(*) as total FROM work_list wl, work_group wg, common_member_detail cmd  $where ";
	//echo $sql;
	$db->query($sql);	
	
	$db->fetch();
	$total = $db->dt[total];
	//echo $total;
	

	$sql = "SELECT wl.*, wg.group_name, wg.group_depth, wg.parent_group_ix, cmd.name FROM work_list wl, work_group wg, common_member_detail cmd   $where $orderby_string LIMIT $start, $max";	
	//echo $sql;
	$db->query($sql);



$Script = "

 <script language='javascript'>

function init(){

	var frm = document.searchmember;		
	
	";
if($report_date != "1"){
$Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "	

}


</script>";

if($before_update_kind){
	$update_kind = $before_update_kind;
}

if(!$update_kind){
	$update_kind = "sms";
}

$Contents = "


<table width='100%' cellpadding=0 cellspacing=0 border='0' align='center'>";
if($mmode != "pop"){
$Contents .= "  
  <tr>
    <td align='left' colspan=2 > ".GetTitleNavigation("보고서 관리", "업무관리 > 보고서 관리 ")."</td>
  </tr>
	<tr>
		<td align='left' colspan=2 style='padding:0px 0px 0px 0px;'> 
	    	".WorkTab($total)."
	    </td>
	</tr>";
}
$Contents .= "  
	
	<tr>
		<td colspan=2>
		<div  style='display:block;'>
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
		<input type='hidden' name='mmode' value='".$_GET["mmode"]."'>
		<input type='hidden' name=act value='".$act."'>
		<input type='hidden' name=list_type value='".$list_type."'>
		<input type='hidden' name=list_view_type value='".$list_view_type."'>
		<table width=100% cellpadding=0 cellspacing=0 border=0>	
			<tr>
				<td >";

		$Contents .= SearchBox();
		 
		$Contents .= "    
			</td>
		  </tr>
		  <tr >		    	
				<td style='padding:10px 0px' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  > </td>		    	
			</tr>
		</table>
		</form>
		</div>
		</td>
	</tr>


<tr>
	<td colspan=2>




<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='kt_sms.act.php' target='act' style='display:inline;'><!---->
<input type='hidden' name='mmode' value='".$_GET["mmode"]."'>
<input type='hidden' name='wl_ix[]' id='wl_ix'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>

<div id='result_area' style='display:inline;width:100%;'>
<div class=small style='font-size:19px;font-weight:bold;color:#000000;padding:10px;text-align:center;'>";
if($_GET[report_type] == "D"){
	$Contents .= "일간보고서";
}else if($_GET[report_type] == "W"){
	$Contents .= "주간보고서";
}else if($_GET[report_type] == "M"){
	$Contents .= "월간보고서";
}
$Contents .= "
</div>
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor='#c0c0c0'  align='left' id='work_table'>
  <col width='20%'>
  <col width='30%'>
  <col width='20%'>
  <col width='30%'>
  <tr height='28' bgcolor='#ffffff'>    
    <td align='center' class=leftmenu><font color='#000000'><b>보고기간</b></font></td>
	<td align='center' class=leftmenu>";
if($_GET[sdate] == $_GET[edate]){
	$Contents .= "<b>".ChangeDate($sdate,"Y 년 m 월 d 일")."</b>";
}else{
	$Contents .= "<b>".ChangeDate($sdate,"Y 년 m 월 d 일")." ~ ".ChangeDate($edate,"Y 년 m 월 d 일")." </b>";
}
$Contents .= "
	</td>
	

    <td align='center' class=leftmenu><font color='#000000'><b>담당부서</b></font></td>
    <td align='center' class=leftmenu><font color='#000000'><b>".($department_name ? $department_name:"전체부서")."</b></font></td>
    
  </tr>
  <tr height='28' bgcolor='#ffffff'>    
    <td align='center' class=leftmenu><font color='#000000'><b>보고자</b></font></td>
	<td align='center' class=leftmenu><font color='#000000'><b>".$admininfo[charger]."</b></font></td>
	
   
    <td align='center' class=leftmenu><font color='#000000'><b></b></font></td>
    <td align='center' class=leftmenu><font color='#000000'><b></b></font></td>
    
  </tr>
  <tr>
	<td colspan=4 bgcolor='#ffffff' style='padding:20px;'>
	<table cellpadding=5 cellspacing=0 border=0 width='100%'>";



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
		

if($report_style == "date"){

		if($db->dt[group_depth] == 2){
			$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$db->dt[parent_group_ix]."' ");
			$mdb->fetch(0);
			$group_name = $mdb->dt[group_name]." > ".$db->dt[group_name];
		}else{
			$group_name = $db->dt[group_name];
		}

		if($b_sdate != $db->dt[sdate]){
			$Contents .= "<tr><td colspan=3><div class=small style='font-size:19px;font-weight:bold;color:#000000;'>".ChangeDate($db->dt[sdate],"Y년 m월 d일")."  ".$db->dt[stime]."</div></td></tr>";
		}		

		$Contents .= "
		  <tr height='28' bgcolor='#ffffff'  >   
			<td align='left' style='width:400px;padding:3 0 13 5;line-height:160%;'>";

		$Contents .= "<div style='padding:0 0 0 20px;'>
				".ChangeDate($db->dt[sdate],"Y년 m월 d일")."  ".$db->dt[stime]." ~ ".ChangeDate($db->dt[dday],"Y년 m월 d일")."  ".$db->dt[dtime]."<br>		
				<a href=\"work_view.php?mmode=&wl_ix=".$db->dt[wl_ix]."\" class='".($db->dt[dday] < date("Ymd") && $db->dt[status] != "WC"? "gray":"")."' title='".$db->dt[work_detail]."'>
				$group_name : <b style='font-size:11px;'>".$db->dt[work_title]."</b>
				</a><br>
				<div style='color:gray;'>".nl2br($db->dt[work_detail])."</div>
				</div>
			</td>
			<td align='center' >
			<table width=150 cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
					<col width='".$db->dt[complete_rate]."%'>
					<col width='".(100-$db->dt[complete_rate])."%'>
					<tr height=8><td bgcolor='#ff7200'></td><td></td></tr>
				</table>
				".$work_status[$db->dt[status]]."(".$db->dt[complete_rate]."%)</td>
			<td align='center' >".$db->dt[charger]."</td>
		  </tr>
		   ";
		$b_sdate = $db->dt[sdate];
}else if($report_style == "personal"){
		if($db->dt[group_depth] == 2){
			$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$db->dt[parent_group_ix]."' ");
			$mdb->fetch(0);
			$group_name = $mdb->dt[group_name]." > ".$db->dt[group_name];
		}else{
			$group_name = $db->dt[group_name];
		}
	
		if($b_charger_ix != $db->dt[charger_ix]){
			$Contents .= "<tr><td colspan=3><div class=small style='font-size:19px;font-weight:bold;color:#000000;'>".$db->dt[charger]."</div></td></tr>";
		}
		
		if($b_parent_group_ix != $db->dt[parent_group_ix] || $b_group_ix != $db->dt[group_ix]){
			$Contents .= "<tr><td colspan=3><div class=small style='padding:0px 0px 0px 20px;font-size:17px;font-weight:bold;color:#000000;'>".$group_name."</div></td></tr>";
		}
		

		$Contents .= "
		  <tr height='28' bgcolor='#ffffff'  >   
			<td align='left' style='padding:3 0 3 5;line-height:160%'>";

		$Contents .= "<div style='padding:0 0 0 40px;'>
				".ChangeDate($db->dt[sdate],"Y년 m월 d일")."  ".$db->dt[stime]." ~ ".ChangeDate($db->dt[dday],"Y년 m월 d일")."  ".$db->dt[dtime]."<br>		
				<a href=\"work_view.php?mmode=&wl_ix=".$db->dt[wl_ix]."\" class='".($db->dt[dday] < date("Ymd") && $db->dt[status] != "WC"? "gray":"")."' title='".$db->dt[work_detail]."'>
				<b style='font-size:11px;'>".$db->dt[work_title]."</b>
				</a><br>
				<div style='color:gray;'>".nl2br($db->dt[work_detail])."</div>
				</div>
			</td>
			<td align='center' >
			<table width=150 cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
					<col width='".$db->dt[complete_rate]."%'>
					<col width='".(100-$db->dt[complete_rate])."%'>
					<tr height=8><td bgcolor='#ff7200'></td><td></td></tr>
				</table>
				".$work_status[$db->dt[status]]."(".$db->dt[complete_rate]."%)</td>
			<td align='center' nowrap>".$db->dt[charger]."</td>
		  </tr>
		   ";
		$b_parent_group_ix = $db->dt[parent_group_ix];
		$b_group_ix = $db->dt[group_ix];

		$b_charger_ix = $db->dt[charger_ix];

}else{
		if($db->dt[group_depth] == 2){
			$mdb->query("SELECT group_name FROM work_group WHERE group_ix  = '".$db->dt[parent_group_ix]."' ");
			$mdb->fetch(0);
			$group_name = $mdb->dt[group_name]." > ".$db->dt[group_name];
		}else{
			$group_name = $db->dt[group_name];
		}

		if($b_parent_group_ix != $db->dt[parent_group_ix] || $b_group_ix != $db->dt[group_ix]){
			$Contents .= "<tr><td colspan=3><div class=small style='font-size:19px;font-weight:bold;color:#000000;'>".$group_name."</div></td></tr>";
		}		

		$Contents .= "
		  <tr height='28' bgcolor='#ffffff'  >   
			<td align='left' style='padding:3 0 3 5;line-height:160%'>";

		$Contents .= "<div style='padding:0 0 0 20px;'>
				".ChangeDate($db->dt[sdate],"Y년 m월 d일")."  ".$db->dt[stime]." ~ ".ChangeDate($db->dt[dday],"Y년 m월 d일")."  ".$db->dt[dtime]."<br>		
				<a href=\"work_view.php?mmode=&wl_ix=".$db->dt[wl_ix]."\" class='".($db->dt[dday] < date("Ymd") && $db->dt[status] != "WC"? "gray":"")."' title='".$db->dt[work_detail]."'>
				<b style='font-size:11px;'>".$db->dt[work_title]."</b>
				</a><br>
				<div style='color:gray;'>".nl2br($db->dt[work_detail])."</div>
				</div>
			</td>
			<td align='center' >
			<table width=150 cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
					<col width='".$db->dt[complete_rate]."%'>
					<col width='".(100-$db->dt[complete_rate])."%'>
					<tr height=8><td bgcolor='#ff7200'></td><td></td></tr>
				</table>
				".$work_status[$db->dt[status]]."(".$db->dt[complete_rate]."%)</td>
			<td align='center' nowrap>".$db->dt[charger]."</td>
		  </tr>
		   ";
		$b_parent_group_ix = $db->dt[parent_group_ix];
		$b_group_ix = $db->dt[group_ix];


}
	}

if (!$db->total){
		
$Contents = $Contents."		
  <tr height=250>
    <td colspan='2' align='center' bgcolor='#ffffff'>해당조건에 대한 보고서가 없습니다.</td>
  </tr>";
  
}
	
$Contents .= "
	</table>
	</td>
  </tr>
 
</table>
</div>
	
";

$Contents .= "	</td>
	</tr>
</table>
		";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
		
	$P->addScript = "<script  id='dynamic'></script>
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<link type='text/css' href='./js/themes/demos.css' rel='stylesheet' /
<script type='text/javascript' src='./js/jquery-1.4.4.min.js'></script>
<script type='text/javascript' src='./js/jquery-ui-1.8.6.custom.min.js'></script>
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script language='javascript' src='./js/jquery.blockUI.js'></script>
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = work_menu2();
	$P->Navigation = "업무관리 > 보고서 생성하기";
	$P->NaviTitle = "보고서 생성하기";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "<script  id='dynamic'></script>
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<link type='text/css' href='./js/themes/demos.css' rel='stylesheet' />
<script type='text/javascript' src='./js/jquery-1.4.4.min.js'></script>
<script type='text/javascript' src='./js/jquery-ui-1.8.6.custom.min.js'></script>
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script language='javascript' src='./js/jquery.blockUI.js'></script>
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 보고서 관리";
	$P->title = "보고서 생성하기";
	$P->strContents = $Contents;
	$P->prototype_use = false;
	echo $P->PrintLayOut();
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
	global $admininfo, $mdb, $work_status, $charger_ix, $department, $report_style;
	global $parent_group_ix, $group_ix;
$mstring .= "  
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='input_table_box'>
			<col width='90px'>
			<col width='310px'>
			<col width='120px'>
			<col width='*'>
	";

$mstring .= "<tr height=29>
		      <td class='input_box_title'>보고서 종류 </td>
		      <td class='input_box_item' >
		      <select name=report_type>						
						<option value='' ".CompareReturnValue("",$_GET[report_type],"selected").">보고서 종류</option>
						<option value='D' ".CompareReturnValue("D",$_GET[report_type],"selected").">일간업무보고</option>
						<option value='W' ".CompareReturnValue("W",$_GET[report_type],"selected").">주간업무보고</option>
						<option value='M' ".CompareReturnValue("M",$_GET[report_type],"selected").">월간업무보고</option>
						<option value='P' ".CompareReturnValue("P",$_GET[report_type],"selected").">프로젝트 진행 보고</option>
		      </select>
		      </td>	
			  <td class='input_box_title'>보고서 형식 </td>
		      <td class='input_box_item' nowrap>
				<input type='radio' name='report_style' id='report_style_1'  value='work_group' ".($report_style == "work_group" ? "checked":"")."><label for='report_style_1'>업무그룹 리포트</label>
				<input type='radio' name='report_style' id='report_style_2'  value='date' ".($report_style == "date" ? "checked":"")."><label for='report_style_2'>일별 리포트</label>
				<input type='radio' name='report_style' id='report_style_3'  value='personal' ".($report_style == "personal" ? "checked":"")."><label for='report_style_3'>부서/담당자별 리포트</label>
		      </td>	
		    </tr>
		    <tr height=29>
				<td class='input_box_title'>업무 그룹 </td>
				<td class='input_box_item'>
				".getWorkGroupInfoSelect('parent_group_ix', '1 차그룹',$parent_group_ix, $parent_group_ix, "select", 1, " onChange=\"loadWorkGroup(this,'group_ix')\" ")."
				".getWorkGroupInfoSelect('group_ix', '2 차그룹',$parent_group_ix, $group_ix, "select", 2)."
				
				</td>		  
				<td class='input_box_title'>담당자 : </td>
				<td class='input_box_item' ><div style='display:none;'>".workCompanyList($admininfo["company_id"])."</div>
					".makeDepartmentSelectBox($mdb,"department",$department,"select","부서", "onchange=\"loadWorkUser(this,'charger_ix')\"")."
					".workCompanyUserList($admininfo["company_id"], 'charger_ix', $department, $charger_ix)."							
				</td>
			</tr>
		    <tr height=29>
		      <td class='input_box_title'>조건검색 </td>
		      <td class='input_box_item' colspan=3>
				<table cellpadding=0 cellspacing=0>
				<tr>
				<td style='padding:0px 4px 0px 0px'>
					<select name=search_type>						
					<option value='work_title' ".CompareReturnValue("mobile",$search_type,"selected").">업무내용 + 업무상세</option>
					<option value='charger' ".CompareReturnValue("user_name",$search_type,"selected").">담당자명</option>
					</select>
				</td>
				<td>
					<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:250px;padding:0px;' >
				</td>
				</tr>
				</table>
			  </td>			
		    </tr>
		   <tr height=29>
				<td class='input_box_title'> 업무상태 : </td>
				<td class='input_box_item' colspan=3>
				";
				$mstring .= "<input type='checkbox' name='work_status[]' id='work_status_' value='".($key)."' ><label for='work_status_' style='width:55px;'>전체업무</label>";

				foreach($work_status  as $key => $value){
					$mstring .= "<input type='checkbox' name='work_status[]' id='work_status_".($key)."' value='".($key)."' ".CompareReturnValue($key,$_GET["work_status"],' checked')."><label for='work_status_".($key)."' style='padding-right:10px;'>".$value."</label>";
				}
			

			$mstring .= "
				</td>
				
			  </tr>
		    ";

$selectd_date = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2),substr($selectd_date,6,2),substr($selectd_date,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-1,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-2,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($selectd_date,4,2)-3,substr($selectd_date,6,2)+1,substr($selectd_date,0,4)));	

 $mstring .= " 
		    <tr height=29>
		      <td class='input_box_title'><label for='report_date'>보고일자</label><input type='checkbox' name='report_date' id='report_date' value='1' onclick='ChangeReportDate(document.searchmember);' ".CompareReturnValue("1",$_GET[report_date],"checked")."></td>
		      <td class='input_box_item' colspan=3 >
		      	<table cellpadding=0 cellspacing=2 border=0 width='100%' bgcolor=#ffffff>		
					<tr>					
						<TD width=80 nowrap>
						<input type='text' name='sdate' class='textbox' style='width:80px;text-align:center;padding:0px;' id='start_datepicker' value='".$_GET[sdate]."'>
						</TD>
						<TD width=20 align=center> ~ </TD>
						<TD width=80 nowrap>
						<input type='text' name='edate' class='textbox' style='width:80px;text-align:center;padding:0px;' id='end_datepicker' value='".$_GET[edate]."'>
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
		    </table>
		
	";

	return $mstring;
}
/*

CREATE TABLE `shop_sms_group` (
  `sg_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_name` varchar(50) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}

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