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
//echo $_SESSION["ss_mail_ix"];
$_SESSION["ss_mail_ix"] = "";

if($update_kind != $_REQUEST['update_kind']){
    $update_kind = $_REQUEST['update_kind'];
}

$Script = "
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
	
	CKEDITOR.replace('mail_content2',{
						startupFocus : false,height:500
					});

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

	

	var checkBatchBool = true;
	function BatchSubmit(frm){
		if(frm.update_type.value == 1 && frm.search_searialize_value.length < 1){		
			alert('적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요');//'적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요'
			return false;
		}

		if($('#update_kind_reserve').attr('checked')){
			if(frm.reserve.value == ''){
				alert('적립금 지금액/차감액을 입력해주세요');//'적립금 지금액/차감액을 입력해주세요'
				frm.reserve.focus();
				return false;
			}

			if(frm.etc.value == ''){
				alert('적립금 적립내용을 입력해주세요');//'적립금 적립내용을 입력해주세요'
				frm.etc.focus();
				return false;
			}
		}else if($('#update_kind_group').attr('checked')){
			if(frm.update_gp_ix.value == ''){
				alert('변경하시고자 하는 회원그룹을 선택해주세요');//'변경하시고자 하는 회원그룹을 선택해주세요'
				if(frm.update_gp_ix.value == '' && !frm.update_gp_ix.disabled){
					frm.update_gp_ix.focus();
				}
				return false;
			}


		}else if($('#update_kind_sms').attr('checked')){
			if(frm.sms_text.value.length < 1){
				alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');//'SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요'
				frm.sms_text.focus();
				return false;
			}
		}else if($('#update_kind_coupon').attr('checked')){
			if($('input[name=coupon_use]').prop('checked') == false){
				alert('발행쿠폰 목록을 선택해 주세요.');
				return false;
			}	
			if(frm.publish_ix.value == ''){
				alert('지급 하시고자 하는 쿠폰을 선택해주세요');//'지급 하시고자 하는 쿠폰을 선택해주세요'
				if(frm.publish_ix.value == ''){
					frm.publish_ix.focus();
				}
				return false;
			}
			
		}else if($('#update_kind_sendemail').attr('checked')){

			if(frm.email_subject.value.length < 1){
				alert('이메일 제목을 입력해주세요');//'이메일 제목을 입력해주세요'
				frm.email_subject.focus();
				return false;
			}

			frm.mail_content.value = $('#iView').contents().find('body').html();

			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'){
				alert('이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요');//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}
		}else if($('#update_kind_coupon').attr('checked')){

			if(frm.email_title.value.length < 1){
				alert('이메일 제목을 입력해주세요');//'이메일 제목을 입력해주세요'
				frm.email_title.focus();
				return false;
			}

			var mail_value = CKEDITOR.instances['mail_content2'].getData();
			
			if(mail_value == ''){
				alert('이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요');//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}

		}
		
		$('.submitBtn').attr('disabled','disabled');
		
		if(frm.update_type.value == 1){
			
			if($('#confirm_bool').val() == '1'){
				if($('#update_kind_reserve').attr('checked')){
					if(!confirm('검색회원 적립금 일괄 지급을 하시겠습니까?')){return false; $('.submitBtn').attr('disabled',false);}//'검색회원 적립금 일괄 지급을 하시겠습니까?'
				}else if($('#update_kind_group').attr('checked')){
					if(!confirm('검색회원 전체의 회원그룹 변경을 하시겠습니까?')){return false; $('.submitBtn').attr('disabled',false);}//'검색회원 전체의 회원그룹 변경을 하시겠습니까?'
				}else if($('#update_kind_sms').attr('checked')){
					if(!confirm('검색회원 전체에게 SMS 발송을 하시겠습니까?')){return false; $('.submitBtn').attr('disabled',false);}//'검색회원 전체에게 SMS 발송을 하시겠습니까?'
				}else if($('#update_kind_coupon').attr('checked')){
					if(!confirm('검색회원 전체에게 쿠폰일괄지급을 하시겠습니까?')){return false; $('.submitBtn').attr('disabled',false);}//'검색회원 전체에게 쿠폰일괄지급을 하시겠습니까?'
				}else if($('#update_kind_sendemail').attr('checked')){
					if(!confirm('검색회원 전체에게 이메일발송을 하시겠습니까?')){return false; $('.submitBtn').attr('disabled',false);}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
				}else if($('#update_kind_bigemail').attr('checked')){
					if(!confirm('검색회원 전체에게 이메일발송을 하시겠습니까?')){return false; $('.submitBtn').attr('disabled',false);}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
				}
			}
			
			
		
		}else if(frm.update_type.value == 2){
			
			var code_checked_bool = false;
			for(i=0;i < frm.code.length;i++){
				if(frm.code[i].checked){
					code_checked_bool = true;
				}
			}
			
			if(!code_checked_bool){
				alert('선택된 회원이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요'); //'선택된 회원이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요'
				// alert(language_data['member_batch.php']['H'][language]);//'선택된 회원이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요'
				return false;
				$('.submitBtn').attr('disabled',false);
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
					console.log(d);
				}
			});
			alert('메일 전송 요청이 완료 되었습니다.');
			return false;
			$('.submitBtn').attr('disabled',false);
		}
		
		if(checkBatchBool == true){
			if(confirm('일괄 처리를 진행 하시겠습니까?')){
				checkBatchBool = false;
				return true;				
			}else{
				$('.submitBtn').attr('disabled',false);
				return false;				
			}
		}else{
			alert('등록중입니다.');
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
/*
	if($update_kind){
		setcookie("member_update_kind",$update_kind, time()+3600000,"/",$HTTP_HOST);
		//$update_kind = $_COOKIE["update_kind"];
	}else if($_COOKIE["member_update_kind"]){
		$update_kind = $_COOKIE["member_update_kind"];
	}else if($before_update_kind){
		$update_kind = $before_update_kind;
	}
*/
/*
	if($_REQUEST['before_update_kind']){
		$update_kind = $_REQUEST['before_update_kind'];
	}
	//echo $_COOKIE["update_kind"];
	if($_COOKIE["member_update_kind"]){
		$update_kind = $_COOKIE["member_update_kind"];
	}else if(!$update_kind){
		$update_kind = "sms";
	}
*/

	$Contents = "


	<table width='100%' border='0' align='center'>
	<tr>
	    <td align='left' colspan=6 > ".GetTitleNavigation("회원정보 일괄관리", "회원관리 > 회원정보 일괄관리 ")."</td>
	</tr>
	</table>
	<table width='100%' border='0' align='center' id='bigemail_menu' ".($_REQUEST['update_kind'] == 'bigemail' ? "style='display:block'" : "style='display:none'" ).">
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
							<td class='box_02' onclick=\"document.location.href='member_batch.php?update_kind=bigemail'\">메일발송</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_03'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='bigemail_list.php'\">발송리스트</td>
							<th class='box_03'></th>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	</table>
	<table width='100%' border='0' align='center'>
	<tr>
		<td>";
	
		
	$Contents .= "
	<form name=searchmember method='get'><!--SubmitX(this);'-->
	<input type='hidden' name='mode' value='search'>
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
				<input type='hidden' name=mode value='search'>	
				<input type='hidden' name=act value='".$act."'>
				<input type='hidden' name=before_update_kind value='".$_REQUEST['update_kind']."'>
				<input type='hidden' name=update_kind value='".$_REQUEST['update_kind']."'>
				<colgroup>
					<col style='width:18%' />
					<col style='width:32%' />
					<col style='width:18%' />
					<col style='width:32%' />";
if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents .= "
					<tr>
						<td class='search_box_title' > 글로벌 회원 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
}
$Contents .= "
				</colgroup>
				<tr height=27>
					<td class='search_box_title' bgcolor='#efefef' align=center>회원그룹
					<img src='../images/icon/search_icon.gif' value='검색' onclick=\"PopSWindow2('./search_category.php?group_code=member',600,600,'add_brand_category')\" align=absmiddle style='cursor:pointer;' />
					</td>
					<td class='search_box_item' align=left style='padding-left:5px;' colspan='3'>
						<table width='98%' cellpadding='0' cellspacing='0' id='objMember'>
						<colgroup>
							<col width='*'>
							<col width='600'>
						</colgroup>
						<tbody>";
							if(count($gid) > 0){
								for($k=0;$k<count($gid);$k++){
									$re_gid = $gid[$k];
									$sql = "SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 and gp_ix	=  '".$re_gid."'";
									$db->query($sql);
									$db->fetch();
								
					$Contents .= "	<tr style='height:26px;' id='row_".$re_gid."'>
										<td>
										<input type='hidden' name='gid[]' id='cid_".$re_gid."' value='".$re_gid."'>".$db->dt['gp_name']."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_gid."')\"><img src='../images/korea/btc_del.gif' border='0'></a>
										</td>
									</tr>";
								}
							}
							$Contents .= "
						</tbody>
						</table>
					</td>
				</tr>
				<!--<tr height=27>
					<td class='search_box_title' bgcolor='#efefef' align=center>이벤트당첨자 
						<img src='../images/icon/search_icon.gif' value='검색' onclick=\"ShowModalWindow('./search_category.php?group_code=event',600,600,'add_brand_category')\" align=absmiddle  style='cursor:pointer;'>
					</td>
					<td class='search_box_item' align=left style='padding-left:5px;' colspan='3'>
					<table width='98%' cellpadding='0' cellspacing='0' id='objEvent'>
									<colgroup>
										<col width='*'>
										<col width='600'>
									</colgroup>
									<tbody>";
										
										if(count($eid) > 0){
								
											for($k=0;$k<count($eid);$k++){

												$re_eid = $eid[$k];
												$sql = "select se.* from ".TBL_SHOP_EVENT." se left join shop_event_relation ser on (se.er_ix=ser.er_ix) where event_ix = '".$re_eid."'";
												$db->query($sql);
												$db->fetch();

								$Contents .= "	<tr style='height:26px;' id='row_".$re_eid."'>
													<td>
													<input type='hidden' name='eid[]' id='cid_".$re_eid."' value='".$re_eid."'>".$db->dt['event_title']."(".date("Y.m.d",$db->dt[event_use_sdate])."~".date("Y.m.d",$db->dt[event_use_edate]).")</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_eid."')\"><img src='../images/korea/btc_del.gif' border='0'></a>
													</td>
												</tr>";
											}
										}
							$Contents .= "
										</tbody>
										</table>
					</td>
				</tr>-->
				<tr height=27>
					<td class='search_box_title' bgcolor='#efefef' align=center>SMS 수신여부 </td>
					<td class='search_box_item' align=left >
						<input type=checkbox name='smssend_yn[]' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신(O)</label>
						<input type=checkbox name='smssend_yn[]' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부(X)</label>
					</td>
					<td class='search_box_title' bgcolor='#efefef' align=center>메일 수신여부 </td>
					<td class='search_box_item' align=left >
						<input type=checkbox name='mailsend_yn[]' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신(O)</label>
						<input type=checkbox name='mailsend_yn[]' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부(X)</label>
					</td>
			    </tr>
				<tr height=27>
					<td class='search_box_title' bgcolor='#efefef' align=center>회원구분 </td>
					<td class='search_box_item' align=left >
						<input type=checkbox name='mem_div[]' value='D' id='buyer'  ".CompareReturnValue("D",$mem_div,"checked")."><label for='buyer'>구매회원</label>
						<input type=checkbox name='mem_div[]' value='S' id='cooper' ".CompareReturnValue("S",$mem_div,"checked")."><label for='cooper'>협력사</label>
						<input type=checkbox name='mem_div[]' value='MD' id='staff' ".CompareReturnValue("MD",$mem_div,"checked")."><label for='staff'>직원</label>
					</td>
					<td class='search_box_title' bgcolor='#efefef' align=center>성별</td>
					<td class='search_box_item' align=left style='padding-left:5px;'>
						<input type=checkbox name='sex[]' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
						<input type=checkbox name='sex[]' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
						<input type=checkbox name='sex[]' value='D' id='sex_all'  ".CompareReturnValue("D",$sex,"checked")."><label for='sex_all'>기타</label>
					</td>
			    </tr>
				<!--tr  height=27>
					<th class='search_box_title' bgcolor='#efefef' width='100' align='center'>가입일
					<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
					</th>
					<td class='search_box_item' colspan='3'>
						".search_date('startDate','endDate',$startDate,$endDate)."
					</td>
				</tr-->
				<tr  height=27>
					<th class='search_box_title'>
						<select name='date_type' style='width:80px;'>
							<option value='m.date' ".CompareReturnValue('m.date',$date_type,' selected').">가입일</option>
							<option value='m.last' ".CompareReturnValue('m.last',$date_type,' selected').">최근로그인</option>
						</select>
						<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked').">
					</th>
					<td class='search_box_item'  colspan=3>
						".search_date('startDate','endDate',$startDate,$endDate)."
					</td>
				</tr>


			    <tr height=27>
					<td class='search_box_title' style='padding-top:5px;'>  검색어
						<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' /></span><br>
						<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> (다중검색 체크)
					</td>
					<td class='search_box_item' colspan='3'>
						<table cellpadding=0 cellspacing=0 border='0'>
						<tr>
							<td valign='top'>
								<div style='padding-top:5px;'>
								<select name='search_type' id='search_type'  style=\"font-size:12px;\">
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type).">회원명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type).">회원ID</option>
									<option value='cmd.pcs' ".CompareReturnValue("cmd.pcs",$search_type).">핸드폰번호</option>
								</select>
								</div>
							</td>
							<td style='padding:5px;'>
								<div id='search_text_input_div'>
									<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
								</div>
								<div id='search_text_area_div' style='display:none;'>
									<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
								</div>
							</td>
							<td>
								<div>
									<span class='small blu' > * 다중 검색은 검색어가 정확히 일치하는 값만 검색 됩니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
								</div>
							</td>
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
	</table>";

	$Contents .= "
			</td>
		</tr>
		<tr height=50>
	    	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
	    </tr>
	</table><br></form>";

