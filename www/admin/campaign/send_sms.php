<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("./mail.config.php");

$db = new Database;
$mdb = new Database;
$sdb = new Database;
$gdb = new Database;
$sms_design = new SMS;

$update_kind = "sms";
$page_title = "SMS 개별/대량발송";
$page_navigation = "메일링/SMS 발송관리 > SMS 개별/대량발송";

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
	if($orderdate && $mode=='search'){
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

	$sql = "SELECT mall_domain FROM shop_shopinfo WHERE mall_domain_key = '$admininfo[mall_domain_key]'";

	$sdb->query($sql);
	$sdb->fetch();
	$domain	=	$sdb->dt['mall_domain'];


	// 전체 갯수 불러오는 부분
	$sql = "SELECT count(*) as total FROM shop_addressbook ab, shop_addressbook_group abg  $where ";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	
	if($QUERY_STRING == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}

	$str_page_bar = page_bar($total, $page,$max, $query_string,"view");

	$sql = "SELECT ab.*, abg.group_name, abg.group_depth, abg.parent_group_ix FROM shop_addressbook ab, shop_addressbook_group abg   $where  order by ab.regdate desc LIMIT $start, $max";
	//echo $sql;
	$db->query($sql);

$Script = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>
<script type='text/javascript'>


function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
		$('#endDate').attr('disabled',false);
		$('#startDate').attr('disabled',false);
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
		$('#endDate').attr('disabled',true);
		$('#startDate').attr('disabled',true);
	}
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
	 
		if(frm.update_kind[0].checked){
			
			$('#byte').html('1000byte');
			$('#msg_type').html('LMS');
			$('#lms_type').html('<input type=hidden name=send_type style=width:100px value=3>');

		}else if(frm.update_kind[1].checked){
			$('#mms_byte').html('1000byte');
			$('#mms_msg_type').html('LMS');
			$('#mms_lms_type').html('<input type=hidden name=send_type style=width:100px value=3>');

		}else if(frm.update_kind[2].checked){
			
			$('#nmb_byte').html('1000byte');
			$('#nmb_msg_type').html('LMS');
			$('#nmb_lms_type').html('<input type=hidden name=send_type style=width:100px value=3>');

		}
	}

	if(sms_txt_length < 80){
	  $('#byte').html('80byte');
	  $('#msg_type').html('SMS');
	  $('#lms_type').html('<input type=hidden name=send_type style=width:100px value=1>');
	}

	if(frm.update_kind[0].checked){
		$('input[name=sms_text_count]').val(sms_txt_length);
		$('.sms_text').val(sms_txt);
	}else if(frm.update_kind[1].checked){
		$('input[name=mms_text_count]').val(sms_txt_length);
		$('.mms_text').val(sms_txt);
	}else if(frm.update_kind[2].checked){
		$('input[name=nmb_text_count]').val(sms_txt_length);
		$('.nmb_text').val(sms_txt);
	}
	
}

