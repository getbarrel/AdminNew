<?
include("../class/layout.work.class");
include("work.lib.php");

$db = new Database;
$mdb = new Database;

$sql = 	"SELECT wl.*, wg.group_depth , case when wg.group_depth = 1 then wg.group_ix else parent_group_ix end as parent_group_ix , cmd.department
				FROM work_list wl, work_group wg , common_member_detail cmd
				where wl_ix ='$wl_ix' and wl.group_ix = wg.group_ix and wl.charger_ix = cmd.code ";

//echo $sql;
$db->query($sql);
$db->fetch();

if($db->total){
	$act = "update";
	$sdate = $db->dt[sdate];
	$dday = $db->dt[dday];
	$parent_group_ix = $db->dt[parent_group_ix];

	$department = $db->dt["department"];
	$charger_ix = $db->dt[charger_ix];
	$is_schedule = $db->dt[is_schedule];
	$is_hidden = $db->dt[is_hidden];

	$stime = $db->dt[stime];
	$dtime = $db->dt[dtime];
	$group_ix = $db->dt[group_ix];

	$sql = 	"SELECT  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cr.charger_ix
				FROM work_charger_relation cr, common_member_detail cmd  
				where cr.charger_ix = cmd.code and wl_ix ='$wl_ix'  ";

	//echo $sql;
	$db->query($sql);
	$co_charger_data_rows = $db->getrows();
	$co_charger_name = $co_charger_data_rows[0];
	$co_charger_ix = $co_charger_data_rows[1];
	if($co_charger_ix == ""){
		$co_charger_ix = array();
	}
	//print_r($co_charger_ix);

	if($charger_ix != $admininfo[charger_ix]){
		//$readonly_str = " disabled ";
	}
	//if(in_array($admininfo[charger_ix] , $co_charger_ix)){

	//}
	WorkHistory($mdb, $wl_ix, $admininfo[charger_ix], "R", "업무 변경 화면 ");

}else{
	//WorkHistory($mdb, $wl_ix, $admininfo[charger_ix], "R", "업무 등록 화면 ");

	$act = "insert";
	$sdate = $sdate;
	if($dday){
		$dday = $dday;
	}else{
		$dday = $sdate;
	}
	if($group_ix){
		$sql = "select parent_group_ix from work_group wg where group_ix = '".$group_ix."' and group_depth = '2' ";
		//echo $sql;
		$db->query($sql);
		if($db->total){
			$db->fetch();
			//echo $db->dt[parent_group_ix];
			$parent_group_ix = $db->dt[parent_group_ix];
		}else{
			$parent_group_ix = $group_ix;
		}
	}else{
		$parent_group_ix = "11";
	}
	$charger_ix = $admininfo[charger_ix];
	$department = $admininfo["department"];
}

//print_r($admininfo);

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>";
if($mmode == ""){
$Contents01 .= "
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("업무 등록/수정", "업무관리 > 업무 등록/수정 ")."</td>
	  </tr>";
}
$Contents01 .= "
	  <tr >
	    <td align='left' colspan=4>";
