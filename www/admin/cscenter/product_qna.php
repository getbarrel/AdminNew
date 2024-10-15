<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include("$DOCUMENT_ROOT/class/sms.class");
include("../campaign/mail.config.php");
//auth(9);
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

$db = new MySQL;
$mdb = new MySQL;
$gdb = new MySQL;
$sms_design = new SMS;
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

	$sDate = date("Y-m-d", $before10day);
	$eDate = date("Y-m-d");

	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
}

if(!$re_bool){
	$re_bool = 'A';
}

$sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

$db->query($sql);
$db->fetch();
$front_url = $db->dt['front_url'];


$Script = "	<script language='javascript'>
function ProductQnaDelete(bbs_ix){
	if(confirm(language_data['product_qna.php']['A'][language])){
	//'제품문의를  정말로 삭제하시겠습니까? '
		window.frames['act'].location.href='/admin/cscenter/product_qna.act.php?act=delete&bbs_ix='+bbs_ix
		//document.getElementById('iframe_act').src='product_qna.act.php?act=delete&bbs_ix='+bbs_ix;
	}
}

function ChangeRegistDate(frm){
	var value = $('#regdate').attr('checked');
	if(value == 'checked'){
		$('#start_datepicker').attr('disabled',false);
		$('#end_datepicker').attr('disabled',false);
	}else{
		$('#start_datepicker').attr('disabled',true);
		$('#end_datepicker').attr('disabled',true);
	}
}

function init(){
	var frm = document.searchmember;
	//onLoad('$sDate','$eDate');
}

function init2(){
	var frm = document.searchmember;
	//onLoad('$sDate','$eDate');
}

function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	//LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	//LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);

}