// 페이지가 로드될 때 기본적으로 첫번째 tab이 기본적으로 설정되도록 한다
$(document).ready(function(){
	 /*CKEDITOR.replace('basicinfo',{
	  startupFocus : false,height:500
	  });*/

	$('#sms_code_use').click(function(){
		if ($(this).is(':checked')){         
			var sms_text	=	$('.sms_text').val();
			var sms_len		=	$('input[name=sms_text_count]').val();
				sms_text = sms_text +'$domain'+'/sms.php';
				sms_txt_length = sms_text.byteLength();
				sms_text_length = sms_len + sms_txt_length;
			$('.sms_text').val(sms_text);
			$('input[name=sms_text_count]').val(sms_text_length);

		}else{
			var del_txt	=	'$domain'+'/sms.php';
				org_txt	=	$('.sms_text').val();
				in_txt	=	org_txt.replace(del_txt,'');
				$('.sms_text').val(in_txt);
		}
	});

	frm	=	document.list_frm;

	$('#tabs').tabs();
	$('#tabs1').tabs();
	$('#tabs2').tabs();
	getContentTab(1);

	$('#select_delete').click(function(){
		$('input[name=\"mem_ix[]\"]:checked').each(function(){
			$(this).parent().remove();
		});
	});
	
	$('input[name=mem_add]').click(function(){
	
	  var frm		=	document.list_frm;
      var mem_num	=	frm.mem1.value + '-' + frm.mem2.value + '-' + frm.mem3.value;
	  var mem_txt		=	'<div class=non_member><input type=checkbox name=mem_ix[] id=mem_ix value='+mem_num+' />'+mem_num+'<br /></div>';
		
	 if($('input[name=mem1]').val()==''){
		alert('추가하실 전화번호를 입력해주세요');
		$('input[name=mem1]').focus();
		return false;
	 }else if($('input[name=mem2]').val()==''){
		alert('추가하실 전화번호를 입력해주세요');
		$('input[name=mem2]').focus();
		return false;
	 }else if($('input[name=mem3]').val()==''){
		alert('추가하실 전화번호를 입력해주세요');
		$('input[name=mem3]').focus();
		return false;
	 }

	  $('.nmb_area').append(mem_txt);
    });
	
	 $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
	 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');

	 $('input[name=send_time_mms]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=send_time_hour_mms]').attr('disabled',true).css('color','#ccc');
	 $('select[name=send_time_minite_mms]').attr('disabled',true).css('color','#ccc');

	 $('input[name=send_time_nmb]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=send_time_hour_nmb]').attr('disabled',true).css('color','#ccc');
	 $('select[name=send_time_minite_nmb]').attr('disabled',true).css('color','#ccc');

	$('.send_time_now').click(function(){
        if(this.checked){
             $('input[name=send_time_sms]').attr('disabled',true).css('background-color','#fff');
			 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
			 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');
        }
    });

	$('.nmb_send_time_now').click(function(){
        if(this.checked){
             $('input[name=send_time_nmb]').attr('disabled',true).css('background-color','#fff');
			 $('select[name=send_time_hour_nmb]').attr('disabled',true).css('color','#ccc');
			 $('select[name=send_time_minite_nmb]').attr('disabled',true).css('color','#ccc');
        }
    });
	
	
	$('.send_time_reserve').click(function(){
        if(this.checked){
            $('input[name=send_time_sms]').attr('disabled',false).css('background-color','#fff7da');
            $('select[name=send_time_hour]').attr('disabled',false).css('color','#666');
			$('select[name=send_time_minite]').attr('disabled',false).css('color','#666');
        }
    });
    
	$('.nmb_send_time_reserve').click(function(){
        if(this.checked){
            $('input[name=send_time_nmb]').attr('disabled',false).css('background-color','#fff7da');
            $('select[name=send_time_hour_nmb]').attr('disabled',false).css('color','#666');
			$('select[name=send_time_minite_nmb]').attr('disabled',false).css('color','#666');
        }
    });
        

});