if($mmode == "" && false){
$Contents01 .= "
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>

							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php'\">업무  관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_group.php'\" >업무 그룹관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_add.php'\">업무 등록/수정</td>
								<th class='box_03'></th>
							</tr>
							</table>

				<td class='btn'>

				</td>
			</tr>
			</table>
		</div>";
}
$Contents01 .= "
		<div class='t_no' style='margin: 0px 0px 0px; border-top: solid 3px #c6c6c6; '>
			<!-- my_movie start -->
			<div class='my_box' >
				<form name='div_form' action='work.act.php' method='post' onsubmit='return CheckFormValue(this)'  target='act' style='display:inline;'> <!--CheckForm-->
				<input name='act' type='hidden' value='$act'>
				<input name='mmode' type='hidden' value='".$mmode."'>
				<input name='wl_ix' type='hidden' value='$wl_ix'>
				<input name='company_id' type='hidden' value='".$_SESSION['admininfo']['company_id']."'>
				<table width=99% cellpadding=2 bgcolor='#c0c0c0' cellspacing=1 border=0 class='input_table_box'>
					<col width=140>
					<col width=*>
					<tr bgcolor=#ffffff >
				    <td class='input_box_title'><b>  그룹 : </b></td>
				    <td class='input_box_item' colspan=3>
				    	".getWorkGroupInfoSelect('parent_group_ix', '1 차그룹',$parent_group_ix, $parent_group_ix, "select", 1, " ".$readonly_str." validation=true title='업무그룹' onChange=\"loadWorkGroup(this,'group_ix')\" ")."
				    	".getWorkGroupInfoSelect('group_ix', '2 차그룹',$parent_group_ix, $group_ix, "select", 2)."
						<span class=small>업무을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.</span>
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 담당자 : </b></td>
				    <td class='input_box_item' colspan=3>
						<table>
						<tr>
							<td>
							".workCompanyList($admininfo["company_id"])."
							".makeDepartmentSelectBox($mdb,"department",$department,"select","부서", "validation=true title='팀' onchange=\"loadWorkUser(this,'charger_ix')\"")."
							".workCompanyUserList($admininfo["company_id"],"charger_ix",$department, $charger_ix," style='width:150px;'")."
							</td>
							<td><img src='../images/orange/cowork.gif' align=absmiddle style='display:inline;'></td>
							<td><input type='checkbox' name='co_charger_yn' id='co_charger_yn' value='Y' onclick=\"$('#co_charger_area').toggle();\" ".($db->dt[co_charger_yn] == "Y" ? "checked":"")." align=absmiddle></td>
							<td><label for='co_charger_yn'>협업자 있음</label></td>
						</tr>
						</table>
				    </td>
				  </tr>
				  <tr bgcolor='#ffffff' id='co_charger_area' ".($db->dt[co_charger_yn] == "Y" ? "":"style='display:none;'")." >
				    <td class='input_box_title'><b> 협업자 : </b></td>
				    <td class='input_box_item' style='padding:5px 0 5px 87px ' colspan=3>
						<table>
						<tr>
							<td>
						".makeDepartmentSelectBox($mdb,"co_department",$co_department,"select","협력부서", "multiple='true' ".$readonly_str." style='height:100px;border:1px solid silver;' onchange=\"loadWorkUser(this,'co_charger_ix','".$wl_ix."')\"")."
					    ".workCompanyUserList($admininfo["company_id"],"co_charger_ix[]",$co_department, $co_charger_ix, "multiple='true' ".$readonly_str." style='width:200px;height:100px;border:1px solid silver;'")."
							</td>
						</tr>
						</table>
				    </td>
				  </tr>

				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 시작날짜 : </b></td>
				    <td class='input_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0>
							<tr>
								<td width=87 style='padding:0px 7px 0px 0px;'>
								<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:80px;text-align:center;' id='start_datepicker' validation=true title='시작날짜'>
								</td>
								<td width=70 nowrap>
								<select name='shour'>";
					if($db->dt[stime]){
						$stime = split(":",$db->dt[stime]);
						$stime_str = $stime[0];
					}else{
						$stime = split(":",$_GET[stime]);
						$stime_str = $stime[0];
					}
					for($i=0;$i < 24;$i++){
						$Contents01 .= "<option value='".str_pad($i,2,"0",STR_PAD_LEFT)."' ".($stime_str == str_pad($i,2,"0",STR_PAD_LEFT) ? "selected":"").">".str_pad($i,2,"0",STR_PAD_LEFT)."</option>";
					}

					$Contents01 .= "</select> 시
								</td>
								<td width=70>
								<select name='sminute'>";
					for($i=0;$i < 6;$i++){
						$Contents01 .= "<option value='".(str_pad($i*10,2,"0",STR_PAD_LEFT))."' ".($stime[1] == str_pad($i*10,2,"0",STR_PAD_LEFT) ? "selected":"").">".(str_pad($i*10,2,"0",STR_PAD_LEFT))."</option>";
					}

					$Contents01 .= "</select> 분
								</td>
								<td style='padding:0px 0px 0px 10px'>
								<table>
								<tr>
									<td width=10><img src='../images/orange/calendar.gif' align=absmiddle style='display:inline;'></td>
									<td width=10><input type='checkbox' name='is_schedule' id='is_schedule' value='1' ".($is_schedule == "1" ? "checked":"")." onclick=\"$('#where_area').toggle();\"></td>
									<td width=45><label for='is_schedule'> 스케줄</label></td>
									<td class=small>(스케줄로 등록된 업무에 대해서만 캘린더에 노출되게 됩니다.)</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 작업기한 : </b></td>
				    <td class='input_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0>
							<tr>
							<td width=87 style='padding:0px 7px 0px 0px;'>
							<input type='text' name='dday' class='textbox' value='".$dday."' style='width:80px;text-align:center;' id='end_datepicker' validation=true title='작업기한'>
							</td>
							<td width=70>
							<select name='dhour'>";
				//$dtime = split(":",$db->dt[dtime]);
				if($db->dt[dtime]){
					$dtime = split(":",$db->dt[dtime]);
					$dtime_str = $dtime[0];
				}else{
					if($dtime){
						$dtime = split(":",$_GET[dtime]);
						$dtime_str = $dtime[0];
					}else{
						if($stime = '00'){
							$dtime_str = $stime;
						}else{
							$dtime_str = $stime+1;
						}
					}
				}
				for($i=0;$i < 24;$i++){
					$Contents01 .= "<option value='".str_pad($i,2,"0",STR_PAD_LEFT)."' ".($dtime_str == str_pad($i,2,"0",STR_PAD_LEFT) ? "selected":"").">".str_pad($i,2,"0",STR_PAD_LEFT)."</option>";
				}

				$Contents01 .= "</select> 시
							</td>
							<td>
							<select name='dminute'>";
				for($i=0;$i < 6;$i++){
					$Contents01 .= "<option value='".(str_pad($i*10,2,"0",STR_PAD_LEFT))."' ".($dtime[1] == str_pad($i*10,2,"0",STR_PAD_LEFT) ? "selected":"").">".(str_pad($i*10,2,"0",STR_PAD_LEFT))."</option>";
				}

				$Contents01 .= "</select> 분
							</td>
							</tr>
							</table>
				    </td>
				  </tr>
				  <tr bgcolor=#ffffff id='where_area' ".(($db->dt[is_schedule] == "1" || $db->dt[is_schedule] == "") ? "":"style='display:none;'")." >
				    <td class='input_box_title'><b> 장소 : </b></td>
					<td class='input_box_item' colspan=3>
					<input type=text class='textbox' name='work_where' value='".$db->dt[work_where]."' maxlength='20' style='height:22px;width:230px;' > <span class=small>20자 이내로 작성</span></td>
				  </tr>
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 업무내용 : </b></td>
					<td class='input_box_item' colspan=3>
					<!--select name='work_type'>
						<option value=''>업무유형</option>
						<option value=''>제안</option>
						<option value=''>결함</option>
						<option value=''>기획</option>
						<option value=''>제안</option>
					</select-->
					<input type=text class='textbox' name='work_title' value='".$db->dt[work_title]."' maxlength='30' style='height:22px;width:430px;' validation=true title='업무내용'> <span class=small>30자 이내로 작성</span></td>

				  </tr>
				  
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> 업무상세 : </td>
				    <td class='input_box_item'  style='padding:5px;' colspan=3>
					<table width=100% border=0 cellpadding=0 cellspacing=0 >
						<tr>
							<td colspan=2 align=left>
							<textarea  name='work_detail' id='work_detail' ".(($db->dt[work_detail] == "" || substr_count($db->dt[work_detail],"\n") < 20) ? "style='width:98%;height:250px;'":"style='width:100%;height:".(substr_count($db->dt[work_detail],"\n")*20)."px'")."  class='tline'>".($db->dt[is_html] == 1 ?  $db->dt[work_detail]:nl2br($db->dt[work_detail]))."</textarea><br>
							</td>
						</tr>
						<tr>
							<td style='padding-top:5px;'>
							<input type=checkbox name='is_hidden' id='is_hidden' value='1' ".(($db->dt[is_hidden] == "1") ? "checked":"")."><label for='is_hidden'>비공개</label> <img src='../images/key.gif' align=absmiddle style='display:inline;'>
							</td>
							<td align=right>
								<a href='#bt' onClick=\"javascript:TextareaResize('+',$('#work_detail'))\"><img src='../images/btk_s_long.gif' border='0'></a>
								<a href='#bt' onClick=\"javascript:TextareaResize('-',$('#work_detail'))\"><img src='../images/btk_s_short.gif' border='0'></a>
							</td>
						</tr>
						<tr>
							<td colspan=2>
							<input type=checkbox name='is_report' id='is_report' value='1' ".(($db->dt[is_report] == "1" || $db->dt[is_report] == "") ? "checked":"")."><label for='is_report'>작업완료시 나에게 완료보고 보내기</label>
							(<input type=radio name='report_type' value='1' id='report_type_1'  ".(($db->dt[report_type] == "1" || $db->dt[report_type] == "") ? "checked":"")."><label for='report_type_1'>Email</label>
							<input type=radio name='report_type' value='2' id='report_type_2' ".CompareReturnValue("2",$report_type,"checked")."><label for='report_type_2'>SMS</label> )
							</td>
						</td>
						</tr>
					</table>
					</td>
				  </tr>
				  <tr bgcolor=#ffffff>
					<td class='input_box_title'><b> 업무 중요도 : </b></td>
					<td class='input_box_item' >
					<!--select name='importance'>
						<option value=''>해당없음</option>
						<option value='H' ".($db->dt[importance] == "H" ? "selected":"").">상</option>
						<option value='M' ".($db->dt[importance] == "M" ? "selected":"").">중</option>
						<option value='L' ".($db->dt[importance] == "L" ? "selected":"").">하</option>
					</select-->
						<input type=radio name='importance' value='' id='importance_' ".($db->dt[importance] == "" ? "checked":"")."><label for='importance_'>해당없음</label>
						<input type=radio name='importance'  value='E' id='importance_e' ".($db->dt[importance] == "E" ? "checked":"")."><label for='importance_e'>긴급</label>
						<input type=radio name='importance'  value='H' id='importance_h' ".($db->dt[importance] == "H" ? "checked":"")."><label for='importance_h'>상</label>
						<input type=radio name='importance'  value='M' id='importance_m' ".($db->dt[importance] == "M" ? "checked":"")."><label for='importance_m'>중</label>
						<input type=radio name='importance'  value='L' id='importance_l' ".($db->dt[importance] == "L" ? "checked":"")."><label for='importance_l'>하</label>
					</td>
				  
				    <td class='input_box_title'> 업무상태 : </td>
				    <td class='input_box_item' >

					<select name='work_status' onchange=\"WorkStatusSelect(this.value)\">";
				foreach($work_status  as $key => $value){
					$Contents01 .= "<option value='".($key)."' ".($key == $db->dt[status] ? "selected":"").">".($value)."</option>";
				}

				$Contents01 .= "</select>

				    </td>

				  </tr>
				  <!--tr height=1><td colspan=4 background='../image/dot.gif'></td></tr-->";

