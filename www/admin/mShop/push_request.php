<?php
include("../class/layout.class");


$Script = "<script type='text/javascript'>
$(function(){
	$('#push_form').submit(function(){
		if($('input[name=title]').val() == ''){
			alert('제목을 입력해주세요');
			$(this).focus();
			return false;
		}

		$('#submit_image').hide();
	});
	$('#push_text').click(function(){
		$('#contents').show();
		$('#upfile').hide();
		$('#notifile').hide();
	});
   $('#push_img').click(function(){
		$('#contents').hide();
		$('#notifile').hide();
		$('#upfile').show();
	});
	$('#noti_img').click(function(){
		$('#contents').show();
		$('#upfile').hide();
		$('#notifile').show();
	});
})
</script>";

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
			<tr>
				<td align='left' colspan=6 > ".GetTitleNavigation("푸시메시지 발송", "모바일 관리 > 푸시메시지 관리")."</td>
			</tr>
		<tr>
			<td>";
		$mstring .= "
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5px'>
				<form name=push_form method='post' id='push_form' action='/admin/mobile/appapi/pushService/request.php' enctype='multipart/form-data' target='act' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>
					<tr height=30>
					  <td class='search_box_title'>푸시제목</td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>  
						  <input type=text name='title' class='textbox' value='".$title."' style='width:30% ; vertical-align:top;' >
						  <b>※ 푸시제목을 입력 안하시면 [상점명]으로 보내지게 됩니다.</b>
					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>관리용제목</td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>  
						  <input type=text name='push_title' class='textbox' value='".$push_title."' style='width:30% ; vertical-align:top;' >
						  <b>※ 제목은 발송한 목록에서 확인하기 위한 텍스트로 실제 푸시메시지에는 발송되지 않습니다.</b>
					  </td>
					</tr>
					<tr height=30>
						<td class='search_box_title'>
							푸시발송타입
						</td>
						<td class='search_box_item' colspan='3'>
							<input type='radio' value='txt' id='push_text' name='contents_type' checked /><label for='push_text'>텍스트</label>
							<!--input type='radio' value='img' id='push_img' name='contents_type' /><label for='push_img'>이미지</label-->
							<input type='radio' value='noti_img' id='noti_img' name='contents_type' /><label for='noti_img'>노티이미지</label>
							<!--&nbsp;&nbsp;<b>※ 이미지 푸시메시지는 안드로이드 APP에만 발송 됩니다.</b> -->
						</td>
					</tr>
					<tr height=630>
						<td class='search_box_title'>
							푸시내용
						</td>
						<td class='search_box_item' colspan='3' style='padding:15px;'>
							<div id='contents'>
								<textarea name='contents' class='textbox' style='width:150px;vertical-align:top;resize:none;margin:10px 0' onkeyup=\"fc_chk_byte(this,200, this.form.sms_text_count,'sms');\" ></textarea>
								<br />
								<input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right;width:50px;padding-left:50px' maxlength=4 value=0> byte/<span id='byte'>200byte</span><span id='lms_type'></span><br />
								<b>※ 푸시 메시지 내용은 최대 200byte(한글 100자)까지 입력 하실 수 있습니다.</b> <br/> <br/>
							</div>
							<div id='upfile' style='display:none'>
								<input type='file' name='push_img' />
								<b>※ 권장사이즈 : 578*987 사이즈의 이미지를 권장합니다.</b>
							</div>
							<div id='notifile' style='display:none'>
								<input type='file' name='noti_img' />
								<b>※ 권장사이즈 : 892*393 사이즈의 이미지를 권장합니다.</b> 
							</div>
						</td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>푸시링크</td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>  
						  <input type=text name='link' class='textbox' value='' style='width:97% ; vertical-align:top;' >
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
		$mstring .= "
			</td>
		</tr>
		<tr >
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/btn_send.gif' align=absmiddle id='submit_image' ></td>
		</tr>
		</form>
	</table>";


$help_text = '앱에서 푸시알림 허용한 사용자에게 메시지를 발송합니다.';
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>푸시메시지 발송</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;

$P = new LayOut();
$P->addScript = "".$Script;
//$P->OnloadFunction = "init();";
$P->Navigation = "모바일샵관리 > 푸시메시지 > 메시지 발송";
$P->title = "메시지 발송";
$P->strLeftMenu = mshop_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();
