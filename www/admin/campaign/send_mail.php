<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("./mail.config.php");

$db = new Database;
$mdb = new Database;
$sdb = new Database;
$sms_design = new SMS;

$update_kind = "sendemail";
$page_title = "메일 개별/대량발송";
$page_navigation = "메일링/SMS 발송관리 > 메일 개별/대량발송";

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

	//검색 1주일단위 디폴트
	if ($startDate == ""){
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");

	}

	$where = " where ab_ix != '' and ab.group_ix = abg.group_ix and ab.company_id = '".$admininfo[company_id]."'";
	
	//1차그룹2차그룹 검색
	if($search_parent_group_ix != "" && $search_group_ix == ""){
		$where .= " and (abg.group_ix = '".$search_parent_group_ix."' or abg.parent_group_ix = '".$search_parent_group_ix."') ";
	}else if($search_parent_group_ix != "" && $search_group_ix != ""){
		$where .= " and abg.parent_group_ix = '".$search_parent_group_ix."' ";
	}
	
	//1차그룹 검색
	if($search_group_ix != ""){
		$where .= " and abg.group_ix = '".$search_group_ix."' ";
	}
	
	//가입/등록일 검색 
	if($startDate){
		$where .= "and ab.regdate between '$startDate 00:00:00' and '$endDate 23:59:59' ";
	}

	//메일수신여부 검색
	if(is_array($mail_yn)){
		for($i=0;$i < count($mail_yn);$i++){
			if($mail_yn[$i] != ""){
				if($mail_yn_str == ""){
					$mail_yn_str .= "'".$mail_yn[$i]."'";
				}else{
					$mail_yn_str .= ",'".$mail_yn[$i]."' ";
				}
			}
		}

		if($mail_yn_str != ""){
			$where .= " AND mail_yn in ($mail_yn_str) ";
		}
	}else{
		if($mail_yn){
			$where .= " AND mail_yn = '$mail_yn' ";
		}
	}
	
	//가입여부 검색
	if(is_array($mbjoin)){
		for($i=0;$i < count($mbjoin);$i++){
			if($mbjoin[$i] != ""){
				if($mbjoin_str == ""){
					$mbjoin_str .= "'".$mbjoin[$i]."'";
				}else{
					$mbjoin_str .= ",'".$mbjoin[$i]."' ";
				}
			}
		}

		if($mbjoin_str != ""){
			$where .= " AND mbjoin in ($mbjoin_str) ";
		}
	}else{
		if($mbjoin){
			$where .= " AND mbjoin = '$mbjoin' ";
		}
	}

	//SMS수신여부 검색
	if(is_array($sms_yn)){
		for($i=0;$i < count($sms_yn);$i++){
			if($sms_yn[$i] != ""){
				if($sms_yn_str == ""){
					$sms_yn_str .= "'".$sms_yn[$i]."'";
				}else{
					$sms_yn_str .= ",'".$sms_yn[$i]."' ";
				}
			}
		}

		if($sms_yn_str != ""){
			$where .= " AND sms_yn in ($sms_yn_str) ";
		}
	}else{
		if($sms_yn){
			$where .= " AND sms_yn = '$sms_yn' ";
		}
	}
	
	//성별검색 
	if(is_array($sex)){
		for($i=0;$i < count($sex);$i++){
			if($sex[$i] != ""){
				if($sex_str == ""){
					$sex_str .= "'".$sex[$i]."'";
				}else{
					$sex_str .= ",'".$sex[$i]."' ";
				}
			}
		}

		if($sex_str != ""){
			$where .= " AND sex in ($sex_str) ";
		}
	}else{
		if($sex){
			$where .= " AND sex = '$sex' ";
		}
	}

	//회원구분
	if(is_array($mem_type)){
		for($i=0;$i < count($mem_type);$i++){
			if($mem_type[$i] != ""){
				if($mem_type_str == ""){
					$mem_type_str .= "'".$mem_type[$i]."'";
				}else{
					$mem_type_str .= ",'".$mem_type[$i]."' ";
				}
			}
		}

		if($mem_type_str != ""){
			$where .= " AND mem_type in ($mem_type_str) ";
		}
	}else{
		if($mem_type){
			$where .= " AND mem_type = '$mem_type' ";
		}
	}
	
	//조건검색
	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	// 전체 갯수 불러오는 부분
	$sql = "SELECT count(*) as total FROM shop_addressbook ab, shop_addressbook_group abg  $where ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&region=$region&gp_level=$gp_level&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");

	$sql = "SELECT ab.*, abg.group_name, abg.group_depth, abg.parent_group_ix FROM shop_addressbook ab, shop_addressbook_group abg   $where  order by ab.regdate desc LIMIT $start, $max";
	//echo $sql;
	$db->query($sql);