if($act == "update"){
$Contents01 .= "
				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'> 업무진행율 : </td>
				    <td class='input_box_item' >

					<select name='complete_rate' id='complete_rate'>";
				foreach($work_complet_rate  as $key => $value){
					$Contents01 .= "<option value='".($key)."'  ".($key == $db->dt[complete_rate] ? "selected":"").">".($value)."</option>";
				}

				$Contents01 .= "</select>

				    </td>
				    <td class='input_box_title'> 등록담당자 : </td>
				    <td class='input_box_item' >".$db->dt[reg_name]."</td>
				  </tr>
				  </table>
				  ";
}
if($charger_ix == $admininfo[charger_ix] || $db->dt[reg_charger_ix] == $admininfo[charger_ix] || in_array($admininfo[charger_ix],$co_charger_ix) ||  $act == "insert" ){
	$Contents01 .= "
				  <table width=100% cellpadding=0  cellspacing=0>
						<tr bgcolor=#ffffff >
						<td colspan=4 align=center style='padding:10px 0px;'>";
	$Contents01 .= "	<input type='checkbox' name='is_close' id='is_close' value='1'><label for='is_close'>저장후닫기</label>
								<input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' >";

	if($db->dt[reg_charger_ix] == $admininfo[charger_ix]){
	$Contents01 .= "	<img src='../image/b_del.gif' border=0 onclick=\"DeleteWorkList('".$wl_ix."','".$mmode."')\" style='cursor:pointer;' align=absmiddle>";
	}
	$Contents01 .= "
						</td>
					</tr>";
}