function clearAll(frm){
	for(i=0;i < frm.bbs_ix.length;i++){
			frm.bbs_ix[i].checked = false;
	}
}
function checkAll(frm){
	for(i=0;i < frm.bbs_ix.length;i++){
			frm.bbs_ix[i].checked = true;
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
}
</script>
<script type='text/javascript'>

function cid_del(code){
	$('#row_'+code).remove();
}

//바이트 가져오는 함수
String.prototype.byteLength = function() {
    var l= 0;
     
    for(var idx=0; idx < this.length; idx++) {
        var c = escape(this.charAt(idx));
         
        if( c.length==1 ) l ++;
        else if( c.indexOf('%u')!=-1 ) l += 2;
        else if( c.indexOf('%')!=-1 ) l += c.length/3;
    }
     
    return l;
};

function use_templet(obj){
	sms_txt	=	obj.prev().prev().find('div').text();
	sms_txt_length = sms_txt.byteLength();
	
	frm	=	document.list_frm;

	if(sms_txt_length > 80){
	 
			$('#byte').html('1000byte');
			$('#msg_type').html('LMS');
			$('#lms_type').html('<input type=hidden name=send_type style=width:100px value=3>');

	}

	if(sms_txt_length < 80){
	  $('#byte').html('80byte');
	  $('#msg_type').html('SMS');
	  $('#lms_type').html('<input type=hidden name=send_type style=width:100px value=1>');
	}

		$('input[name=sms_text_count]').val(sms_txt_length);
		$('.sms_text').val(sms_txt);
	
}

$(document).ready(function(){

	$('#lms_title').hide();
	
	$('select[name=update_type]').val('2');
	view_member_num(2);

	$('input[name=update_kind]').change(function() {
			var radioValue = $(this).val();
			if (radioValue == 'bigemail') {
				$('#bigemail_menu').css('display','block');		
			}else{
				$('#bigemail_menu').css('display','none');
			}
		});

	$('input[name=mult_search_use]').click(function (){
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');
			
			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
	});

	var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
	if(mult_search_use == '1'){
		$('#search_text_input_div').css('display','none');
		$('#search_text_area_div').css('display','');

		$('#search_text_area').attr('disabled',false);
		$('#search_texts').attr('disabled',true);
	}else{
		$('#search_text_input_div').css('display','');
		$('#search_text_area_div').css('display','none');

		$('#search_text_area').attr('disabled',true);
		$('#search_texts').attr('disabled',false);
	}

	frm	=	document.list_frm;
	$('#tabs').tabs();
	getContentTab(1);

	 $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
	 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');

	 $('input[name=send_time_email]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=email_time_hour]').attr('disabled',true).css('color','#ccc');
	 $('select[name=email_time_minite]').attr('disabled',true).css('color','#ccc');

	$('.send_time_now').click(function(){
		if(this.checked){
			 $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
			 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
			 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');
		}
	});

	$('.send_time_reserve').click(function(){
		if(this.checked){
			$('input[name=send_time_sms]').attr('disabled',false).css('background-color','#fff7da');
			$('select[name=send_time_hour]').attr('disabled',false).css('color','#666');
			$('select[name=send_time_minite]').attr('disabled',false).css('color','#666');
		}
	});

	$('.email_send_time_now').click(function(){
		if(this.checked){
			 $('input[name=send_time_email]').attr('disabled',true).css('background-color','#fff');
			 $('select[name=email_time_hour]').attr('disabled',true).css('color','#ccc');
			 $('select[name=email_time_minite]').attr('disabled',true).css('color','#ccc');
		}
	});

	$('.email_send_time_reserve').click(function(){
		if(this.checked){
			$('input[name=send_time_email]').attr('disabled',false).css('background-color','#fff7da');
			$('select[name=email_time_hour]').attr('disabled',false).css('color','#666');
			$('select[name=email_time_minite]').attr('disabled',false).css('color','#666');
		}
	});
});


function getContentTab(index){

	var targetDiv = '.tabs-' + index; 
	$(targetDiv).html();   // 해당 div에 결과가 나타남
}

	function view_go()
	{
		var sort = document.view.sort[document.view.sort.selectedIndex].value;

		location.href = 'member.php?view='+sort;
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

	function init(){

		var frm = document.searchmember;
		//onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

	if($regdate != "1"){
	$Script .= "
	/*
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	*/	
		";
	}
	if($visitdate != "1"){
	$Script .= "
		/*
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;	
		*/
		";
	}
	if($bir != "1"){
	$Script .= "
	/*
		frm.birYY.disabled = true;
		frm.birMM.disabled = true;
		frm.birDD.disabled = true;
	*/";
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
				alert(language_data['member_batch.php']['D'][language]);//'SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요'
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

			frm.mail_content.value = $('#iView').contents().find('body').html();

			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'){
				alert(language_data['member_batch.php']['G'][language]);//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}
		}else if($('#update_kind_bigemail').attr('checked')){

			if(frm.email_title.value.length < 1){
				alert(language_data['member_batch.php']['F'][language]);//'이메일 제목을 입력해주세요'
				frm.email_title.focus();
				return false;
			}

			var mail_value = CKEDITOR.instances['mail_content2'].getData();
			
			if(mail_value == ''){
				alert(language_data['member_batch.php']['G'][language]);//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}

		}

		if(frm.update_type.value == 1){
			
			if($('#confirm_bool').val() == '1'){
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
				}else if($('#update_kind_bigemail').attr('checked')){
					if(!confirm(language_data['member_batch.php']['M'][language])){return false;}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
				}
			}
			
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


		if($('#update_kind_bigemail').attr('checked')){
		
			var formdata = new FormData();
			$.each($('#list_form').serializeArray(), function(i, field) {
			    formdata.append(field.name, field.value);
			});
			var contents = CKEDITOR.instances['mail_content2'].getData();
			formdata.append('mail_contents', contents);

			$.ajax({
				url: '/admin/member/member_batch.act.php',
				data: formdata,
				contentType: false,
				processData: false,
				type: 'post',
				datatype: 'json',					
				error: function(e) { console.log(e); },
				success: function(d) {
					console.log('mail send complete');
				}
			});
			alert('메일 전송 요청이 완료 되었습니다.');
			return false;
		}
	}

	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_reserve','batch_update_group','batch_update_sms','batch_update_coupon','batch_update_sendemail','batch_update_bigemail');

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
		var sms_cnt=$('#remainder_sms_cnt');
		var email_cnt=$('#remainder_email_cnt');
		var bigemail_cnt=$('#remainder_bigemail_cnt');
		var frm=document.list_frm;
		if(sel.value==1) {
			sms_cnt.html(num);
			email_cnt.html(num);
			bigemail_cnt.html(num);
		} else {
			var frm=document.list_frm;
			var code_checked_num = 0;
			for(i=1;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_num++;
				}
			}
			sms_cnt.html(code_checked_num);
			email_cnt.html(code_checked_num);
			bigemail_cnt.html(code_checked_num);
		}
	}

	function input_check_num() {
		var sms_cnt=$('#remainder_sms_cnt');
		var email_cnt=$('#remainder_email_cnt');
		var bigemail_cnt=$('#remainder_bigemail_cnt');
		var frm=document.list_frm;
		if(frm.update_type.value==2) {
			var code_checked_num = 0;
			for(i=1;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_num++;
				}
			}
			sms_cnt.html(code_checked_num);
			email_cnt.html(code_checked_num);
			bigemail_cnt.html(code_checked_num);
		}
	}

	function LoadEmail(email_type){
		if(email_type == 'new'){
			//$('#email_subject').css('display','inline');
			$('#email_select_area').css('display','none');
		}else if(email_type == 'box'){
			//$('#email_subject').css('display','none');
			$('#email_select_area').css('display','inline');
		}
	}
	
	function LoadEmail2(email_type){
		if(email_type == 'new'){
			$('#email_list').css('display','none');
		}else if(email_type == 'box'){
			$('#email_list').css('display','inline');
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
						CKEDITOR.instances.mail_content2.setData(mail_info.mail_text);
						$('#email_subject_text').val(mail_info.mail_title);
						$('#email_title').val(mail_info.mail_title);
						//alert(mail_info);
						//$('#row_'+wl_ix).slideRow('up',500);
					}
				});
			}
		});
	});

	</script>";
	