$Script = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>
<script language='javascript'>
function init(){
	  CKEDITOR.replace('basicinfo',{
	  startupFocus : false,height:500
	  });
}
function loadCampaignGroup(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//alert(target);
	//var depth = sel.depth;
	var depth = sel.getAttribute('depth');
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target);
	//dynamic.src = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	window.frames['act'].location.href = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	//document.location.href='campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

	function BatchSubmit(frm){

	if(frm.update_type.value == 1){
		if(frm.search_searialize_value.value.length < 1){
			alert('적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요');
			return false;
		}

	}else if(frm.update_type.value == 2){
		var ab_ix_checked_bool = false;
		for(i=0;i < frm.ab_ix.length;i++){
			if(frm.ab_ix[i].checked){
				ab_ix_checked_bool = true;
			}
			//	frm.ab_ix[i].checked = false;
		}
		if(!ab_ix_checked_bool){
			alert('선택된 수신자가 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요');
			return false;
		}
	}
	
	if(frm.email_subject.value.length < 1){
		alert('이메일 제목을 입력해주세요');
		frm.email_subject.focus();
		return false;
	}

	if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'|| frm.mail_content.value == '<p>&nbsp;</p>'){ //크롬용
		alert('이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요');
		//frm.mail_content.focus();
		return false;
	}

	if(frm.update_type.value == 1){
		
		if(confirm('검색회원 전체에게 이메일발송을 하시겠습니까?')){return true;}else{return false;}

	} else {
		
			if(confirm('선택한 회원에게 이메일발송을 하시겠습니까?')){return true;}else{return false;}

	}

}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_sendemail','batch_update_receive','batch_update_hotcon','batch_update_group','batch_update_sms');

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('campaign_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function DeleteAddressBook(ab_ix){
	if(confirm('해당 메일링/SMS 목록을 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='addressbook.act.php?act=delete&ab_ix='+ab_ix;
		//document.getElementById('act').src='addressbook.act.php?act=delete&ab_ix='+ab_ix;
	}
}

function LoadEmail(email_type){
	if(email_type == 'new'){
		//$('#email_subject_text').css('display','inline');
		$('#email_select_area').css('display','none');
	}else if(email_type == 'box'){
		//$('#email_subject_text').css('display','none');
		$('#email_select_area').css('display','inline');
	}
}
$(document).ready(function() {
	$('select#email_subject_select').change(function(){
		if($(this).val() != ''){
			$.ajax({
				type: 'GET',
				data: {'act': 'mail_info', 'mail_ix': $(this).val()},
				url: './mail.act.php',
				dataType: 'json',
				async: true,
				beforeSend: function(){

				},
				success: function(mail_info){
					$('#mail_text').val(mail_info.mail_text);
					$('#email_subject_text').val(mail_info.mail_title);
					//alert(mail_info);
					//$('#row_'+wl_ix).slideRow('up',500);
				}
			});
		}
	});
});
</script>";
if($list_type == "addressbook_list"){
	$update_kind = "group";
}
if($update_kind == ""){
	if($before_update_kind){
		$update_kind = $before_update_kind;
	}
	//echo $_COOKIE["update_kind"];
	if($_COOKIE["campaign_update_kind"]){
		$update_kind = $_COOKIE["campaign_update_kind"];
	}else if(!$update_kind){
		$update_kind = "sms";
	}
}

$Contents = "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
  <tr>
    <td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation($page_title, $page_navigation)."</td>
  </tr>";
if($list_type == "addressbook_list"){
$Contents .= "
  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab' width=100%>
					<col width='600'>
					<col width='*'>
					<tr>
						<td class='tab'>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_list.php'\">주소록 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add.php'\">주소록 개별등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add_excel.php'\">주소록 대량등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_01'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_group.php'\" >주소록 그룹관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0;'>
							<!--총건수 :&nbsp;-->
							".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." <b>".number_format($total)."</b>
						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr>";
}
$Contents .= "
  <tr>
  	<td>";
$Contents .= "
<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' >
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top >
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
    <input type='hidden' name=act value='".$act."'>
    <input type='hidden' name=before_update_kind value='".$update_kind."'>";

$Contents .= "
			<tr>
				<td class='search_box_title'>가입 / 등록일</td>
				<td class='search_box_item'  colspan=3>
					".search_date('startDate','endDate',$startDate,$endDate)."
				</td>
		    <tr>
		      <td class='search_box_title'>주소록 그룹 </td>
		      <td class='search_box_item'>
		      ".getCampaignGroupInfoSelect('search_parent_group_ix', '1 차그룹',$search_parent_group_ix, $search_parent_group_ix, 1, " onChange=\"loadCampaignGroup(this,'search_group_ix')\" ")."
				  ".getCampaignGroupInfoSelect('search_group_ix', '2 차그룹',$search_parent_group_ix, $search_group_ix, 2)."
		      <!--".getFirstDIV($mdb, $search_parent_group_ix, 'search_parent_group_ix', "onChange=\"loadCampaignGroup(this,'search_group_ix')\"")."
		      <select name='search_group_ix' id='search_group_ix' >
					<option value=''>1차그룹을 먼저 선택해주세요</option>
					</select-->
		      </td>
		      <td class='search_box_title'>가입여부</td>
		      <td class='search_box_item'>
				 <input type=checkbox name='mbjoin[]' value='1' id='join_o' ".CompareReturnValue("1",$mbjoin,"checked")."><label for='join_o'>회원</label>
				 <input type=checkbox name='mbjoin[]' value='0' id='join_x'  ".CompareReturnValue("0",$mbjoin,"checked")."><label for='join_x'>비회원</label>
			  </td>
		    </tr>
			<tr>
		      <td class='search_box_title'>성별 <img src='".$required3_path."'></td>
		      <td class='search_box_item'>
				<input type=checkbox name='sex[]' value='0' id='sex_male' ".CompareReturnValue("0",$sex,"checked")."><label for='sex_male'>남자</label>
				 <input type=checkbox name='sex[]' value='1' id='sex_female'  ".CompareReturnValue("1",$sex,"checked")."><label for='sex_female'>여자</label>
				 <input type=checkbox name='sex[]' value='2' id='sex_all'  ".CompareReturnValue("2",$sex,"checked")."><label for='sex_all'>기타</label>
		      </td>
			  <td class='search_box_title'>회원구분</td>
		      <td class='search_box_item'>
				 <input type=checkbox name='mem_type[]' value='M' id='mem_type_user' ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_user'>일반회원</label>
				 <input type=checkbox name='mem_type[]' value='C' id='mem_type_biz'  ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_biz'>사업자회원</label>
				 <input type=checkbox name='mem_type[]' value='A' id='mem_type_staff'  ".CompareReturnValue("A",$mem_type,"checked")."><label for='mem_type_staff'>직원</label>
		      </td>
		    </tr>
		    <tr>
		      <td class='search_box_title'>이메일 수신여부 </td>
		      <td class='search_box_item'>
		      <input type=checkbox name='mail_yn[]' value='' id='mailsend_' ".CompareReturnValue("",$mail_yn,"checked")."><label for='mailsend_'>전체</label>
		      <input type=checkbox name='mail_yn[]' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mail_yn,"checked")."><label for='mailsend_y'>수신회원만</label>
		      <input type=checkbox name='mail_yn[]' value='0' id='mailsend_n' ".CompareReturnValue("0",$mail_yn,"checked")."><label for='mailsend_n'>수신거부회원</label>
		      </td>
		       <td class='search_box_title'>SMS 수신여부 </td>
		      <td class='search_box_item'>
		      <input type=checkbox name='sms_yn[]' value='' id='smssend_' ".CompareReturnValue("",$sms_yn,"checked")."><label for='smssend_'>전체</label>
		      <input type=checkbox name='sms_yn[]' value='1' id='smssend_y'  ".CompareReturnValue("1",$sms_yn,"checked")."><label for='smssend_y'>수신회원만</label>
		      <input type=checkbox name='sms_yn[]' value='0' id='smssend_n' ".CompareReturnValue("0",$sms_yn,"checked")."><label for='smssend_n'>수신거부회원</label>
		      </td>
		    </tr>
		    <tr>
			<td class='search_box_title'>조건검색 </td>
		      <td class='search_box_item' colspan='3'>
					<table>
						<tr>
							<td>
							  <select name=search_type>
										<option value='user_name' ".CompareReturnValue("user_name",$search_type,"selected").">성명</option>
										<option value='mobile' ".CompareReturnValue("mobile",$search_type,"selected").">핸드폰번호</option>
										<option value='email' ".CompareReturnValue("email",$search_type,"selected").">이메일</option>
										<optiongroup >=========================</optiongroup>
										<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
										<option value='phone' ".CompareReturnValue("phone",$search_type,"selected").">회사전화</option>
										<option value='fax' ".CompareReturnValue("fax",$search_type,"selected").">회사팩스</option>
										<option value='com_address' ".CompareReturnValue("com_address",$search_type,"selected").">주소</option>
							  </select>
							 </td>
							 <td><input type=text name='search_text' class=textbox value='".$search_text."' style='width:100%' ></td>
						</tr>
					</table>
		      </td>
		     <!-- <td class='search_box_title'><label for='regdate'>등록일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td class='search_box_item' colspan=3 style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
				<tr>
					<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
					<TD style='padding:0 5px;' align=center> ~ </TD>
					<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
					<TD style='padding-left:15px;'>
						<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
						<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
						<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
						<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
						<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
					</TD>
				</tr>
			</table>
		      </td>
		    </tr>
		    <!--tr height=27>
		      <td bgcolor='#efefef' align=center><label for='visitdate'>등록일자</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
		      <td align=left colspan=3  style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
							<tr>
								<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
								<TD style='padding:0 5px;' align=center> ~ </TD>
								<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
								<TD style='padding-left:15px;'>
								<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
								<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
								<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
								</TD>
							</tr>
						</table>
		      </td>
		    </tr>
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr-->

		    </table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>			";