if($mode == "search"){
include "member_query2.php";
}

$Contents .= "
	<form id='list_form' name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='member_batch.act.php' target='act' enctype='multipart/form-data' >
	<input type='hidden' name='confirm_bool' id='confirm_bool' value='1'>
	<input type='hidden' name='code[]' id='code'>
	<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
	<div id='result_area'>
	<div style='padding:4px;'>회원수 : ".number_format($total)." 명</div>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<tr height='34' style='font-weight:bold' bgcolor='#ffffff'>
		<td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td width='4%' align='center' class='m_td' ><font color='#000000'><b>번호</b></font></td>
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>가입일</b></font></td>
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>국내<br>해외</b></font></td>
		<td width='7%' align='center' class='m_td'><font color='#000000'><b>최근로그인</b></font></td>
		<td width='8%' align='center' class=m_td><font color='#000000'><b>그룹</b></font></td>
		<td width='12%' align='center' class=m_td><font color='#000000'><b>성명(ID)</b></font></td>
		<td width='10%' align='center' class=m_td><font color='#000000'><b>핸드폰</b></font></td>
		<td width='10%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
		<td width='4%' align='center' class=m_td><font color='#000000'><b>SMS<br>수신여부</b></font></td>
		<td width='4%' align='center' class=m_td><font color='#000000'><b>이메일<br>수신여부</b></font></td>
		<td width='5%' align='center' class=m_td><font color='#000000'><b>관리</b></font></td>";
		/*
	    <td width='7%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>
		if($admininfo[mall_type] != "H"){
$Contents .= "
	    <td width='7%' align='center' class=m_td><font color='#000000'><b>적립금</b></font></td>";
		}
		$Contents .= "
			<td width='10%' align='center' class=m_td><font color='#000000'><b>최종로그인</b></font></td>
	    <td width='10%' align='center' class=e_td><font color='#000000'><b>메일링</b></font></td>
	  </tr>";*/

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
            $nationality = GetDisplayDivision($db->dt['mall_ix'], "text");
	$Contents .= "
	  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
	    <td class='list_box_td list_bg_gray' align='center' ><input type=checkbox name=code[] id='code' value='".$db->dt[code]."' onClick='input_check_num()'></td>
	    <td class='list_box_td' align='center' >".$no."</td>
	    <td class='list_box_td list_bg_gray' align='center'><span title=''>".$db->dt[regdate]."</span></td>
	    <td class='list_box_td list_bg_gray' align='center'><span title=''>".$nationality."</span></td>
		<td class='list_box_td list_bg_gray' align='center'><span title=''>".$db->dt[last]."</span></td>
	    <td class='list_box_td point' align='center' nowrap>".$db->dt[gp_name]."</td>
	    <td class='list_box_td list_bg_gray' align='center' ><a href=\"javascript:PopSWindow2('member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\"><b>".wel_masking_seLen($db->dt[name], 1, 1)."(".$db->dt[id].")</b></a></td>
		<td class='list_box_td list_bg_gray' align='center' ><b>".(($_REQUEST['update_kind'] == "sendemail" || $_REQUEST['update_kind'] == "coupon" || $_REQUEST['update_kind'] == "reserve") ? wel_masking("P", $db->dt[pcs]) : wel_masking("P", $db->dt[pcs]))."</b></td>
		<td class='list_box_td list_bg_gray' align='center' ><b>".(($_REQUEST['update_kind'] == "sms" || $_REQUEST['update_kind'] == "coupon" || $_REQUEST['update_kind'] == "reserve") ? wel_masking("E", $db->dt[mail]) : wel_masking("E", $db->dt[mail]))."</b></td>
		<td class='list_box_td' align='center' >".($db->dt[sms]=='1'?"O":"X")."</td>
	    <td class='list_box_td' align='center' >".($db->dt[info]=='1'?"O":"X")."</td>
		";
			$Contents .= "
			<td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			if($update_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow2('member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle alt='고객상담' title='고객상담' ></a> ";
			}

			if($update_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" style='cursor:pointer;' alt='수정' title='수정'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정' ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				//$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" style='cursor:pointer;' alt='삭제' title='삭제'/> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
			if($create_auth){
				 $Contents .= "
				 <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" style='cursor:pointer;' alt='문자발송' title='문자발송'>
				 <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" style='cursor:pointer;' alt='이메일발송' title='이메일발송'>
				 ";
			}else{
				$Contents .= "
				 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle alt='문자발송' title='문자발송'></a>
				 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle alt='이메일발송' title='이메일발송'></a>
				 ";
			}
		$Contents .= "
		</td>
	    ";/*<td class='list_box_td' align='center' >".$db->dt[visit]."</td>
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
		$Contents .= "*/
	";
	  </tr>
		";
		}

	if (!$db->total){

	$Contents .= "
	  <tr height=50>
	    <td class='list_box_td' colspan='13' align='center'>";
		if($mode == "search"){
			if($search_false){
				$Contents .= "조회하시고자 하는 검색조건을 선택후 검색해주세요";
			}else{
				$Contents .= "검색결과에 맞는 회원 데이타가 없습니다.";
			}
		}else{
			$Contents .= "조회하시고자 하는 검색조건을 선택후 검색해주세요";
		}
		$Contents .= "
		</td>
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
	<div id='batch_update_reserve' ".(($_REQUEST['update_kind'] == "reserve" || $update_kind == "") ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>마일리지 일괄변경</b> <span class=small style='color:gray'><!--적립금 금액 및 내용을 입력후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=170>
		<col width=*>
		<tr>
			<td class='input_box_title'> <b>마일리지 지급액 / 사용액</b></td>
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
					<!--option value=0>적립대기</option-->
					<option value=1>적립완료</option>
					<option value=2>사용내역</option>
					<!--option value=5>반품</option-->
					<!--option value=9>주문취소</option-->
				</select>
			</td>
		</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' class='submitBtn'>";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;'></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>
	</div>
	<div id='batch_update_group' ".($_REQUEST['update_kind'] == "group" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>회원그룹 일괄변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 회원그룹 선택후 저장 버튼을 클릭해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% style='border:1px solid #e2e2e2;'>
		<col width=200>
		<col width=*>

		<tr>
			<td bgcolor='#efefef'>
				 <b>회원그룹</b>
				<input type='checkbox' name='mem_group_use' id='mem_group_use' id='bir' value='1' onclick=\"if(this.checked){\$('#update_gp_ix').removeAttr('disabled');}else{\$('#update_gp_ix').attr('disabled','disabled');}\">
			</td>
			<td >".makeGroupSelectBox($mdb,"update_gp_ix",$update_gp_ix, " disabled")." <span class=small style='color:gray'><!--회원그룹 변경에 따라 회원등급이 자동 변경됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D' )."</span></td></tr>
		<!--tr height=1><td colspan=4 class='dot-x'></td></tr>
		<tr>
			<td bgcolor='#efefef'> <b>회원등급</b><input type='checkbox' name='mem_level_use' id='bir' value='1' onclick=\"if(this.checked){\$('update_gp_level').disabled = false;}else{\$('update_gp_level').disabled = true;}\"></td>
			<td>
			".makeGroupLevelSelectBox($mdb,"update_gp_level",$update_gp_level, " disabled")."
			</td>
		</tr-->
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=30><td colspan=4 align=left><span class=small style='color:gray'><!--회원그룹 변경시 회원 등급이 자동으로 변경됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E' )."</span></td></tr>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' class='submitBtn'>";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;'></a>";
            }
            $help_text .= "
                
            </td>
        </tr>
	</table>
	</div>
	<div id='batch_update_sms' ".($_REQUEST['update_kind'] == "sms" ? "style='display:block'":"style='display:none'")." >
	
	<div style='padding:20px 0 4px 0'>
	<img src='../images/dot_org.gif' align=absmiddle> <b>SMS/LMS/MMS 일괄발송</b>
	<input type='hidden' name='sms_send_type' value='M' />
</div>";
$cominfo = getcominfo();
$help_text .= "<input type=hidden name='sms_send_page' value='1' >
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=170>
	<col width=*>
	<tr height='22'>
			<td align='left' class='input_box_title'>발송수량(1회) : </td>
			<td class='input_box_item'>
			<select name='max'>
				<option value='5'>5</option>
				<option value='10'>10</option>
				<option value='20'>20</option>
				<option value='50'>50</option>
				<option value='100' selected=''>100</option>
				<option value='200'>200</option>
				<option value='300'>300</option>
				<option value='400'>400</option>
				<option value='500'>500</option>
				<option value='1000'>1000</option>
			</select>
			<input type='checkbox' name='stop' id='stop'><label for='stop'>정지</label>
			</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>총 발송예정수 </b></td>
		<td class='input_box_item'>
		<b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</a> 명

		
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>발송구분</b></td>
		<td class='input_box_item'>
			<table cellpadding=0>
				<tr>
				<td>
				<input type='radio' name='send_time_type' checked value='0' ".CompareReturnValue("O",$send_time_type,"checked")." class='send_time_now' id='send_time_now' /><label for='send_time_now'>즉시발송</label>
				<input type='radio' name='send_time_type' value='1' ".CompareReturnValue("1",$send_time_type,"checked")." class='send_time_reserve' id='send_time_reserve' /><label for='send_time_reserve'>예약발송</label>
				</td>
				<td>
				".select_date('send_time_sms')."
				</td>
				<td>
				<select name='send_time_hour'>";
                    for($i=0;$i < 24;$i++){
                        $help_text.= "<option value='".sprintf("%02d", $i)."' ".($sTime == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
                                        }
                        $help_text.= "
                    </select> 시
                    <select name='send_time_minite'>";
                    for($i=0;$i < 60;$i++){
                        $help_text.= "<option value='".sprintf("%02d", $i)."' ".($sMinute == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
                                        }
                        $help_text.= "
                    </select>분
				</tr>
			</table>
		</td>
	</tr>
	<tr style='display: none;'>
		<td class='input_box_title'><b>MMS (300Kbyte) </b></td>
		</td>
		<td class='input_box_item'>
			<input type='file' name='mms_file' /> 
		</td>
	</tr>
</table>
<table cellpadding=0 cellspacing=0 width='903' style='margin-top:41px;'>
	<tr>
		<td  valign=top>
			<div style='width:178px;'>
				<div style='padding:0 0 14px 10px;'>보내는사람 : <input type=text name='send_phone' class=textbox size=12 value='".$cominfo[com_phone]."'></div>
				<div style='padding:0 0 14px 10px;' id='lms_title' style='display:block' >LMS 제목 : <input type=text name='lms_title' class=textbox size=12 value='제목없음'></div>
				<input type='hidden' name='sms_text_array[]' id='sms_text_array' value='' />
				<div class='from_sms'>
					<textarea name='sms_text' class='sms_text' onkeyup=\"fc_chk_lms(this,80,1000, this.form.sms_text_count,'sms');\" ></textarea>
				</div>
				<div align='right' style='height:30px;line-height:30px;'>
					<b id='msg_type'>SMS</b><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right;width:50px' maxlength=4 value=0> byte/<span id='byte'>80byte</span><span id='lms_type'></span>
				</div>
			</div>
		</td>
		<td valign=top>
			<div id = 'select_sms_type'></div>	
			<div class='sms_info'>
				<div>
					<h3>자주쓰는 소스설명</h3>
					<div style='width:100px;padding-left:10px;'>
					<p>{name} : 고객명</p>
					<p>{id} : 고객ID</p>
					</div>
				</div>
				<dl style='display:none;'>
					<dt>
						<input type='checkbox' name='0' value='sms_code' id='sms_code_use' />
						<label for='sms_code_use'>분석코드 사용(준비중)<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;클릭/유입/매출분석사용</label>
					</dt>
					<dd>* []사이트 URL의 별도의 코드가 추가되어 유입/로그인/회원가입/매출액 등 상세 분석이 가능합니다.</dd>
					<dd>* 사용시 SMS*2건 처리됩니다.</dd>
					<dd>* 사용시 LMS*4건 처리됩니다.</dd>
				</dl>
			</div>
		</td>
		<td class='tabs_custom'>	
		<div id='tabs'>
			<ul>
				<li><a href='#tabs-1' onclick='getContentTab(1);'>A그룹</a></li>
				<li><a href='#tabs-2' onclick='getContentTab(2);'>B그룹</a></li>
				<li><a href='#tabs-3' onclick='getContentTab(3);'>C그룹</a></li>
				<li><a href='#tabs-4' onclick='getContentTab(4);'>D그룹</a></li>
				<li><a href='#tabs-5' onclick='getContentTab(5);'>E그룹</a></li>
			</ul>
			<div id='tabs-1'>";
				$sql	=	'SELECT sms_title , sms_text FROM shop_sms_box WHERE sms_group = "A" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total2 = $gdb->total;
				$max = 6; //페이지당 갯수

				if ($page == '')
				{
					$start = 0;
					$page  = 1;
				}
				else
				{
					$start = ($page - 1) * $max;
				}
				$sql	=	'SELECT sms_title , sms_text FROM shop_sms_box WHERE sms_group = "A" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$msg_page_bar = page_bar($total2, $page,$max, "&max=$max","view");
				for($i=0;$i<$gdb->total;$i++){
					$gdb->fetch($i);
					$help_text.= "
						<div class='sms_area_box'>
							<div style='padding:0 0 5px 2px;'>제목 : ".$gdb->dt['sms_title']."</div>
							<div class='sms_areas'>
								<div class='sms_area'>".$gdb->dt['sms_text']."</div>
							</div>
							<p style='width:140px;padding:0;margin:0;height:25px;line-height:25px;text-align:right;'><span>".mb_strlen($gdb->dt['sms_text'],"CP949")."/80 byte</span></p>
							<img src='../images/btn_apply.png' onclick='use_templet($(this))' class='btn_apply' />
						</div>
					";
					if($i %3 == 2){
					$help_text.= "<div style='padding:0 0 16px 12px;margin:0 auto;dispaly:block'><img src='../images/sms_area_gap.gif' /></div>";
					}
				}
				$help_text.= "<div style='padding:0 0 16px 12px;'>".$msg_page_bar."</div>";
			$help_text.= "
			</div>      
			<div id='tabs-2'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "B" and disp = 1 LIMIT 0,6';
					$gdb->query($sql);
					$total2 = $gdb->total;

					for($i=0;$i<$total2;$i++){
						$gdb->fetch($i);
						$help_text.= "
							<div class='sms_area_box'>
								<div style='padding:0 0 5px 2px;'>제목 : ".$gdb->dt['sms_title']."</div>
								<div class='sms_areas'>
									<div class='sms_area'>".$gdb->dt['sms_text']."</div>
								</div>
								<p style='width:140px;padding:0;margin:0;height:25px;line-height:25px;text-align:right;'><span>".mb_strlen($gdb->dt['sms_text'],'CP949')."/80 byte</span></p>
								<img src='../images/btn_apply.png' onclick='use_templet($(this))' class='btn_apply' />
							</div>
						";
					}
				$help_text.= "
			</div>
			<div id='tabs-3'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "C" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total2 = $gdb->total;

				for($i=0;$i<$total2;$i++){
					$gdb->fetch($i);
					$help_text.= "
						<div class='sms_area_box'>
							<div style='padding:0 0 5px 2px;'>제목 : ".$gdb->dt['sms_title']."</div>
							<div class='sms_areas'>
								<div class='sms_area'>".$gdb->dt['sms_text']."</div>
							</div>
							<p style='width:140px;padding:0;margin:0;height:25px;line-height:25px;text-align:right;'><span>".mb_strlen($gdb->dt['sms_text'],'euc-kr')."/80 byte</span></p>
							<img src='../images/btn_apply.png' onclick='use_templet($(this))' class='btn_apply' />
						</div>
					";
				}
				$help_text.= "
			</div>      
			<div id='tabs-4'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "D" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total2 = $gdb->total;

				for($i=0;$i<$total2;$i++){
					$gdb->fetch($i);
					$help_text.= "
						<div class='sms_area_box'>
							<div style='padding:0 0 5px 2px;'>제목 : ".$gdb->dt['sms_title']."</div>
							<div class='sms_areas'>
								<div class='sms_area'>".$gdb->dt['sms_text']."</div>
							</div>
							<p style='width:140px;padding:0;margin:0;height:25px;line-height:25px;text-align:right;'><span>".mb_strlen($gdb->dt['sms_text'],'euc-kr')."/80 byte</span></p>
							<img src='../images/btn_apply.png' onclick='use_templet($(this))' class='btn_apply' />
						</div>
					";
				}
			$help_text.= "
			</div>      
			<div id='tabs-5'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "E" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total2 = $gdb->total;

				for($i=0;$i<$total2;$i++){
					$gdb->fetch($i);
					$help_text.= "
						<div class='sms_area_box'>
							<div style='padding:0 0 5px 2px;'>제목 : ".$gdb->dt['sms_title']."</div>
							<div class='sms_areas'>
								<div class='sms_area'>".$gdb->dt['sms_text']."</div>
							</div>
							<p style='width:140px;padding:0;margin:0;height:25px;line-height:25px;text-align:right;'><span>".mb_strlen($gdb->dt['sms_text'],'euc-kr')."/80 byte</span></p>
							<img src='../images/btn_apply.png' onclick='use_templet($(this))' class='btn_apply' />
						</div>
					";
				}
			$help_text.= "
			</div>      
		</div>
			<!--<table cellpadding=0 cellspacing=0><input type=hidden name='sms_send_page' value='1 LIMIT 0,6' >
				<tr height=26></tr>
				<tr height=22><td align=left class=small>SMS 잔여건수</td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>
				<tr height=22><td align=left class=small>발송수/발송대상</td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</b> 명</td></tr>
				<tr height=22>
						<td align=left class=small>발송수량(1회)</td>
						<td>
						<select name=sms_max>
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
				<tr height=22><td align=left class=small>일시정지 : </td><td><input type='checkbox' name='stop' id='sms_stop'><label for='sms_stop'>정지</label></td></tr>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                    $help_text.="
				    <tr height=50><td align=center colspan=3><input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0> </td></tr>";
                }else{
                    $help_text.="
				    <tr height=50><td align=center colspan=2><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0></a> </td></tr>";
                }
                $help_text.="
			</table>-->
		</td>
	</tr>
	";
	;
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                    $help_text.="
				    <tr height=50><td align=center colspan=2><input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0> </td></tr>";
                }else{
                    $help_text.="
				    <tr height=50><td align=center colspan=2><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0></a> </td></tr>";
                }
                $help_text.="
</table>
</div>
	<div id='batch_update_coupon' ".($_REQUEST['update_kind'] == "coupon" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>쿠폰 일괄지급</b> <span class=small style='color:gray'><!--지급 하시고자하는 쿠폰을 선택해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=170>
		<col width=*>
		<tr height=30>
			<td class='input_box_title'>
				 <b>발행쿠폰 목록</b>
				<input type='checkbox' name='coupon_use' id='bir' value='1' onclick=\"if(this.checked){\$('#publish_ix').removeAttr('disabled');}else{\$('#publish_ix').attr('disabled','disabled');}\">
			</td>
			<td class='input_box_item'>".CouponPublishSelectBox($mdb,"publish_ix", " disabled")." <span class=small style='color:gray'><!--기 발행된 쿠폰 목록입니다. -->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H' )."</span>
			
				쿠폰 지급 개수 : <input type='text' name='coupon_publish_cnt' id='coupon_publish_cnt' value='1' size='2' maxlength='2' style='text-align: center' onkeydown='onlyNumber(this)' onkeyup='onlyNumber(this)'/> 			
			</td>
		</tr>

	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=30><td colspan=4 align=left><span class=small style='color:gray'><!--선택된 회원에게 쿠폰이 발급됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I' )."</span></td></tr>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' class='submitBtn'>";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>
	</div>
	<div id='batch_update_sendemail' ".($_REQUEST['update_kind'] == "sendemail" ? "style='display:block'":"style='display:none'")." >
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
						<input type='radio' name='email_type' id='email_type_new' value='new' checked onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
						<!-- 
						<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
						<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label> -->
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
	<div id='batch_update_bigemail' ".($_REQUEST['update_kind'] == "bigemail" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif' align=absmiddle> <b>대량email 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 email 을 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=15%>
		<col width=35%>
		<col width=15%>
		<col width=35%>
		<tr height=22><input type=hidden name='big_email_send_page' value='1'>
			<td class='input_box_title'> <b>발송수/발송대상 </b> </td>
			<td class='input_box_item'><b id='sended_bigemail_cnt' class=blu>0</b> 건 / <b id='remainder_bigemail_cnt'>$total</b> 명</td>
			<td class='input_box_title'> <b>발송수량(1회) </b> </td>
			<td class='input_box_item'>
				<select name=bigemail_max>
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
				<input type='checkbox' name='stop' id='bigemail_stop'><label for='bigemail_stop'>정지</label>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>발송구분</b></td>
			<td class='input_box_item'>
				<table cellpadding=0>
					<tr>
					<td>
					<input type='radio' name='email_send_time_type' checked value='0' ".CompareReturnValue("O",$send_time_type,"checked")." class='email_send_time_now' id='email_send_time_now' /><label for='email_send_time_now'>즉시발송</label>
					<input type='radio' name='email_send_time_type' value='1' ".CompareReturnValue("1",$send_time_type,"checked")." class='email_send_time_reserve' id='email_send_time_reserve' /><label for='email_send_time_reserve'>예약발송</label>
					</td>
					<td>
					".select_date('send_time_email')."
					</td>
					<td>
					<select name='email_time_hour'>";
						for($i=0;$i < 24;$i++){
							$help_text.= "<option value='".sprintf("%02d", $i)."' ".($sTime == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
											}
							$help_text.= "
						</select> 시
						<select name='email_time_minite'>";
						for($i=0;$i < 60;$i++){
							$help_text.= "<option value='".sprintf("%02d", $i)."' ".($sMinute == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
											}
							$help_text.= "
						</select>분
					</tr>
				</table>
			</td>
			<td class='input_box_title'> <b>이메일수신거부 하단삽입</b></td>
			<td class='input_box_item'>
				<input type='radio' name='sendno_yn' value='Y' id='sendno_y' /><label for='sendno_y'>사용함</label>
				<input type='radio' name='sendno_yn' value='N' id='sendno_n' /><label for='sendno_n'>사용안함</label>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>보내는사람</b></td>
			<td class='input_box_item'>
				<input type='text' name='sender' class=textbox />
			</td>
			<td class='input_box_title'> <b>보내는 메일주소</b></td>
			<td class='input_box_item'>
				<input type='text' name='sendermail' class=textbox />
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일 발송구분</b></td>
			<td class='input_box_item' colspan=3>
				<table cellpadding=0>
					<tr>
						<td>
						<input type='radio' name='email_type_select' id='bigemail_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail2('new');\">
						<label for='bigemail_type_new'>새로작성</label>
						<input type='radio' name='email_type_select' id='bigemail_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail2('box');\">
						<label for='bigemail_type_box'>기존이메일선택</label>
						<div id='email_list' style='display:none'>
						".getMailList("","","width:250px;")."
						</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>이메일 제목</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='email_title' id='email_title'   class=textbox value=''  style='width:250px;height:21px;padding:0px;margin:0px;' >					
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>첨부파일</b></td>
			<td class='input_box_item' colspan=3>
				<input type='file' name='email_file' />
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>참조</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='mail_cc_all'  class=textbox value='' style='width:350px' > <span class='small blu'><!--콤마(,) 구분으로 이메일을 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K' )."</span>
			</td>
		</tr>
		<tr>
			<td class='input_box_item' style='padding:0px;' colspan=4><textarea name='mail_content2' id='mail_content2'></textarea></td>
		</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>
                <input type=checkbox name='save_mail' id='save_mail' value='1' align=absmiddle>
				<input type='hidden' name='save' value='1' />
                <label for='save_mail'>자주쓰는 메일 저장하기</label>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                    $help_text .= "
                    <input class='btn-submit' type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle >";
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
	</select>";

    if($update_kind == 'group'){
		$select .= "<input type='radio' name='update_kind' id='update_kind_group' value='group' ".(($_REQUEST['update_kind'] == "group" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_group');\"><label for='update_kind_group'>회원그룹 일괄변경</label>";
	}
	if($update_kind == 'sms'){
        $select .= "<input type='radio' name='update_kind' id='update_kind_sms' value='sms' ".CompareReturnValue("sms",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS 일괄발송</label>";
	}
	if($update_kind == 'sendemail'){
        $select .= "<input type='radio' name='update_kind' id='update_kind_sendemail' value='sendemail' ".CompareReturnValue("sendemail",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sendemail');\"><label for='update_kind_sendemail'>이메일 일괄발송</label>";
	}
	if($update_kind == 'bigemail'){
        $select .= "<input type='radio' name='update_kind' id='update_kind_bigemail' value='bigemail' ".CompareReturnValue("bigemail",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_bigemail');\"><label for='update_kind_bigemail'>대량이메일 일괄발송</label>";
	}


	if($admininfo[mall_type] != "H"){

		if($update_kind == 'coupon'){
		$select .= "
			<input type='radio' name='update_kind' id='update_kind_coupon' value='coupon' ".CompareReturnValue("coupon",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_coupon');\"><label for='update_kind_coupon'>쿠폰 일괄지급</label>";
        }
		if($update_kind == 'reserve'){
		$select .= "
			<input type='radio' name='update_kind' id='update_kind_reserve' value='reserve' ".(($update_kind == "reserve" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_reserve');\"><label for='update_kind_reserve'>적립금 일괄지급</label>";
		}
	}
$select .= "
	</nobr>";

	if($admininfo[mall_type] == "H"){
		$Contents .= "".HelpBox($select, $help_text, 700)."</form>";
	}else{
		$Contents .= "".HelpBox($select, $help_text, 900)."</form>";
	}

	$Contents .= "
	<form name='lyrstat'>
		<input type='hidden' name='opend' value=''>
	</form>";

$Contents .= "<iframe name='test_act' id='act' width=600 height=600 frameborder=0 ></iframe>";

	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='member.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script type='text/javascript' src='../ckeditor/ckeditor.js'></script>".$Script;
	$P->OnloadFunction = "init();Init(document.list_frm);";
	$P->strLeftMenu = member_menu();
	$P->jquery_use = true;
	$P->prototype_use = false;
	switch($_REQUEST['update_kind']) {
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
		case("bigemail") :
			$P->Navigation = "HOME > 회원관리 > 대량 이메일 일괄발송";
			$P->title = "대량 이메일 일괄발송";
		break;
		default :
			$P->Navigation = "HOME > 회원관리 > 회원정보 일괄관리";
			$P->title = "회원정보 일괄관리";
		break;
	}
	$P->strContents = $Contents;
	echo $P->PrintLayOut();


//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>