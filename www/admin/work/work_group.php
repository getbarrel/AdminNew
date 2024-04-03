<?
include("../class/layout.work.class");
include("work.lib.php");

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
//$voneweekafter = date("Ymd", time()-84600*6);
$vtwoweekafter = date("Ymd", time()+84600*14);
$vfourweekafter = date("Ymd", time()+84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
$voneweekafter = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
$v15after = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
$vfourweekafter = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthafter = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthafter = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthafter = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));


$db = new Database;

//$db->query("SELECT * FROM ".TBL_BBS_MANAGE_CONFIG." where bm_ix ='$bm_ix' ");
//$db->fetch();
$board_name = $db->dt[board_name];
$board_ename = $db->dt[board_ename];

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("업무 그룹 관리", "마케팅지원 > 업무 그룹 관리 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 그룹 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' style='margin-top:3px;'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15%'>
	  <col width='35%'>
	  <tr>
	    <td class='input_box_title'> 그룹 타입 : </td>
	    <td class='input_box_item' colspan=3 >
	    	<input type=radio name='group_depth' value='1' id='group_depth_1' onclick=\"document.getElementById('parent_group_ix').disabled=true;\" checked><label for='group_depth_1'>1차그룹</label>
	    	<input type=radio name='group_depth' value='2' id='group_depth_2' onclick=\"document.getElementById('parent_group_ix').disabled=false;\" ><label for='group_depth_2'>2차그룹</label>
	    	".getFirstDIV()." <span class='small'>2차 그룹 등록하기 위해서는 반드시 1차그룹를 선택하셔야 합니다.</span>
	    </td>
	  </tr>
	  <tr>
	    <td class='input_box_title'>프로멕트명(그룹명) : </td>
		<td class='input_box_item' colspan=3 ><input type=text class='textbox' name='group_name' value='".$db->dt[group_name]."' style='width:230px;margin-left:2px;'> <span class=small></span></td>
	  </tr>
	  <tr height=27>
		  <td class='search_box_title'><b>프로젝트 계약기간</b></td>
		  <td class='search_box_item' colspan=3 >
			<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
				<col width=70>
				<col width=20>
				<col width=70>
				<col width=*>
				<tr>
					<TD nowrap>
					<input type='text' name='contract_sdate' class='textbox' value='".$contract_sdate."' style='height:20px;width:70px;text-align:center;' id='contract_sdate'>
					</TD>
					<TD align=center> ~ </TD>
					<TD nowrap>
					<input type='text' name='contract_edate' class='textbox' value='".$contract_edate."' style='height:20px;width:70px;text-align:center;' id='contract_edate'>
					</TD>
					<TD style='padding:0px 10px'>
						<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a> 
						<a href=\"javascript:setSelectDate('$today','$voneweekafter',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
						<a href=\"javascript:setSelectDate('$today','$v15after',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
						<a href=\"javascript:setSelectDate('$today','$vonemonthafter',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
						<a href=\"javascript:setSelectDate('$today','$v2monthafter',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
						<a href=\"javascript:setSelectDate('$today','$v3monthafter',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
					</TD>
				</tr>
			</table>
		  </td>
	  </tr>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'><b> 프로젝트 매니저 (PM) </b></td>
		<td class='input_box_item' colspan=3>
			<table cellpadding=0 cellspacing=0>
			<col width='*'>
			<col width='30px'>
			<col width='20px'>
			<col width='130px'>
			<tr>
				<td>
				".workCompanyList($admininfo["company_id"])."
				".makeDepartmentSelectBox($db,"department",$department,"select","부서", "validation=true title='팀' onchange=\"loadWorkUser(this,'charger_ix')\"")."
				".workCompanyUserList($admininfo["company_id"],"charger_ix",$department, $charger_ix," style='width:150px;'")."
				</td>
				<td align=center><img src='../images/orange/cowork.gif' align=absmiddle style='display:inline;'></td>
				<td align=center><input type='checkbox' name='co_charger_yn' id='co_charger_yn' value='Y' onclick=\"$('#co_charger_area').toggle();\" ".($db->dt[co_charger_yn] == "Y" ? "checked":"")." align=absmiddle></td>
				<td><label for='co_charger_yn'>협업자 있음</label></td>
			</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor='#ffffff' id='co_charger_area' ".($db->dt[co_charger_yn] == "Y" ? "":"style='display:none;'")." >
		<td class='input_box_title'><b> 프로젝트 팀원 : </b></td>
		<td class='input_box_item' style='padding:5px 0 5px 87px ' colspan=3>
			<table>
			<tr>
				<td>
			".makeDepartmentSelectBox($db,"co_department",$co_department,"select","협력부서", "multiple='true' ".$readonly_str." style='height:100px;border:1px solid silver;' onchange=\"loadWorkUser(this,'co_charger_ix','".$wl_ix."')\"")."
			".workCompanyUserList($admininfo["company_id"],"co_charger_ix[]",$co_department, $co_charger_ix, "multiple='true' ".$readonly_str." style='width:200px;height:100px;border:1px solid silver;'")."
				</td>
			</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'>노출순서 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:50px;'> <span class=small>노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다</span></td> 
	    <td class='input_box_title'>프로젝트 : </td>
		<td class='input_box_item'><input type=checkbox name='is_project' id='is_project' value='1' > <label for='is_project'>프로젝트 설정</label><span style='width:50px;'></span>
			<span class=small>프로젝트 설정시 하부 공정단계를 구성 하실 수 있습니다.</span></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'>프로젝트 완료여부 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='is_complete' value='1' id='is_complete_1' checked><label for='is_complete_1'>완료</label>
	    	<input type=radio name='is_complete' value='0' id='is_complete_0' ><label for='is_complete_0'>진행중</label>
	    </td>
		 <td class='input_box_title'>사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' id='disp_1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' id='disp_0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 그룹명을  입력해주세요
	</td>