$Contents .= "
    </td>
  </tr>
  <tr height=50>
    	<td style='padding:10px 20px 20px 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  > <!--a href=\"javascript:mybox.service('addressbook_add.php?code_ix=','10','450','600', 4, [], Prototype.emptyFunction, [], '회원관리 > 메일링/SMS대상추가');\">메일링/SMS 대상추가</a--></td>
  </tr></form>
</table>
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='addressbook_list.act.php' target='act'>
<input type='hidden' name='ab_ix[]' id='ab_ix'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<div id='result_area' style='clear:both;'>
<table width='100%' border='0' cellpadding='0' cellspacing='0'  class='list_table_box'>
  <col width='40px'>
  <col width='40px'>
  <col width='80px'>
  <col width='200px'>
  <col width='120px'>
  <col width='100px'>
  <col width='*'>
  <col width='125px'>
  <col width='60px'>
  <col width='60px'>
  <col width='100px'>
  <tr height='28' bgcolor='#ffffff'>
    <td align='center' class='s_td'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td align='center' class='m_td'><font color='#000000'><b>순번</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>가입여부</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>성명/ID</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>핸드폰/전화번호</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>이메일</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
	<td align='center' class='m_td' small'><font color='#000000'><b>SMS</b></font></td>
    <td align='center' class='m_td' small' nowrap><font color='#000000'><b>메일링</b></font></td>
    <td align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";
	

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		
		$no = $total - ($page - 1) * $max - $i;
				
		if($db->dt[group_depth] == 2){
			$mdb->query("SELECT group_name FROM shop_addressbook_group WHERE group_ix  = '".$db->dt[parent_group_ix]."' ");
			$mdb->fetch(0);
			$group_name = $mdb->dt[group_name]." > ".$db->dt[group_name];
		}else{
			$group_name = $db->dt[group_name];
		}
	