function getContentTab(index){

	var targetDiv = '.tabs-' + index; 
	$(targetDiv).html();   // 해당 div에 결과가 나타남
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
	
	if(!frm.update_kind[2].checked){

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

	}else{

		var mem_ix_checked_bool = false;
		for(i=0;i < frm.mem_ix.length;i++){
			if(frm.mem_ix[i].checked){
				mem_ix_checked_bool = true;
			}
			//	frm.mem_ix[i].checked = false;
		}
		if(!mem_ix_checked_bool){
			alert('선택된 수신자가 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요');
			return false;
		}
		
	}
	
	if(frm.update_kind[0].checked){
		if(frm.sms_text.value.length < 1){
			alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
			frm.sms_text.focus();
			return false;
		}
	}

	if(frm.update_kind[1].checked){
		if(frm.sms_text1.value.length < 1){
			alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
			frm.sms_text.focus();
			return false;
		}
	}

	if(frm.update_kind[2].checked){
		if(frm.sms_text2.value.length < 1){
			alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
			frm.sms_text.focus();
			return false;
		}
	}
	

	if(frm.update_type.value == 1){

		if(frm.update_kind[0].checked){
			if(confirm('검색한 회원의 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[1].checked){
			if(confirm('검색한 회원의 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[2].checked){
			if(confirm('검색한 일괄삭제 하시겠습니까?')){return true;}else{return false;}
		}

	} else { 
		if(frm.update_kind[0].checked){
			if(confirm('선택한 회원의 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[1].checked){
			if(confirm('선택한 회원의 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}else if(frm.update_kind[2].checked){
			if(confirm('선택한 비회원의 SMS 발송을 하시겠습니까?')){return true;}else{return false;}
		}
	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_sms','batch_update_mms','batch_update_member');

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
	<input type='hidden' name=mode value='search'>
    <input type='hidden' name=act value='".$act."'>
    <input type='hidden' name=before_update_kind value='".$update_kind."'>";

$Contents .= "
			<tr>
				<td class='search_box_title'>가입 / 등록일
					<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
				</td>
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
		      <td class='search_box_item' colspan='3'>
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
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='addressbook_list.act.php' enctype='multipart/form-data' target='act'>
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
<div id='batch_update_sms' ".($update_kind == "sms" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'>
	<img src='../images/dot_org.gif' align=absmiddle> <b>SMS/LMS/MMS 일괄발송</b>
	<input type='hidden' name='sms_send_type' value='M' />
</div>";
$cominfo = getcominfo();
$help_text .= "
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=170>
	<col width=*>
	<tr>
		<td class='input_box_title'> <b>총 발송예정수 </b></td>
		<td class='input_box_item'><b id='sended_hotcon_cnt' class=blu>0</b> 건 / <b id='remainder_hotcon_cnt'>$total</a> 명</td>
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
	<tr>
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
				<div class='from_sms'>
					<textarea name='sms_text' class='sms_text' onkeydown=\"fc_chk_lms(this,80,1000, this.form.sms_text_count,'sms');\" onkeyup=\"fc_chk_lms(this,80,1000, this.form.sms_text_count,'sms');\" ></textarea>
				</div>
				<div align='right' style='height:30px;line-height:30px;'>
					<b id='msg_type'>SMS</b><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right;width:50px' maxlength=4 value=0> byte/<span id='byte'>80byte</span><span id='lms_type'></span>
				</div>
			</div>
		</td>
		<td valign=top>
			<div class='sms_info'>
				<div>
					<h3>자주쓰는 소스설명</h3>
					<div style='width:100px;margin:20px auto'>
					<p>{name} : 고객명</p>
					<p>{site} : 사이트명</p>
					<p>{} : 사이트 URL</p>
					</div>
				</div>
				<dl>
					<dt>
						<input type='checkbox' name='0' value='sms_code' id='sms_code_use' />
						<label for='sms_code_use'>분석코드 사용<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;클릭/유입/매출분석사용</label>
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
				
				$sql	=	'SELECT sms_title , sms_text FROM shop_sms_box WHERE sms_group = "A" and disp = 1';
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
				$msg_page_bar = page_bar($total, $page,$max, "&max=$max","view");
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
			$help_text.= "
			</div>      
			<div id='tabs-2'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "B" and disp = 1 LIMIT 0,6';
					$gdb->query($sql);
					$total = $gdb->total;

					for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				<tr height=22><td align=left class=small>발송수/발송대상</td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</a> 명</td></tr>
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
<div id='batch_update_mms' ".($update_kind == "mms" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'>
	<img src='../images/dot_org.gif' align=absmiddle> <b>MMS 일괄발송</b>
</div>";
$cominfo = getcominfo();

$help_text .= "
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=170>
	<col width=*>
	<tr>
		<td class='input_box_title'> <b>총 발송예정수 </b><input type=hidden name='hotcon_send_page' value='1'></td>
		<td class='input_box_item'><b id='sended_hotcon_cnt' class=blu>0</b> 건 / <b id='remainder_hotcon_cnt'>$total</a> 명</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>발송구분</b></td>
		<td class='input_box_item'>
			<table cellpadding=0>
				<tr>
				<td>
					<input type='radio' name='mms_send_time_type' value='0' checked ".CompareReturnValue("O",$mms_send_time_type,"checked")." class='mms_send_time_now' id='send_time_now_mms' /><label for='send_time_now_mms'>즉시발송</label>
					<input type='radio' name='mms_send_time_type' value='1' ".CompareReturnValue("1",$mms_send_time_type,"checked")." class='mms_send_time_reserve' id='send_time_reserve_mms' /><label for='send_time_reserve_mms'>예약발송</label>
				</td>
				<td>
				".select_date('send_time_mms')."
				</td>
				<td>
				<SELECT name='send_time_hour_mms'>";
                    for($i=0;$i < 24;$i++){
                        $help_text.= "<option value='".sprintf("%02d", $i)."' ".($sTime == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
                                        }
                        $help_text.= "
                    </SELECT> 시
                    <SELECT name='send_time_minite_mms'>";
                    for($i=0;$i < 60;$i++){
                        $help_text.= "<option value='".sprintf("%02d", $i)."' ".($sMinute == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
                                        }
                        $help_text.= "
                    </SELECT>분
				</tr>
			</table>
		</td>
	</tr>
</table>
<table cellpadding=0 cellspacing=0 width='903' style='margin-top:41px;'>
	<tr>
		<td valign=top>
			<div style='width:178px;'>
				<div style='padding:0 0 14px 10px;'>보내는사람 : <input type=text name='send_phone' class=textbox style='display:inline;' size=12 value='".$cominfo[com_phone]."'></div>
				<div class='from_sms'>
					<textarea class='mms_text' name='mms_text' onkeyup=\"fc_chk_lms(this,80,1000, this.form.mms_text_count,'mms');\" onkeydown=\"fc_chk_lms(this,80,1000, this.form.mms_text_count,'mms');\" ></textarea>
				</div>
				<div align='right' style='height:30px;line-height:30px;'>
					<b id='mms_msg_type'>SMS</b><input type=text name='mms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> byte/<span id='mms_byte'>80byte</span><span id='mms_lms_type'></span>
				</div>
			</div>
		</td>
		<td valign=top>
			<div class='sms_info'>
				<div>
					<h3>자주쓰는 소스설명</h3>
				</div>
				<dl>
					<dt>
						<input type='checkbox' name='sms_code' value='' id='sms_code_use' />
						<label for='sms_code_use'>분석코드 사용<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;클릭/유입/매출분석사용</label>
					</dt>
					<dd>* []사이트 URL의 별도의 코드가 추가되어 유입/로그인/회원가입/매출액 등 상세 분석이 가능합니다.</dd>
					<dd>* 사용시 SMS*1건 처리됩니다.</dd>
					<dd>* 사용시 LMS*3건 처리됩니다.</dd>
				</dl>
			</div>
		</td>
		<td  class='tabs_custom'>
			<div id='tabs1'>
			<ul>
				<li><a href='#tabs-1' onclick='getContentTab(1);'>A그룹</a></li>
				<li><a href='#tabs-2' onclick='getContentTab(2);'>B그룹</a></li>
				<li><a href='#tabs-3' onclick='getContentTab(3);'>C그룹</a></li>
				<li><a href='#tabs-4' onclick='getContentTab(4);'>D그룹</a></li>
				<li><a href='#tabs-5' onclick='getContentTab(5);'>E그룹</a></li>
			</ul>
			<div id='tabs-1'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "A" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
			<div id='tabs-2'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "B" and disp = 1 LIMIT 0,6';
					$gdb->query($sql);
					$total = $gdb->total;

					for($i=0;$i<$total;$i++){
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
			<div id='tabs-3'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "C" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
			<!--<table cellpadding=0 cellspacing=0><input type=hidden name='sms_send_page' value='1' >
				<tr height=26></tr>
				<tr height=22><td align=left class=small>SMS 잔여건수</td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>
				<tr height=22><td align=left class=small>발송수/발송대상</td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</a> 명</td></tr>
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
<div id='batch_update_member' ".($update_kind == "member" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'>
	<img src='../images/dot_org.gif' align=absmiddle> <b>비회원 문자발송</b>
</div>";
$cominfo = getcominfo();

$help_text .= "
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
	<col width=170>
	<col width=*>
	<tr>
		<td class='input_box_title'> <b>총 발송예정수 </b><input type=hidden name='hotcon_send_page' value='1'></td>
		<td class='input_box_item'><b id='sended_hotcon_cnt' class=blu>0</b> 건 / <b id='remainder_hotcon_cnt'>$total</a> 명</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>발송구분</b></td>
		<td class='input_box_item'>
			<table cellpadding=0>
				<tr>
				<td>
					<input type='radio' name='nmb_send_time_type' checked value='0' ".CompareReturnValue("O",$nmb_send_time_type,"checked")." class='nmb_send_time_now' id='send_time_now_nmb' /><label for='send_time_now_nmb'>즉시발송</label>
				<input type='radio' name='nmb_send_time_type' value='1' ".CompareReturnValue("1",$nmb_send_time_type,"checked")." class='nmb_send_time_reserve' id='send_time_reserve_nmb' /><label for='send_time_reserve_nmb'>예약발송</label>
				</td>
				<td>
				".select_date('send_time_nmb')."
				</td>
				<td>
				<SELECT name='send_time_hour_nmb'>";
                    for($i=0;$i < 24;$i++){
                        $help_text.= "<option value='".sprintf("%02d", $i)."' ".($sTime == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
                                        }
                        $help_text.= "
                    </SELECT> 시
                    <SELECT name='send_time_minite_nmb'>";
                    for($i=0;$i < 60;$i++){
                        $help_text.= "<option value='".sprintf("%02d", $i)."' ".($sMinute == sprintf("%02d", $i) ? "selected":"").">".sprintf("%02d", $i)."</option>";
                                        }
                        $help_text.= "
                    </SELECT>분
				</tr>
			</table>
		</td>
	</tr>
</table>
<table cellpadding=0 cellspacing=0 width='903' style='margin-top:41px;'>
	<tr>
		<td valign='top'>
			<div style='width:178px;'>
				<div style='padding:0 0 14px 10px;'>보내는사람 : <input type=text name='send_phone' class=textbox  size=12 value='".$cominfo[com_phone]."'></div>
				<div class='from_sms'>
					<textarea class='nmb_text' name='nmb_text' onkeyup=\"fc_chk_lms(this,80,1000, this.form.nmb_text_count,'nmb');\" onkeydown=\"fc_chk_lms(this,80,1000, this.form.nmb_text_count,'nmb');\" ></textarea>
				</div>
				<div align='right' style='height:30px;line-height:30px;'>
					<b id='nmb_msg_type'>SMS</b><input type=text name='nmb_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> byte/<span id='nmb_byte'>80byte</span><span id='nmb_lms_type'></span>
				</div>
			</div>
		</td>
		<td valign=top>
			<table cellpadding=0 cellspacing=0 style='width:202px;'>
				<tr height=26>
					<td>
						받는사람 : <input type='button' name='mem_add' value='추가' />
					</td>
				</tr>
				<tr height=26>
					<td>
					<input type='text' name='mem1' maxlength=3 style='width:50px' /> - <input type='text' name='mem2' maxlength=4 style='width:50px' /> - <input type='text' name='mem3' maxlength=4 style='width:50px' />
					</td>
				</tr>
				<tr height=26>
				<td>
						<div class='nmb_area' style='width:200px;height:200px;background-color:#fff;border:1px solid #efefef;padding:2px;overflow:hidden;overflow-y:scroll;margin:10px 0;>
						<input type='hidden' name='mem_ix' />
					";
						$sql = "SELECT * FROM shop_addressbook WHERE mbjoin = '0'";
						$sdb->query($sql);
						$total	=	$sdb->total;
						for($i=0;$i<$total;$i++){
							$sdb->fetch($i);
							$help_text.= "
								<div class='non_member'>
									<input type=checkbox name=mem_ix[] id='mem_ix' value='".$sdb->dt[ab_ix]."' />
									".$sdb->dt['user_name'].$sdb->dt['mobile']."<br />
								</div>";
						}
					$help_text.= "
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<input type='checkbox' name='all_fixed' id='all_fixed' onclick='fixAll2(document.list_frm)' />
					<input type='button' value='선택삭제' id='select_delete' >
					</td>
				</tr>
			</table>
		</td>
		<td class='tabs_custom'>
			<div id='tabs2'>
			<ul>
				<li><a href='#tabs-1' onclick='getContentTab(1);'>A그룹</a></li>
				<li><a href='#tabs-2' onclick='getContentTab(2);'>B그룹</a></li>
				<li><a href='#tabs-3' onclick='getContentTab(3);'>C그룹</a></li>
				<li><a href='#tabs-4' onclick='getContentTab(4);'>D그룹</a></li>
				<li><a href='#tabs-5' onclick='getContentTab(5);'>E그룹</a></li>
			</ul>
			<div id='tabs-1'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "A" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
			<div id='tabs-2'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "B" and disp = 1 LIMIT 0,6';
					$gdb->query($sql);
					$total = $gdb->total;

					for($i=0;$i<$total;$i++){
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
			<div id='tabs-3'>";
				$sql	=	'SELECT sms_text FROM shop_sms_box WHERE sms_group = "C" and disp = 1 LIMIT 0,6';
				$gdb->query($sql);
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
				$total = $gdb->total;

				for($i=0;$i<$total;$i++){
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
			<!--<table cellpadding=0 cellspacing=0><input type=hidden name='sms_send_page' value='1' >
				<tr height=26></tr>
				<tr height=22><td align=left class=small>SMS 잔여건수</td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>
				<tr height=22><td align=left class=small>발송수/발송대상</td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</a> 명</td></tr>
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
				<input type='radio' name='update_kind' id='update_kind_sms' value='sms' ".CompareReturnValue("sms",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS/LMS 일괄발송</label>
				<!--<input type='radio' name='update_kind' id='update_kind_mms' value='mms' ".CompareReturnValue("mms",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_mms');\"><label for='update_kind_mms'>MMS 일괄발송</label>-->
				<input type='radio' name='update_kind' id='update_kind_member' value='member' ".CompareReturnValue("member",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_member');\"><label for='update_kind_member'>비등록 회원 문자발송</label>";

				$Contents .= "".HelpBox($select, $help_text,450)."</form>";
}


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='addressbook.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = campaign_menu();
$P->Navigation = $page_navigation;
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