$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("상품문의 관리", "고객센타 > 상품문의 관리 ")."</td>
		</tr>"; 
$mstring .= "
		<tr>
			<td>
			<form name='searchmember'>
			<input type=hidden name='mmode' value='".$mmode."'>
						<input type=hidden name='mem_ix' value='".$mem_ix."'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef' width='95%'><img src='../images/dot_org.gif' align=absmiddle> <b>상품문의 검색하기</b></td>
					<td style='border-bottom:2px solid #efefef;' width='5%'>
						<a href='./cscenter.manage.php?page_type=product_qna'><input type='button' value='상품문의 게시판 설정' style='margin: 3px;cursor:pointer;'></a>
					</td>
				</tr>
				<tr>
				<td style='width:100%;' valign=top colspan=3>
					<table width=100%  border=0 cellpadding='0' cellspacing='0'>
						<!--tr><td style='border-bottom:2px solid #efefef' height=25><img src='../images/dot_org.gif' align=absmiddle> <b>상품Q&A 검색하기</b></td></tr-->
						<tr>
							<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
								<table class='box_shadow' style='width:100%;' align=left border=0 cellpadding='0' cellspacing='0'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02'></td>
										<th class='box_03'></th>
									</tr>
									<tr>
										<th class='box_04'></th>
										<td class='box_05' valign=top>
											<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
											<TR>
												<TD bgColor=#ffffff style='padding:0 0 0 0;'>
												<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
													<col width='15%'/>
													<col width='35%'/>
													<col width='15%'/>
													<col width='35%'/>";