$Contents = $Contents."
  <tr height='30' align=center onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'><input type=checkbox name=ab_ix[] id='ab_ix' value='".$db->dt[ab_ix]."'></td>
    <td class='list_box_td list_bg_gray'>".$no."</td>
	 <td class='list_box_td' nowrap>".($db->dt['mbjoin']=="1" ? "O" : "X")."</td>
    <td class='list_box_td point' style='padding:5px;'><span >".$group_name."</span></td>
    <td class='list_box_td  list_bg_gray' >".$db->dt[user_name]."</td>
    <td class='list_box_td' nowrap>".$db->dt[mobile]."</td>
    <td class='list_box_td point' >".$db->dt[email]."</td>
    <td class='list_box_td' nowrap>".$db->dt[regdate]."</td>
    <td class='list_box_td list_bg_gray' >".($db->dt[sms_yn] == "1" ? "수신":"수신거부")."</td>
    <td class='list_box_td' >".($db->dt[mail_yn] == "1" ? "수신":"수신거부")."</td>
    <td class='list_box_td list_bg_gray' nowrap>";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
        $Contents.="
    	<a href=\"javascript:PopSWindow('addressbook_add.php?mmode=pop&ab_ix=".$db->dt[ab_ix]."',880,600,'member_info')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
    }else{
        $Contents.="
    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
    }
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
        $Contents.="
	    <a href=\"javascript:DeleteAddressBook('".$db->dt[ab_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }else{
        $Contents.="
	    <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }
    $Contents.="
	  </td>
  </tr> ";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='11' align='center'>등록된 데이터가 없습니다.</td>
  </tr>";

}