$Contents01 .= "
				  </table>
				  </form>
				</div>

		</div>
	    </td>
	  </tr>

	  </table>";



$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업무을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>비공개</u>업무는  본인 이외의 리스트에 노출되지 않습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("업무 등록 관리", $help_text);

$Contents = "<table width='900' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$help_text."</td></tr>";
$Contents = $Contents."</table >";



 $Script = "
<script  id='dynamic'></script>
<style>
/* css for timepicker */
#ui-timepicker-div dl{ text-align: left; }
#ui-timepicker-div dl dt{ height: 25px; }
#ui-timepicker-div dl dd{ margin: -25px 0 10px 65px; }

</style>
<!--link rel='stylesheet' media='all' type='text/css' href='css/jquery-ui-1.8.custom.css' /-->
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<!--link type='text/css' href='./js/themes/demos.css' rel='stylesheet' /-->
<script language='JavaScript' src='/include/ckeditor/ckeditor.js'></script>
<script language='javascript'>


$(document).ready(function() {

CKEDITOR.replace('work_detail',{
		docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
		font_defaultLabel : '굴림',
		font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
		fontSize_defaultLabel : '12px',
		fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
		language :'ko',
		resize_enabled : false,
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		startupFocus : false,
		uiColor : '#EEEEEE',
		toolbarCanCollapse : false,
		menu_subMenuDelay : 0,
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About']],
		filebrowserImageUploadUrl : '/include/ckeditor/upload.php',
		height:500});

});