if($_SESSION["admin_config"][front_multiview] == "Y"){
    $mstring .= "
													<tr>
														<td class='search_box_title' > 글로벌 회원 구분</td>
														<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
													</tr>";
}
$mstring .= "
													<tr height='27'>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>문의 타입</th>
														<td class='search_box_item'>
															<!--input type='radio' name='msg_type' value='' ".CompareReturnValue("",$msg_type,"checked")."> 전체-->
															<input type='radio' name='msg_type' value='B' checked> 일반
															<!--input type='radio' name='msg_type' value='E' ".CompareReturnValue("E",$msg_type,"checked")."> 긴급-->
														</td>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>관리자 답변여부</th>
														<td class='search_box_item'>
															<input type='radio' name='re_bool' value='A' ".CompareReturnValue("A",$re_bool,"checked")." id='re_bool_a'> <label for='re_bool_a'>전체</label>
															<input type='radio' name='re_bool' value='Y' ".CompareReturnValue("Y",$re_bool,"checked")." id='re_bool_y'> <label for='re_bool_y'>답변완료</label>
															<input type='radio' name='re_bool' value='N' ".CompareReturnValue("N",$re_bool,"checked")." id='re_bool_n'> <label for='re_bool_n'>답변대기</label>
														</td>
													</tr>
													<tr height=30>
														<td class='input_box_title' ><label for='regdate'>작성일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked")."></td>
														<td class='input_box_item' colspan='3'>
															".search_date('sdate','edate',$sdate,$edate)."
														</td>
													</tr>
													<tr height='27'>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>조건검색</th>
														<td class='search_box_item'>
														<select name=search_type>
															<option value='pname' ".CompareReturnValue("pname",$search_type,"selected").">제품명</option>
															<!--<option value='bbs_subject' ".CompareReturnValue("bbs_subject",$search_type,"selected").">제목</option>-->
															<option value='bbs_name' ".CompareReturnValue("bbs_name",$search_type,"selected").">작성자</option>
															<option value='bbs_contents' ".CompareReturnValue("bbs_contents",$search_type,"selected").">내용</option>
														</select>
														<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:200px;' >
														</td>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>문의분류</th>
														<td class='search_box_item'>
														<select name='bbs_div' style='width:100px;'>
															<option value=''>전체</option>
															".getQnaDiv()."
														</select>
														</td>
													</tr>";
													if($admininfo[admin_level] == 9){
													$mstring .= "
													<tr height='27'>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>셀러업체</th>
														<td class='search_box_item'>
															".companyAuthList($company_id , "validation=false title='셀러업체' ")."
														</td>
														<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>판매처</th>
														<td class='search_box_item'>
														<select name='site_code'>
															<option value=''>전체</option>
															<option value='self'".($site_code=='self' ? ' selected' : '').">자체쇼핑몰</option>	";
															if(! empty($site_infos)){
																foreach($site_infos as $key=>$val)
																{
																	$mstring .= "
																<option value='{$key}'".($key==$site_code ? ' selected' : '').">{$val}</option>";
																}
															}
														$mstring .= "
														</select>
														</td>
													</tr>
													";
													}
												$mstring .= "
												</table>
												</TD>
											</TR>
											</TABLE>
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
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan=3 align=center style='padding:20px 20px 20px 0'>
					<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
				</td>
			</tr>
			</table>
			</form>
			</td>
		</tr>
		<tr>
			<td>
			".ProductQna()."
			</td>
		</tr>
		";
$mstring .="</table>";

$Contents = $mstring;