</tr>
</table>
";
*/

$title = "<table border=0 width=100%>
			<col width=''>
			<col width='*'>
			<col width='10'>
			<col width='130'>
			<tr>
				<td><div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u>  그룹 목록</b></div></td>
				<td></td>
				<td><input type='checkbox' name='view_all_group' id='view_all_group' onclick='ToggleAllGroup()' ".($_COOKIE[view_all_group] == 1 ? "checked":"")." ></td>
				<td style='vertical-align:middle;'><label for='view_all_group'> 사용하지 않는 분류포함</label></td>
			</tr>
		</table>";

$innerview = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%",$title)."</td>
	  </tr>
	 
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td' style='width:150px;'> 그룹명</td>
	    <td class='m_td' style='width:70px;'> 노출순서</td>
	    <td class='m_td' style='width:100px;'> 사용유무</td>
	    <td class='m_td' style='width:150px;'> 등록일자</td>
	    <td class='e_td' style='width:200px;'> 관리</td>
	  </tr>";
if($_COOKIE["view_all_group"] == "1"){
	//$where .= " and (wg.disp = '".$_COOKIE["view_all_group"]."' )  ";
}else{
	$where .= " and (wg.disp = '1' )  ";
}

$sql = "select wg.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order 
			, case when group_depth = 1 then group_ix  else vieworder+10000 end as vieworder2 
			from work_group wg where company_id ='".$admininfo["company_id"]."' 
			$where 
			order by  group_order asc , vieworder2 asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.group_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by group_ix
		order by group_order asc, group_depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[group_depth])."></td>
						<td width='*' align='left'>".($db->dt[is_project] == '1' ? "<b>[P]</b>":"")." ".$db->dt[group_name]." </td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td list_bg_gray'><a href='work_group.act.php?act=change_disp&group_ix=".$db->dt[group_ix]."&disp=".($db->dt[disp] == "1" ? "0":"1")."' target=iframe_act>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</a></td>
		    <td class='list_box_td'>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
		    	".($db->dt[is_project] == '1' ? "<b><a href='work_project_architecture.php?group_ix=".$db->dt[group_ix]."'>공정단계설정</a></b>":"")."
				<a href=\"javascript:updateGroupInfo('".$db->dt[group_ix]."','".$db->dt[group_name]."','".$db->dt[disp]."','".$db->dt[vieworder]."','".$db->dt[is_project]."','".$db->dt[group_depth]."','".$db->dt[parent_group_ix]."','".$db->dt[contract_sdate]."','".$db->dt[contract_edate]."')\"><img src='../image/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[group_ix]."')\"><img src='../image/btc_del.gif' border=0></a>
		    </td>
		  </tr>  ";
	}
}else{
	$innerview .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 그룹가 없습니다. </td>
		  </tr>  ";
}
$innerview .= "
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

	  </table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px;'><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' cellpadding=0 cellspacing=0 border=0>";
$Contents = $Contents."<form name='group_form' action='work_group.act.php' method='post' onsubmit='return CheckValue(this)' target=act style='display:inline;'><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='bm_ix' type='hidden' value='$bm_ix'><input name='group_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td id='result_area'>".$innerview."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업무 그룹 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>그룹명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면
		됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >프로젝트 설정을 클릭하실 경우 하부 공정단계를 설정하여 업무를 보다 효율적으로 관리 하실 수 있습니다. </td></tr>

	</table>
	";


	$help_text = HelpBox("업무 그룹 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "<script language='javascript' src='work.js'></script>\n<script language='javascript' src='work.group.js'></script>";
 $Script .= "
 <script language='javascript'>
 $(function() {
	$(\"#contract_sdate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#contract_edate').val() != '' && $('#contract_edate').val() <= dateText){
			$('#contract_edate').val(dateText);
		}else{
			$('#contract_edate').datepicker('setDate','+90d');
		}
	}

	});

	$(\"#contract_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#contract_edate').timepicker();
});

function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#contract_sdate\").val(FromDate);
	$(\"#contract_edate\").val(ToDate);
}


</script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 업무 그룹관리";
	$P->title = "그룹관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else if($mmode == "inner_list"){
	echo $innerview;
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "업무관리 > 업무 그룹관리";
	$P->title = "그룹관리";
	$P->footer_menu = footMenu()."".footAddContents();
	$P->strContents = $Contents;
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