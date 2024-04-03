<?
	include("../class/layout.class");
	include("../webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
	include("../campaign/mail.config.php");
	//auth(9);

	//print_r(getcominfo());
	$db = new Database;
	$mdb = new Database;
	$sms_design = new SMS;

	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	if ($FromYY == ""){
		$sDate = date("Y/m/d", $before10day);
		$eDate = date("Y/m/d");
		$startDate = date("Ymd", $before10day);
		$endDate = date("Ymd");
	}else{
		$sDate = $FromYY."/".$FromMM."/".$FromDD;
		$eDate = $ToYY."/".$ToMM."/".$ToDD;
		$startDate = $FromYY.$FromMM.$FromDD;
		$endDate = $ToYY.$ToMM.$ToDD;
	}

	if ($vFromYY == ""){

		$sDate2 = date("Y/m/d", $before10day);
		$eDate2 = date("Y/m/d");
		$startDate2 = date("Ymd", $before10day);
		$endDate2 = date("Ymd");

	}else{

		$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
		$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
		$startDate2 = $vFromYY.$vFromMM.$vFromDD;
		$endDate2 = $vToYY.$vToMM.$vToDD;

	}

	if ($birYY == ""){

		$sDate3 = date("Y/m/d");
		$eDate3 = date("Y/m/d");

		$startDate3 = date("Ymd");
		$endDate3 = date("Ymd");
	}else{

		$sDate3 = $birYY."/".$birMM."/".$birDD;
		$eDate3 = "none";
		$startDate3 = $birYY.$birMM.$birDD;
		$endDate3 = "none";
		$birDate = $birYY.$birMM.$birDD;
	}



		$max = 15; //페이지당 갯수

		if ($page == '')
		{
			$start = 0;
			$page  = 1;
		}
		else
		{
			$start = ($page - 1) * $max;
		}


include "member_query.php";


	$Script = "
	<script language='javascript'>

	function view_go()
	{
		var sort = document.view.sort[document.view.sort.selectedIndex].value;

		location.href = 'member.php?view='+sort;
	}


	function ChangeRegistDate(frm){
		if(frm.regdate.checked){
			frm.FromYY.disabled = false;
			frm.FromMM.disabled = false;
			frm.FromDD.disabled = false;
			frm.ToYY.disabled = false;
			frm.ToMM.disabled = false;
			frm.ToDD.disabled = false;
		}else{
			frm.FromYY.disabled = true;
			frm.FromMM.disabled = true;
			frm.FromDD.disabled = true;
			frm.ToYY.disabled = true;
			frm.ToMM.disabled = true;
			frm.ToDD.disabled = true;
		}
	}

	function ChangeVisitDate(frm){
		if(frm.visitdate.checked){
			frm.vFromYY.disabled = false;
			frm.vFromMM.disabled = false;
			frm.vFromDD.disabled = false;
			frm.vToYY.disabled = false;
			frm.vToMM.disabled = false;
			frm.vToDD.disabled = false;
		}else{
			frm.vFromYY.disabled = true;
			frm.vFromMM.disabled = true;
			frm.vFromDD.disabled = true;
			frm.vToYY.disabled = true;
			frm.vToMM.disabled = true;
			frm.vToDD.disabled = true;
		}
	}
	function ChangeBirDate(frm){
		if(frm.bir.checked){
			frm.birYY.disabled = false;
			frm.birMM.disabled = false;
			frm.birDD.disabled = false;
		}else{
			frm.birYY.disabled = true;
			frm.birMM.disabled = true;
			frm.birDD.disabled = true;
		}
	}
	function fixAll(frm){
		if (!frm.all_fix.checked){
			clearAll(frm);
			frm.all_fix.checked = false;
				
		}else{
			checkAll(frm);
			frm.all_fix.checked = true;
		}
		//input_check_num();
	}

	function checkAll(frm){
		for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = true;
		}
	}

	function clearAll(frm){
		for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = false;
		}
	}

	function init(){

		var frm = document.searchmember;
		onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

	if($regdate != "1"){
	$Script .= "
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;";
	}
	if($visitdate != "1"){
	$Script .= "
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;	";
	}

	$Script .= "
	}

	function BatchSubmit(frm){

		if(frm.update_type.value == 1 && frm.search_searialize_value.length < 1){
			alert(language_data['member_batch.php']['A'][language]);//'적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요'
			return false;
		}

		//alert($('#update_kind_group').attr('checked'));
		//return false;
		if($('#update_kind_reserve').attr('checked')){
			if(frm.reserve.value == ''){
				alert(language_data['member_batch.php']['N'][language]);//'적립금 지금액/차감액을 입력해주세요'
				frm.reserve.focus();
				return false;
			}

			if(frm.etc.value == ''){
				alert(language_data['member_batch.php']['B'][language]);//'적립금 적립내용을 입력해주세요'
				frm.etc.focus();
				return false;
			}
		}else if($('#update_kind_group').attr('checked')){
			if(frm.update_gp_ix.value == ''){
				alert(language_data['member_batch.php']['C'][language]);//'변경하시고자 하는 회원그룹을 선택해주세요'
				if(frm.update_gp_ix.value == '' && !frm.update_gp_ix.disabled){
					frm.update_gp_ix.focus();
				}
				return false;
			}


		}else if($('#update_kind_sms').attr('checked')){
			if(frm.sms_text.value.length < 1){
				alert(language_data['member_batch.php']['A'][language]);//'SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요'
				frm.sms_text.focus();
				return false;
			}
		}else if($('#update_kind_coupon').attr('checked')){
			if(frm.publish_ix.value == ''){
				alert(language_data['member_batch.php']['E'][language]);//'지급 하시고자 하는 쿠폰을 선택해주세요'
				if(frm.publish_ix.value == ''){
					frm.publish_ix.focus();
				}
				return false;
			}
		}else if($('#update_kind_sendemail').attr('checked')){

			if(frm.email_subject.value.length < 1){
				alert(language_data['member_batch.php']['F'][language]);//'이메일 제목을 입력해주세요'
				frm.email_subject.focus();
				return false;
			}

			frm.mail_content.value = iView.document.body.innerHTML;

			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'){
				alert(language_data['member_batch.php']['G'][language]);//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}
		}

		if(frm.update_type.value == 1){
			if($('#update_kind_reserve').attr('checked')){
				if(!confirm(language_data['member_batch.php']['I'][language])){return false;}//'검색회원 적립금 일괄 지급을 하시겠습니까?'
			}else if($('#update_kind_group').attr('checked')){
				if(!confirm(language_data['member_batch.php']['J'][language])){return false;}//'검색회원 전체의 회원그룹 변경을 하시겠습니까?'
			}else if($('#update_kind_sms').attr('checked')){
				if(!confirm(language_data['member_batch.php']['K'][language])){return false;}//'검색회원 전체에게 SMS 발송을 하시겠습니까?'
			}else if($('#update_kind_coupon').attr('checked')){
				if(!confirm(language_data['member_batch.php']['L'][language])){return false;}//'검색회원 전체에게 쿠폰일괄지급을 하시겠습니까?'
			}else if($('#update_kind_sendemail').attr('checked')){
				if(!confirm(language_data['member_batch.php']['M'][language])){return false;}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
			}
			//alert(frm.update_kind.value);
		}else if(frm.update_type.value == 2){
			var code_checked_bool = false;
			for(i=0;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_bool = true;
				}
				//	frm.code[i].checked = false;
			}
			if(!code_checked_bool){
				alert(language_data['member_batch.php']['H'][language]);//'선택된 회원이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요'
				return false;
			}
		}
	}

	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_reserve','batch_update_group','batch_update_sms','batch_update_coupon','batch_update_sendemail');

		for(var i=0; i<area.length; ++i){
			if(area[i]==selected_id){
				document.getElementById(selected_id).style.display = 'block';
				$.cookie('member_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
			}else{
				document.getElementById(area[i]).style.display = 'none';
			}
		}
	}

	function view_member_num(sel,num) {
		var sms_cnt=document.getElementById('remainder_sms_cnt');
		var email_cnt=document.getElementById('remainder_email_cnt');
		var frm=document.list_frm;
		if(sel.value==1) {
			sms_cnt.innerHTML=num;
			email_cnt.innerHTML=num;
		} else {
			var frm=document.list_frm;
			var code_checked_num = 0;
			for(i=1;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_num++;
				}
			}
			sms_cnt.innerHTML=code_checked_num;
			email_cnt.innerHTML=code_checked_num;
		}
	}

	function input_check_num() {
		var sms_cnt=document.getElementById('remainder_sms_cnt');
		var email_cnt=document.getElementById('remainder_email_cnt');
		var frm=document.list_frm;
		if(frm.update_type.value==2) {
			var code_checked_num = 0;
			for(i=1;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_num++;
				}
			}
			sms_cnt.innerHTML=code_checked_num;
			email_cnt.innerHTML=code_checked_num;
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
					url: '../campaign/mail.act.php',
					dataType: 'json',
					async: true,
					beforeSend: function(){

					},
					success: function(mail_info){
						document.getElementById('iView').contentWindow.document.body.innerHTML = mail_info.mail_text;
						$('#email_subject_text').val(mail_info.mail_title);
						//alert(mail_info);
						//$('#row_'+wl_ix).slideRow('up',500);
					}
				});
			}
		});
	});

	</script>";

	if($before_update_kind){
		$update_kind = $before_update_kind;
	}
	if($_COOKIE["member_update_kind"]){
		$update_kind = $_COOKIE["member_update_kind"];
	}else if(!$update_kind){
		$update_kind = "sms";
	}


	$Contents = "


	<table width='100%' border='0' align='center'>
	  <tr>
	    <td align='left' colspan=6 > ".GetTitleNavigation("회원정보 일괄관리", "회원관리 > 회원정보 일괄관리 ")."</td>
	  </tr>
	  <!--tr><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 회원정보 일괄관리 </b></div>")."</td></tr-->
	  <tr>
	  	<td>";
	$Contents .= "
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
		<tr>
			<th class='box_01'></th>
			<td class='box_02'></td>
			<th class='box_03'></th>
		</tr>
		<tr>
			<th class='box_04'></th>
			<td class='box_05'  valign=top style='padding:0px'>
	 		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='search_table_box'>
			<form name=searchmember method='get'><!--SubmitX(this);'-->
	    <input type='hidden' name=act value='".$act."'>
	    <input type='hidden' name=before_update_kind value='".$update_kind."'>
		<input type='hidden' name=update_kind value='".$update_kind."'>

			    <tr  height=27>
			      <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >지역선택</td>
			      <td class='search_box_item' width='*' align=left style='padding-left:5px;'>
			      <select name='region' >
					  <option value=''>-- 선택 --</option>
					  <option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
					  <option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
					  <option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
					  <option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
					  <option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
					  <option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
					  <option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
					  <option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
					  <option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
					  <option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
					  <option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
					  <option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
					  <option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
					  <option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
					  <option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
					  <option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
					</select>
			      </td>
				<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
			      <td class='search_box_item' align=left style='padding-left:5px;'>
			      <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
			      <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
			      <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
			    </td>
			    </tr>
			 
			    <tr height=27>
			      <td class='search_box_title' bgcolor='#efefef' align=center>회원그룹 </td>
			      <td class='search_box_item' align=left style='padding-left:5px;'>
			      ".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
			      ".makeGroupLevelSelectBox($mdb,"gp_level",$gp_level)."

			      </td>
			      <td class='search_box_title' bgcolor='#efefef' align=center>조건검색 </td>
			      <td class='search_box_item' align=left style='padding-left:5px;'>
						<table>
							<tr>
								<td>
								  <select name=search_type>
									<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
									<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
									
									<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번화</option>
									<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
									<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
									<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
									<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>
									<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
									<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option>
								  </select>
								 </td>
								 <td><input type=text name='search_text' class='textbox' value='".$search_text."' style='width:100%' ></td>
							</tr>
						</table>
			      </td>
			    </tr>
			    <tr height=27>
			      <td class='search_box_title' bgcolor='#efefef' align=center>발송여부 </td>
			      <td class='search_box_item' align=left >
				   <input type=radio name='mailsend_yn' value='A' id='mailsend_a'  ".CompareReturnValue("A",$mailsend_yn,"checked")." checked><label for='mailsend_a'>모두</label>
			      <input type=radio name='mailsend_yn' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원포함</label>
			      </td>
			       <td class='search_box_title' bgcolor='#efefef' align=center>SMS 발송여부 </td>
			      <td class='search_box_item' align=left >
				  <input type=radio name='smssend_yn' value='A' id='smssend_a'  ".CompareReturnValue("A",$smssend_yn,"checked")." checked><label for='smssend_a'>모두</label>
			      <input type=radio name='smssend_yn' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label><input type=radio name='smssend_yn' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원포함</label>
			      </td>
			    </tr>
			    ";

	$vdate = date("Ymd", time());
	$today = date("Y/m/d", time());
	$vyesterday = date("Y/m/d", time()-84600);
	$voneweekago = date("Y/m/d", time()-84600*7);
	$vtwoweekago = date("Y/m/d", time()-84600*14);
	$vfourweekago = date("Y/m/d", time()-84600*28);
	$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
	$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
	$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
	$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
	$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
	$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

	 $Contents .= "
			    <tr height=27>
			      <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
			      <td class='search_box_item' align=left colspan=3 style='padding-left:5px;'>
			      	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff>

					<tr>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:61px;'></SELECT> 년
						<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:45px;'></SELECT> 월
						<SELECT name=FromDD style='width:45px;'></SELECT> 일 </TD>
						<TD style='padding:0 5px;' align=center>~</TD>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:61px;'></SELECT> 년
						<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:45px;'></SELECT> 월
						<SELECT name=ToDD style='width:45px;'></SELECT> 일</TD>
						<TD style='padding-left:10px;' >
							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
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
			    <tr height=27>
			      <td class='search_box_title' bgcolor='#efefef' align=center><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
			      <td class='search_box_item' align=left colspan=3  style='padding-left:5px;'>
			      	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff>

					<tr>
						<TD nowrap>
						<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY style='width:61px;'></SELECT> 년
						<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM style='width:45px;'></SELECT> 월
						<SELECT name=vFromDD style='width:45px;'></SELECT> 일 </TD>
						<TD style='padding:0 5px;' align=center>~</TD>
						<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY style='width:61px;'></SELECT> 년
						<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM style='width:45px;'></SELECT> 월
						<SELECT name=vToDD style='width:45px;'></SELECT> 일
						</TD>
						<TD style='padding-left:10px;'>
							<a href=\"javascript:select_date('$today','$today',2);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
							<a href=\"javascript:select_date('$vyesterday','$vyesterday',2);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
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
	    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
	    </tr>
	</table><br></form>";