$Contents .= "
</table>
<table width='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr height='40'>
    <td colspan=5 align=left>

    </td>
    <td  colspan='5' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
</table>
</div>";

$help_text = "

<div id='batch_update_sendemail' ".($update_kind == "sendemail" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif' align=absmiddle> <b>email 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 email 을 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span></div>
<input type=hidden name='email_send_page' value='1'>
<table cellpadding=3 cellspacing=0 width=100%  class='input_table_box'>
	<col width=17%>
	<col width=35%>
	<col width=15%>
	<col width=32%>
	<tr>
		<td class='input_box_title'> <b>총 발송 예정수</b></td>
		<td class='input_box_item' colspan=3 >
			<b id='sended_email_cnt' class=blu>0</b> 건 / <b id='remainder_email_cnt'>$total</a> 명
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>발송구분</b></td>
		<td class='input_box_item' colspan=3 >
		<table cellpadding=0>
			<tr>
				<td>
				<input type='radio' name='email_type' id='email_send_now' value='0'><label for='email_send_now'>즉시발송</label>
				<input type='radio' name='email_type' id='email_send_reserve' value='1'><label for='email_send_reserve'>예약발송</label>
				".select_date("date")."
				</td>
				<td>
				<select name='send_time_hour'>
				";	for($i=0;$i<24;$i++){
						$help_text .= "<option value='$i'>$i</option>";
					}
					$help_text .= "
				</select>시
				<select name='send_time_minite'>
					";	for($i=0;$i<60;$i++){
							$help_text .= "<option value='$i'>$i</option>";
						}
					$help_text .= "
				</select>분
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>이메일 발송구분</b></td>
		<td class='input_box_item' colspan=3 >
		<table cellpadding=0>
			<tr>
				<td>
				<input type='radio' name='email_type' id='email_type_tem' value='tem' ".($email_type == "tem" || $email_type == "" ? "checked":"")."><label for='email_type_new'>템플릿</label>
				<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
				<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>최근목록적용</label>
				</td>
			</tr>
			<tr>
				<td colspan=2 id='email_select_area' style='display:none;'>
				".getMailList("","","display:inline;width:250px;")."
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>이메일 제목</b></td>
		<td class='input_box_item' colspan=3 >
		<table cellpadding=0>
			<tr>
				<td><input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px;height:21px;margin:0px;' /></td>

				<td>
				<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
				<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label>
				</td>
			</tr>
			<tr>
				<td colspan=2 id='email_select_area' style='display:none;'>
				".getMailList("","","display:inline;width:250px;")."
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'>
			<b>이메일코드</b>
		</td>
		<td class='input_box_item' style='padding-left:7px;'> 
			<input type=text name='mail_code'  class=textbox value='' style='width:150px;height:21px;'>
		</td>
		<td class='input_box_title'>
			<b>참조사항</b>
		</td>
		<td class='input_box_item' style='padding-left:7px;'> 
			<input type=text name='mail_cc'  class=textbox value='' style='width:150px;height:21px;'>
		</td>
	</tr>
	<!--tr height=22>
		<td class='input_box_title'>
			<b>발송수량(1회) </b>
		</td>
		<td class='input_box_item'>
		<select name=email_max>
			<option value='5' >5</option>
			<option value='10' >10</option>
			<option value='20' >20</option>
			<option value='50' >50</option>
			<option value='100' selected>100</option>
			<option value='200' >200</option>
			<option value='300' >300</option>
			<option value='400' >400</option>
			<option value='500' >500</option>
			<option value='1000' >1000</option>
		</select>
		</td>
	</tr>
	<tr height=22>
		<td class='input_box_title'>
			<b>일시정지 </b> 
		</td>
		<td class='input_box_item' colspan=3>
			<input type='checkbox' name='email_stop' id='email_stop'>
			<label for='email_stop'>정지</label>
		</td>
	</tr-->
	<tr>
		<td bgcolor='#f5f6f5' colspan=4>
			<table cellpadding=0 cellspacing=3 width=100% >
				<tr>
				<td bgcolor='#ffffff'>
					<textarea name=\"mail_content\" id='basicinfo' style='display:none' ><span id='mail_text'></span></textarea>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=50>
        <td colspan=4 align=center>
            <input type=checkbox name='save_mail' id='save_mail' value='1' align=absmiddle>
            <label for='save_mail'>메일함에 저장하기</label>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                $help_text.="
                <input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle >";
            }else{
                $help_text.="
                <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle ></a>";
            }
            $help_text.="
        </td>
    </tr>
