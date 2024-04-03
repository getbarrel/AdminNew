<?
include("../class/layout.work.class");
include("work.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");

$db = new Database;

if($wr_ix != ""){
	$db->query("select * from work_report wr, common_member_detail cmd where wl_ix ='$wl_ix' and wr_ix ='$wr_ix' and wr.charger_ix = cmd.code order by wr.regdate desc   ");		
	$db->fetch();

	$report_desc = $db->dt[report_desc];
	$report_title = $db->dt[report_title];
	$act = "report_update";

}else{
	$act = "report_insert";
}

/*
<html>
<title>메일보내기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
	
<script language='JavaScript' src='../js/admin.js'></Script>
<script language='JavaScript' src='/admin/js/auto.validation.js'></Script>
*/


$Script = "<script language='JavaScript' src='../webedit/webedit.js'></script>
<script type='text/javascript' src='work.js'></script>
<script language='JavaScript' >
function CheckMail(frm){
	if(CheckFormValue(frm)){
		frm.report_desc.value = iView.document.body.innerHTML;	
		return true;
	}else{
		return false;
	}
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');	
		return false;
	}
}

$(document).ready(function() {
	Init(document.send_mail);
	iView.document.body.innerHTML = document.send_mail.b_report_desc.value;
	
});
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	
	<tr><form name='send_mail' method='post' action='work.act.php' onsubmit='return CheckMail(this);' target='act'>
	<input type=hidden name='act' value='".$act."' >
	<input type=hidden name='report_desc' value='' >
	<input type=hidden name='wl_ix' value='".$_GET["wl_ix"]."' >
	<input type=hidden name='wr_ix' value='".$_GET["wr_ix"]."' >
		<td align=center style='padding:5px 10px 5px 10px'>
		<table width=100% cellpadding=0 cellspacing=0>
			<col width='width='13%'>
			<col width='width='*'>
			";

if($act == "report_insert"){
$Contents .= "
			<tr bgcolor=#efefef>
				<td class='input_box_title'  >	<b>보고서 종류 : </b> </td>
				<td class='input_box_item'>
					<table >
						<tr>						
							<td >
								<select name=report_type onchange='ChangeSheet(this.value);' title='보고서 종류' validation=true >				
									<option value=''>보고서 종류를 선택해주세요</option>
									<option value=1>기안서</option>
									<option value=0>일반업무보고</option>
									<!--option value=2>휴가계</option-->
									<option value=9>일일업무보고서(신규)</option>
									<!--option value=3>일일업무보고서</option-->
									<option value=4>주간업무보고서</option>
									<option value=5>월업무보고서</option>
									<option value=6>경비신청서</option>
									<option value=7>출장신청서</option>
									<option value=8>회의록</option>
									
									<option value=99>공지사항</option>
								</select>							
							</td>
							<td class=small> * 일반 내용을 입력 하시고자 하시면 보고서 선택없이 작성하시면 됩니다.</td>
						</tr>
					</table>			
				</td>
			</tr>";
		}			

$Contents .= "
			<tr height=25>
				<td class='input_box_title'><b>보고서 제목 :</b> </td>
				<td class='input_box_item'><input type=text name='report_title' class='textbox'  value='".$report_title."' title='보고서 제목' validation=true style='height:25px;width:100%;'></td>
			</tr>
		</table>
		</td>
	</tr>
	<!--tr>
		<td style='padding:0 10 0 10'>
		<select name='mails[]' style='height:60px;width:100%' multiple>
		
		</select>
		</td>
	</tr-->
	
	<tr>
		<td align=center style='padding:0 10px 0 10px;height:500px;'>
<textarea  name='b_report_desc' style='display:none;' >".$report_desc."</textarea>
".miniWebEdit("..","600px")."
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0 0 0'>
			<table>
				<tr>
					<td><input type='checkbox' name='close_yn' id='close_yn' value='Y'><label for='close_yn'>저장후 창닫기</label></td>
					<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0> <!--<a href='javascript:self.close();'><img src='../image/close.gif' border=0 align=absmiddle></a>--></td>
				</tr>
			</table>
		</td>
	</tr></form>
</TABLE>";






if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = "".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 보고서 등록/수정";
	$P->strContents = $Contents;
	$P->NaviTitle = "보고서 등록/수정";
	$P->prototype_use = false;

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "".$Script;
	$P->strLeftMenu = work_menu();
	$P->Navigation = "HOME > 업무관리 > 보고서 등록/수정";
	$P->title = "보고서 등록/수정";
	$P->strContents = $Contents;
	$P->footer_menu = footMenu()."".footAddContents();
	$P->prototype_use = false;

	echo $P->PrintLayOut();
}