/*
document.onkeydown = ctrlSave;

function ctrlSave() {
	alert(1);
}
*/
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
		if($('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	//$('#start_timepicker').timepicker();


	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});
$().ready(function() {
//	$(\"#work_detail\").focus().autocomplete(cities);

	$('#work_detail').keypress(function(event) {

	  if (event.which == '13') {
		 //event.preventDefault();
		 //alert(parseInt($('#work_detail').css('height').replace('px',''))+20);
		 $('#work_detail').css('height',parseInt($('#work_detail').css('height').replace('px',''))+20);
	   }

	});

});

function showTabContents(vid, tab_id){
	var area = new Array('mailling_insert_form','mailling_search_form');
	var tab = new Array('tab_01','tab_02');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}

}

 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;

 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

function CheckFormBackup(frm){
	if(frm.parent_group_ix.value == ''){
	 		alert('1차 그룹은 반드시 선택하셔야 합니다.');
	 		return false;
 	}

	if(frm.charger_ix.value.length < 1){
		alert('담당자를 입력해주세요');
		frm.charger_ix.focus();
		return false;
	}

	if(frm.sdate.value.length < 1){
		alert('시작 날짜를 입력해주세요');
		frm.sdate.focus();
		return false;
	}

	if(frm.dday.value.length < 1){
		alert('작업완료 기한을 입력해주세요');
		frm.dday.focus();
		return false;
	}

	if(frm.work_title.value.length < 1){
		alert('업무내용을 입력해주세요');
		frm.work_title.focus();
		return false;
	}
	//else{
	/*
		var PT_email = /[a-z0-9_]{2,}@[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일
		if (!PT_email.test(frm.email.value)){
			alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
			frm.email.focus();
			return false;
		}

	}*/
	$.blockUI.defaults.css = {};
	$.blockUI({ message: $('#loading'), css: {  left:'40%', top:'40%', width: '10px' , height: '10px' ,padding:  '10px'} });
	//setTimeout($.unblockUI, 2000);
	return true;
}


 </script>
 ";

if($mmode == "pop" || $mmode == "weelky_pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.2.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<!--script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script-->
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 업무 등록/수정";
	$P->strContents = $Contents;
	$P->NaviTitle = "업무 등록/수정";
	$P->prototype_use = false;

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "<!--script type='text/javascript' src='./js/jquery-1.4.2.min.js'></script-->
<script type='text/javascript' src='./js/ui/ui.core.js'></script>
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<!--script type='text/javascript' src='./js/ui/jquery-ui-timepicker-addon-0.5.js'></script-->
<script type='text/javascript' src='work.js'></script>".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 업무 등록/수정";
	$P->title = "업무 등록/수정";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	$P->prototype_use = false;

	echo $P->PrintLayOut();
}

?>