if($page_type == "seller"){
	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	if($regdate!=1) $P->OnloadFunction = "init();";
	else $P->OnloadFunction = "init2();";
	$P->strLeftMenu = seller_menu();
	$P->Navigation = "셀러관리 > 상품 문의";
	$P->title = "상품 문의";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "init2();";
	$P->strLeftMenu = member_menu();
	$P->Navigation = "셀러관리 > 상품 문의";
	$P->title = "상품 문의";
    $P->NaviTitle = "상품 문의"; 
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	if($regdate!=1) $P->OnloadFunction = "init();";
	else $P->OnloadFunction = "init2();";
	$P->strLeftMenu = cscenter_menu();
	$P->Navigation = "고객센타 > 상품문의 관리";
	$P->title = "상품문의 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function ProductQna(){

	global $admininfo, $admin_config, $DOCUMENT_ROOT,$re_bool,$search_type, $search_text,$_GET,$nset,$page,$startDate,$endDate,$auth_delete_msg,$page_type,$bbs_div,$site_code,$site_infos,$regdate;
	global $mmode, $mem_ix, $msg_type, $mall_ix, $front_url;

	$mdb = new Database;
	$db = new Database;
	$gdb = new Database;

	$sms_design = new SMS;

	$where = " where pq.bbs_ix <> '0' ";

	if($mmode == "personalization"){
		$where .= " and pq.ucode = '".$mem_ix."' ";
	}

	if($mall_ix){
		$where .=" and cu.mall_ix = '".$mall_ix."' ";
	}
	
	if($msg_type != ""){
		if($msg_type == 'B'){
			$where .= " and pq.msg_type in ('','$msg_type') ";
		}else{
			$where .= " and pq.msg_type ='$msg_type' ";
		}
	}

	if($search_type != "" && $search_text != ""){
		$where .= " and pq.".$search_type." LIKE '%$search_text%' ";
	}

	if($_REQUEST[regdate] == "1"){

		$sdate = str_replace("-","",$_REQUEST[sdate]);
		$edate = str_replace("-","",$_REQUEST[edate]);

		if($sdate != "" && $edate != ""){
			$where .= " and  date_format(pq.regdate, '%Y%m%d') between  $sdate and $edate ";
		}
	}

	if($_REQUEST[company_id] != ""){
		$where .= " and pq.company_id = '".$_REQUEST[company_id]."'";
	}
	
	if($site_code!='')
	{
		$where .= " and pq.site_code='".($site_code=='self' ? '' : $site_code)."'";
	}
	
	if($bbs_div!='')
	{
		$where .= " and pq.bbs_div='{$bbs_div}'";
	}

	if($re_bool != ""){
		if($re_bool == "Y"){
			$where .= " and pq.bbs_re_cnt > 0 ";
		}else if($re_bool == "N"){
			$where .= " and pq.bbs_re_cnt = 0";
		}
	}

	if($admininfo[admin_level] == 9){
		$sql = "select pq.*,cu.mall_ix from ".TBL_SHOP_PRODUCT_QNA." pq left join ".TBL_COMMON_USER." cu on pq.ucode = cu.code  $where ";
	}else{
		$sql = "select pq.*,cu.mall_ix from ".TBL_SHOP_PRODUCT_QNA." pq left join ".TBL_COMMON_USER." cu on pq.ucode = cu.code $where and company_id = '".$admininfo["company_id"]."' ";
	}
	
	$mdb->query($sql);
	$total = $mdb->total;
	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString = "
			<form id='list_form' name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='../cscenter/product_qna.act.php'  enctype='multipart/form-data' target='test_act'>
			<input type='hidden' name='confirm_bool' id='confirm_bool' value='1'>
			<input type='hidden' name='code[]' id='code'>
			<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>

			<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>";
	$mString = $mString."
			<col width='4%'>
			<col width='5%'>
			<col width='20%'>
			<col width='*'>
			<col width='5%'>
			<col width='5%'>
			<col width='8%'>
			<col width='8%'>
			<col width='5%'>
			<col width='10%'>
			<tr align=center bgcolor=#efefef style='font-weight:600;'>
				<td class=s_td  height=25>번호</td>
				<td class=m_td >분류</td>
				<td class=m_td >상품명</td>
				<td class=m_td >내용</td>
				<td class=m_td>작성자</td>
				<td class=m_td>국내<br>해외</td>				
				<td class=m_td>등록일자</td>
				<td class=m_td>셀러업체</td>
				<td class=m_td>관리자</br>답변여부</td>
				<td class=e_td>관리</td>
			</tr>";
	if($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=50><td colspan=10 align=center>상품 Q&A 내역이 존재 하지 않습니다.</td></tr>";
	}else{
		$sql = "select pq.*,cu.mall_ix from ".TBL_SHOP_PRODUCT_QNA." pq left join ".TBL_COMMON_USER." cu on pq.ucode = cu.code $where order by  regdate desc limit $start, $max";
		$mdb->query($sql);

		$addQaDir = "";
		if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
			$addQaDir = "/QA";
		}

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$no = $total - ($page - 1) * $max - $i;

			//$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "s");
			$img_str = PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $mdb->dt[pid], "slist");

			$sql = "select 
						ccd.com_name,
						csd.charge_code
					from 
						common_company_detail as ccd
						inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
					where 
						ccd.company_id = '".$mdb->dt[company_id]."'";
			$db->query($sql);
			$db->fetch();
			$com_name = $db->dt[com_name];
			$charge_code = $db->dt[charge_code];
            $link = $front_url."/shop/goodsView/".$mdb->dt['pid'];
			$mString = $mString."<tr height=57 bgcolor=#ffffff align=center>
			<td class='list_box_td' bgcolor='#efefef' >".$no."</td>
			<td class='list_box_td' bgcolor='#efefef' >".getQnaDivName($mdb->dt[bbs_div])."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=left style='text-align:left;word-break:break-all;line-height:160%;padding:10px;font-weight:normal;' >
				<img src='".$img_str."' width=40 height=40 align=absmiddle style='border:1px solid #efefef'>
				<a href='".$link."' target=_blank>".$mdb->dt[pname]."</a>
			</td>
			<td class='list_box_td' bgcolor='#efefef' >
				<a style='cursor:pointer;' onclick=\"javascript:PoPWindow('/admin/cscenter/product_qna.modify.php?bbs_ix=".$mdb->dt[bbs_ix]."&page_type=".$page_type."',600, 720,'product_qna');\">".$mdb->dt[bbs_contents]."</a>
			</td>
			<td class='list_box_td' bgcolor='#efefef'>".wel_masking_seLen($mdb->dt[bbs_name], 1, 1)."</td>
			<td class='list_box_td' bgcolor='#efefef'>".GetDisplayDivision($mdb->dt[mall_ix], "text")."</td>
			<td class='list_box_td' bgcolor='#ffffff'>".str_replace("-",".",substr($mdb->dt[regdate],0,10))."</td>
			<td class='list_box_td' bgcolor='#efefef'>
				<span style='cursor:pointer;' class='helpcloud' help_width='220' help_height='70' help_html='".GET_SELLER_INFO($mdb->dt[company_id])."'>
				<a href= '../seller/company.add.php?company_id=".$mdb->dt[company_id]."&code=".$charge_code."' target='_blank'>
				".$com_name."
				</a>
				</span>
			</td>
			<td class='list_box_td' bgcolor='#efefef'>".($mdb->dt[bbs_re_cnt] > 0 ? "답변완료":"답변대기")."</td>
			<td class='list_box_td' bgcolor='#ffffff' align=center>
				<input type='button' value='수정' style='margin: 1px;' onclick=\"javascript:PoPWindow('/admin/cscenter/product_qna.modify.php?bbs_ix=".$mdb->dt[bbs_ix]."&page_type=".$page_type."',600, 720,'product_qna');\">
				<input type='button' value='삭제' style='margin: 1px;' onclick=\"ProductQnaDelete('".$mdb->dt[bbs_ix]."');\"></br>";
			if(false){
            	$mString .= "<input type='button' value='이력보기' style='margin: 3px;' onclick=\"javascript:PoPWindow3('useafter.history.php?bbs_ix=".$mdb->dt[bbs_ix]."',900,800,'history')\">";
            }
            $mString .= "</td>
			</tr>";
		}

	}
	$mString = $mString."</table>";

	$mString = $mString."
	<table cellpadding=4 cellspacing=0 border='0' width=100%>
		<tr bgcolor=#ffffff>
			<td height=50 colspan=10 align='right'>
			".page_bar($total, $page, $max,  "&max=$max&re_bool=$re_bool&startDate=$startDate&endDate=$endDate&search_type=$search_type&search_text=$search_text&mmode=$mmode&mem_ix=$mem_ix&mall_ix=$mall_ix&msg_type=$msg_type&sdate=$sdate&edate=$edate&bbs_div=$bbs_div&regdate=$regdate&ori_company_id=$ori_company_id&company_id=$company_id&com_name=$com_name&site_code=$site_code&x=$x&y=$y","")."
			</td>
		</tr>
	</table>";

	if($admininfo[mall_type] == "H"){
		//$mString .= "".HelpBox($select, $help_text, 300)."</form>";
	}else{
		//$mString .= "".HelpBox($select, $help_text, 300)."</form>";
	}

	return $mString;
}

function getQnaDiv(){
	global $bbs_div, $db;

	$sql = "select * from shop_product_qna_div where disp='1'";
	$db->query($sql);
	$datas = $db->fetchall("object");
	$return = "";

	if(! empty($datas)){
		foreach($datas as $k => $v){
			$return .= "<option value='".$v[ix]."'".($v[ix] == $bbs_div ? ' selected' : '').">".$v[div_name]."</option>";
		}
	}

	return $return;
}


//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>