$Contents .= "
	<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='member_batch.act.php'  target='act'>
	<input type='hidden' name='code[]' id='code'>
	<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
	<div id='result_area'>
	<div style='padding:4px;'>회원수 : ".number_format($total)." 명</div>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	  <tr height='28' style='font-weight:bold' bgcolor='#ffffff'>
	    <td width='5%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	    <td width='5%' align='center' class='m_td' onclick='BatchSubmit(document.list_frm);'><font color='#000000'><b>번호</b></font></td>
	    <td width='8%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
	    <td width='8%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
	    <td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
		<td width='5%' align='center' class=m_td><font color='#000000'><b>성별</b></font></td>
	    <td width='*' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
	    <td width='10%' align='center' class=m_td><font color='#000000'><b>등록일</b></font></td>
	    <td width='7%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>";
		if($admininfo[mall_type] != "H"){
$Contents .= "
	    <td width='7%' align='center' class=m_td><font color='#000000'><b>적립금</b></font></td>";
		}
		$Contents .= "
			<td width='10%' align='center' class=m_td><font color='#000000'><b>최종로그인</b></font></td>
	    <td width='10%' align='center' class=e_td><font color='#000000'><b>메일링</b></font></td>
	  </tr>";



		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			$no = $total - ($page - 1) * $max - $i;

			if($db->dbms_type == "oracle"){
				$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in (1,2,5,6,7)");
			}else{
				$mdb->query("SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
			}

			$mdb->fetch(0);
			$reserve_sum = number_format($mdb->dt[reserve_sum]);

			if($db->dt[sex_div] == "M"){
				$sex_div_str = "남";
			}else if($db->dt[sex_div] == "W"){
				$sex_div_str = "여";
			}else{
				$sex_div_str = "-";
			}
	$Contents .= "
	  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
	    <td class='list_box_td list_bg_gray' align='center' ><input type=checkbox name=code[] id='code' value='".$db->dt[code]."' onClick='input_check_num()'></td>
	    <td class='list_box_td' align='center' >".$no."</td>
	    <td class='list_box_td list_bg_gray' align='center'><span title=''>".$db->dt[gp_name]."</span></td>
	    <td class='list_box_td point' align='center' nowrap><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[name]."</a> </td>
	    <td class='list_box_td list_bg_gray' align='center' ><b>".$db->dt[id]."</b></td>
		<td class='list_box_td list_bg_gray' align='center' ><b>".$sex_div_str."</b></td>
	    <td class='list_box_td' align='center' >".$db->dt[mail]."</td>
	    <td class='list_box_td list_bg_gray' align='center' >".$db->dt[regdate]."</td>
	    <td class='list_box_td' align='center' >".$db->dt[visit]."</td>";
		if($admininfo[mall_type] != "H"){
		$Contents .= "
	    <td class='list_box_td list_bg_gray' align='center' ><a href=\"javascript:PoPWindow('reserve.pop.php?code=".$db->dt[code]."',650,700,'reserve_pop')\">".$reserve_sum."</a></td>
		<td class='list_box_td' align='center' >".$db->dt[last]."</td>
	    <td class='list_box_td list_bg_gray' align='center' >".($db->dt[info] == "1" ? "수신":"비수신")."</td>";
		}else{
		$Contents .= "
		<td class='list_box_td list_bg_gray' align='center' >".$db->dt[last]."</td>
	    <td class='list_box_td ' align='center' >".($db->dt[info] == "1" ? "수신":"비수신")."</td>";
		}
		$Contents .= "

	  </tr>";

		}

	if (!$db->total){

	$Contents = $Contents."
	  <tr height=50>
	    <td class='list_box_td' colspan='13' align='center'>등록된 회원 데이타가 없습니다.</td>
	  </tr>";

	}



	$Contents .= "
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	  <tr height='40'>
	    <td colspan=5 align=left>

	    </td>
	    <td  colspan='6' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
	  </tr>
	</table>
	</div>";

	$help_text = "
	<div id='batch_update_reserve' ".(($update_kind == "reserve" || $update_kind == "") ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>적립금 일괄변경</b> <span class=small style='color:gray'><!--적립금 금액 및 내용을 입력후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=170>
		<col width=*>
		<tr>
			<td class='input_box_title'> <b>적립금 지급액 / 차감액</b></td>
			<td class='input_box_item'> <input type=text name='reserve'  class=textbox value='' onkeydown='onlyNumber(this)' onkeyup='onlyNumber(this)'  style='width:150' > <span class='small blu'><!--사용의 경우 마니너스 금액으로 입력하세요 예) -1000-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' )."</span></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>적립금 적립내용</b></td>
			<td class='input_box_item'> <input type=text name='etc'  class=textbox value='' style='width:250' ></td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>적립금 상태</b></td>
			<td class='input_box_item'>
				<select name='state'>
					<option value=0>적립대기</option>
					<option value=1>적립완료</option>
					<option value=2>사용내역</option>
					<option value=5>반품</option>
					<option value=9>주문취소</option>
				</select>
			</td>
		</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>
	</div>
	<div id='batch_update_group' ".($update_kind == "group" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>회원그룹 일괄변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 회원그룹 선택후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:1px solid #e2e2e2;'>
		<col width=200>
		<col width=*>

		<tr>
			<td bgcolor='#efefef'>
				 <b>회원그룹</b>
				<input type='checkbox' name='mem_group_use' id='bir' value='1' onclick=\"if(this.checked){\$('#update_gp_ix').removeAttr('disabled');}else{\$('#update_gp_ix').attr('disabled','disabled');}\">
			</td>
			<td >".makeGroupSelectBox($mdb,"update_gp_ix",$update_gp_ix, " disabled")." <span class=small style='color:gray'><!--회원그룹 변경에 따라 회원등급이 자동 변경됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D' )."</span></td></tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=30><td colspan=4 align=left><span class=small style='color:gray'><!--회원그룹 변경시 회원 등급이 자동으로 변경됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E' )."</span></td></tr>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
                
            </td>
        </tr>
	</table>
	</div>
	<div id='batch_update_sms' ".($update_kind == "sms" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>sms 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F' )."</span></div>
	<table cellpadding=0 cellspacing=0>
		<col width='200px;'>
		<col width='200px;'>
		<tr>
			<td style='vertical-align:top;'>
				<table class='box_shadow' style='width:139px;height:120px;table-layout:fixed;' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top style='padding:5px 7px 5px 7px'>
							<table cellpadding=0 cellspacing=0><!--CheckSpecialChar(this);-->
							<tr><td align=left>mallstory sms </td></tr>
							<tr><td><textarea style='width:106px;height:100px;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' name='sms_text' onkeyup=\"fc_chk_byte(this,80, this.form.sms_text_count);\" ></textarea></td></tr>
							<tr><td height=20 align=right><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> / 80 byte </td></tr>
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
			</td>
		<!--/tr>
		<tr-->";
	$cominfo = getcominfo();

	$help_text .= "
			<td valign=top style='padding:0 0 0 10px'>
				<table cellpadding=0 cellspacing=0 ><input type=hidden name='sms_send_page' value='1'>
					<tr height=26>
						<td align=left width=90 class=small>보내는사람 : </td>
						<td><input type=text name='send_phone' class=textbox style='display:inline;' size=12 value='".$cominfo[com_phone]."'></td>
					</tr>
					<tr height=22><td align=left class=small>SMS 잔여건수 : </td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>
					<tr height=22><td align=left class=small>발송수/발송대상 : </td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</b> 명</td></tr>
					<tr height=22>
							<td align=left class=small>발송수량(1회) : </td>
							<td>
							<select name=max>
								<option value='5' >5</option>
								<option value='10'  >10</option>
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
					<tr height=22><td align=left class=small>일시정지 : </td><td><input type='checkbox' name='stop' ></td></tr>
					<tr height=50>
                        <td align=center colspan=2>";
                            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                                $help_text .= "
                                <input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0>";
                            }else{
                                $help_text .= "
                                <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0></a>";
                            }
                            $help_text .= "
                        </td>
                    </tr>
				</table>
			</td>
		</tr>
	</table>
	</div>
	<div id='batch_update_coupon' ".($update_kind == "coupon" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>쿠폰 일괄지급</b> <span class=small style='color:gray'><!--지급 하시고자하는 쿠폰을 선택해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=170>
		<col width=*>
		<tr height=30>
			<td class='input_box_title'>
				 <b>발행쿠폰 목록</b>
				<input type='checkbox' name='mem_group_use' id='bir' value='1' onclick=\"if(this.checked){\$('#publish_ix').removeAttr('disabled');}else{\$('#publish_ix').attr('disabled','disabled');}\">
			</td>
			<td class='input_box_item'>".CouponPublishSelectBox($mdb,"publish_ix", " disabled")." <span class=small style='color:gray'><!--기 발행된 쿠폰 목록입니다. -->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H' )."</span></td></tr>

	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=30><td colspan=4 align=left><span class=small style='color:gray'><!--선택된 회원에게 쿠폰이 발급됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I' )."</span></td></tr>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>
	</div>
	<div id='batch_update_sendemail' ".($update_kind == "sendemail" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif' align=absmiddle> <b>email 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 email 을 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=15%>
		<col width=35%>
		<col width=15%>
		<col width=35%>
		<tr>
			<td class='input_box_title'> <b>이메일 제목</b></td>
			<td class='input_box_item' colspan=3>
				<table cellpadding=0>
					<tr>
						<td><input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px;height:21px;padding:0px;margin:0px;' ></td>

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
			<td class='input_box_title'> <b>참조</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='mail_cc'  class=textbox value='' style='width:350px' > <span class='small blu'><!--콤마(,) 구분으로 이메일을 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K' )."</span>
			</td>
		</tr>
		<tr height=22><input type=hidden name='email_send_page' value='1'>
			<td class='input_box_title'> <b>발송수/발송대상 </b> </td>
			<td class='input_box_item'><b id='sended_email_cnt' class=blu>0</b> 건 / <b id='remainder_email_cnt'>$total</b> 명</td>
			<td class='input_box_title'> <b>발송수량(1회) </b> </td>
			<td class='input_box_item'>
				<select name=email_max>
					<option value='5' >5</option>
					<option value='10'  >10</option>
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
			<td class='input_box_title'> <b>일시정지 </b> </td>
			<td class='input_box_item' colspan=3><input type='checkbox' name='email_stop' id='email_stop'><label for='email_stop'>정지</label></td>
		</tr>
		<tr>
			<td class='input_box_item' style='padding:0px;' colspan=4>".WebEdit()."<input type='hidden' name='mail_content' value=''></td>
		</tr>
	</table>
	<table cellpadding=5 cellspacing=1 width=100% >
		<tr bgcolor=#ffffff>
			<td colspan=2 align=right valign=top style='padding:0px;padding-right:20px;'>
			<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
	    <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
			</td>
		</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>
                <input type=checkbox name='save_mail' id='save_mail' value='1' align=absmiddle>
                <label for='save_mail'>메일함에 저장하기</label>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                    $help_text .= "
                    <input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle >";
                }else{
                    $help_text .= "
                    <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle ></a>";
                }
                $help_text .= "
            </td>
        </tr>
	</table>
	</div>
	";

	$select = "
	<nobr>
	<select name='update_type' onChange='view_member_num(this,\"$total\")'>
		<option value='1'>검색한 회원 전체에게</option>
		<option value='2'>선택한회원 전체에게</option>
	</select>


	<input type='radio' name='update_kind' id='update_kind_group' value='group' ".(($update_kind == "group" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_group');\"><label for='update_kind_group'>회원그룹 일괄변경</label>
	<input type='radio' name='update_kind' id='update_kind_sms' value='sms' ".CompareReturnValue("sms",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS 일괄발송</label>
	<input type='radio' name='update_kind' id='update_kind_sendemail' value='sendemail' ".CompareReturnValue("sendemail",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sendemail');\"><label for='update_kind_sendemail'>이메일 일괄발송</label>";

	if($admininfo[mall_type] != "H"){
		$select .= "
	<input type='radio' name='update_kind' id='update_kind_coupon' value='coupon' ".CompareReturnValue("coupon",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_coupon');\"><label for='update_kind_coupon'>쿠폰 일괄지급</label>
	<input type='radio' name='update_kind' id='update_kind_reserve' value='reserve' ".(($update_kind == "reserve" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_reserve');\"><label for='update_kind_reserve'>적립금 일괄지급</label>";
	}
	$select .= "

	</nobr>";

	if($admininfo[mall_type] == "H"){
		$Contents .= "".HelpBox($select, $help_text, 520)."</form>";
	}else{
		$Contents .= "".HelpBox($select, $help_text, 750)."</form>";
	}

	$Contents .= "
	<form name='lyrstat'>
		<input type='hidden' name='opend' value=''>
	</form>";



	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='member.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n".$Script;
	$P->OnloadFunction = "init();Init(document.list_frm);";
	$P->strLeftMenu = basic_menu();
	$P->jquery_use = true;
	$P->prototype_use = false;
	switch($update_kind) {
		case("sms") :
			$P->Navigation = "HOME > 회원관리 > SMS 일괄발송";
			$P->title = "SMS 일괄발송";
		break;
		case("sendemail") :
			$P->Navigation = "HOME > 회원관리 > 이메일 일괄발송";
			$P->title = "이메일 일괄발송";
		break;
		case("coupon") :
			$P->Navigation = "HOME > 회원관리 > 쿠폰 일괄지급";
			$P->title = "쿠폰 일괄지급";
		break;
		case("reserve") :
			$P->Navigation = "HOME > 회원관리 > 적립금 일괄 관리";
			$P->title = "적립금 일괄 관리";
		break;
		default :
			$P->Navigation = "HOME > 회원관리 > 회원정보 일괄관리";
			$P->title = "회원정보 일괄관리";
		break;
	}
	$P->strContents = $Contents;
	echo $P->PrintLayOut();


?>