</table>
</div>
";

$select = "
<select name='update_type' >
					<option value='2'>선택한회원 전체에게</option>
					<option value='1'>검색한 회원 전체에게</option>
				</select>";
if($list_type == "addressbook_list"){
$select .= "
				<input type='radio' name='update_kind' id='update_kind_group' value='group' ".CompareReturnValue("group",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_group');\"><label for='update_kind_group'>주소록 그룹 일괄변경</label>
				<input type='radio' name='update_kind' id='update_kind_send_type' value='receive' ".CompareReturnValue("receive",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_receive');\"><label for='update_kind_send_type'>메일링/SMS 수신여부 일괄 변경</label>
				<input type='radio' name='update_kind' id='update_kind_nojoin' value='nojoin' ".CompareReturnValue("nojoin",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_nojoin');\"><label for='update_kind_nojoin'>비회원 일괄 삭제</label>";

				
				$Contents .= "".HelpBox($select, $help_text,650)."</form>";
}else{
$select .= "
				
				<input type='radio' name='update_kind' id='update_kind_sendemail' value='sendemail' ".CompareReturnValue("sendemail",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sendemail');\"><label for='update_kind_sendemail'>메일링 일괄발송</label>";
				$Contents .= "".HelpBox($select, $help_text,300)."</form>";
}


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='addressbook.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
$P->OnloadFunction = "init();";//showSubMenuLayer('storeleft');
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();


function getCampaignGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){
	global $admininfo;

	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and abg.company_id = '".$admininfo[company_id]."'
				 order by vieworder asc";
				 //group by group_ix 오라클때문에 제거
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix' and abg.company_id = '".$admininfo[company_id]."'
				 order by vieworder asc ";
				 //group by group_ix 오라클때문에 제거
	}
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
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

function getFirstDIV($mdb, $selected, $object_id='parent_group_ix', $depth=1, $property="disabled"){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT abg.*
			FROM shop_addressbook_group abg
			where group_depth = 1 and abg.company_id = '".$admininfo[company_id]."'
			";
			//group by group_ix 오라클때문에 제거
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

/*

CREATE TABLE `shop_sms_group` (
  `sg_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_name` varchar(50) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}

CREATE TABLE `shop_addressbook` (
  `ab_ix` int(8) unsigned NOT NULL auto_increment,
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
  PRIMARY KEY  (`ab_ix`),
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


