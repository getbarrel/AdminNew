<?
	include("../class/layout.class");
	$db = new Database;

	if($code){
		//회원정보가져오기
		$sql = "SELECT 
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs ,
					AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail ,
					AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel ,cmd.gp_ix, cmd.level_ix, cmd.sex_div,cu.id , cu.code , cu.mem_type ,
					cu.mileage,cu.point,cu.deposit
				FROM 
					common_member_detail cmd
				LEFT JOIN
					common_user cu
				ON (cmd.code = cu.code)
				WHERE cmd.code = '$code'
				";
		$db->query($sql);
		$result = $db->fetch();
	}

	$db->query("SELECT * FROM ".TBL_SHOP_MAILSEND_CONFIG." where mc_ix= '$mc_ix'");
	$db->fetch();

	if($db->total){
		$mc_ix = $db->dt[mc_ix];
		$mc_title = $db->dt[mc_title];
		$mc_mail_title = $db->dt[mc_mail_title];
		$mc_mail_text = $db->dt[mc_mail_text];
		$mc_sms_text = $db->dt[mc_sms_text];

		if($db->dbms_type == "oracle"){
			$mc_mail_text = str_replace("\\",'',$mc_mail_text);
			$mc_sms_text = str_replace("\\",'',$mc_sms_text);
		}

		$mc_code = $db->dt[mc_code];
		$mc_mail_adminsend_yn = $db->dt[mc_mail_adminsend_yn];
		$mc_mail_usersend_yn = $db->dt[mc_mail_usersend_yn];
		$mc_sms_adminsend_yn = $db->dt[mc_sms_adminsend_yn];
		$mc_sms_usersend_yn = $db->dt[mc_sms_usersend_yn];
		$act = "update";
	}else{
		$act = "insert";
		$mc_mail_adminsend_yn = "Y";
		$mc_mail_usersend_yn = "Y";
		$mc_sms_adminsend_yn = "N";
		$mc_sms_usersend_yn = "N";
	}

	if($mc_code != ""){
		$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/mallstory_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
	}

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type='text/javascript' src='../js/facebox.js'></Script>
<LINK href="../css/facebox.css" type="text/css" rel="stylesheet">
<style type="text/css">
	body {margin:0px; padding:0px;}
	body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,form,fieldset,legend,input,textarea,button{margin:0;padding:0;font-size:12px;font-family:Dotum,Arial;color:#666;}
	h1,h2,h3,h4,h5,h6	{font-size:12px;}
	img,fieldset{border:0px;}
	ul,li,ol{list-style:none;}
	a{text-decoration:none;} a:link {color:#181818;} a:hover {text-decoration:underline;color:#585858;} a:visited {color:#181818;}
	em,address{font-style:normal}
	.nobr{text-overflow:ellipsis; overflow:hidden;white-space:nowrap;}
	table	{ border-collapse:collapse;table-layout:fixed;}
	td,th	{padding:0;margin:0;}
	input,label	{vertical-align:middle;border:0;}
	label {cursor:pointer;}
	
	.cti_layout_wrap {width:900px; min-height:300px; padding:10px 30px 20px;  background:#fff; position:relative;}
	
	#facebox .close img {opacity:1;}
	#facebox .close {top: 25px;right: 32px;}
	.cti_layout_wrap:after{content:""; display:block; clear:both;}
	.cti_layout_wrap h3 {margin:15px 0 16px; background:url('../images/cti_poptitle_background.png') 0 bottom repeat-x;}
	
	.member_status{padding:14px 0;}
	.member_status span{font-weight:normal;}
	
	.cti_pop_table_li1 {width:138px; height:26px; border:1px solid #cccccc; background:#fff; margin-right:5px;}
	.cti_pop_table_li2 {width:233px; height:26px; border:1px solid #ccc; background:#fff; margin-right:10px; position:relative;}
	.cti_pop_table_li2 img {position:relative; cursor:pointer; top:3px;}
	.cti_pop_table_li3 {cursor:pointer;}
	
	.cti_table_list{}
	.cti_table_list table {border:1px solid #cccccc;}
	.cti_table_list table tr th {border-bottom:1px solid #e5e5e5; background:#f0f0f0; text-align:center; height:32px; font-weight:normal; color:#363636; font-weight:bold;}
	.cti_table_list table tr td {border-bottom:1px solid #e5e5e5; text-align:left; height:32px; color:#363636; padding-left:15px;}
	.cti_table_list table tr td img {cursor:pointer;}
	.cti_table_list table tr td div.select_warp{ border:1px solid #ccc; width:248px;  float:left;}
	.cti_table_list table tr td div.select_warp select{border:none; width:100%; padding:3px 5px; margin:0;}
	.cti_table_list table tr td input.addr{border:1px solid #ccc; padding:5px 5px; height:25px; line-height:140%; width:590px;}
		
	.border_div{border:1px solid #ddd; margin:12px 10px;}
	.border_div select{border:none; width:100%; height:26px;}
	.border_div input{height:26px;}
	.crm_btn{width:;}
	.btn_area {text-align:center; padding:20px 0;}
	
	
	.mail_select{}
	.mail_select:after{content:""; display:block; clear:both;}
	.mail_select li{float:left;}
	.mail_select li input.mail_select_btn{}
	.mail_select li input.calendar{width:80px; height:25px; line-height:140%; border:1px solid #ccc; background:url('../images/gradation.gif') repeat-x; float:left;}
	.mail_select li div{display:inline-block; }

	.cti_table_list table tr td input.title{border:1px solid #ccc; padding:5px 5px; height:25px; line-height:140%; width:590px;}
	
	.mail_content_text{width:100%; height:100%; border:none; padding:10px; }
	.mail_select_radio{display:inline-block; line-height:140%; font-weight:bold; vertical-align:middle; margin:5px;}
	.time_warp:after{clear:both; display:block; content:"";}
	
</style>
<div class='cti_layout_wrap'>
	<h3>
		<img src="../images/crm_pop2.gif" alt="전화" />
	</h3>
	<h4>
		<div class="member_status">
			<img src="../images/member_icon.gif" alt="회원" style="vertical-align:middle;" />
			<span><?=$result['name']?>(<?=$result['id']?>)회원 </span>
		</div>
	</h4>
	<div class='cti_table_list'>
	<form action="member_crm_act.php" id="mail_form">
	<input type="hidden" name="mode" value="email" />
	<input type="hidden" name="code" value="<?=$code?>" />
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="main_table">
			<col width="213">
			<col width="*">
			<!--<tr height="43">
				<th>
					<strong>답변 템플릿</strong>
				</th>
				<td>
					<div class="select_warp">
						<select id="" style="padding:3px 7px;">
							<option value="" selected="selected">업무전달</option>
						</select>
					</div>
				</td>
			</tr>-->
			<tr height="43">
				<th>
					<strong>수신주소</strong>
				</th>
				<td>
					<input type="text" name="mail" class="addr" value="<?=$result['mail']?>"  style="height:25px; padding:5px;"/>
				</td>
			</tr>
			<!--<tr height="43">
				<th>
					<strong>예약구분</strong>
				</th>
				<td>
					<ul class="mail_select">
						<li>
							<span class="mail_select_radio">
								<input type="radio" value="" name="btn_radio" id="btn_now" class="mail_select_btn" checked style="border:none;"/>
								<label for="btn_now" style="padding-left:5px;">즉시발송</label>
							</span>
						</li>
						<li style="padding-left:10px;">
							<span class="mail_select_radio">
								<input type="radio" value="" name="btn_radio" id="btn_reservation" class="mail_select_btn" style="border:none;"/>
								<label for="btn_reservation" style="padding-left:5px;">예약발송</label>
							</span>
						</li>
						<li class="time_warp">
							<input type="text"value="" name="send_time_mail" id="send_mail_date" class="calendar" style="width:88px; height:27px; height:25px\9; " />
							<div class="select_warp" style="width:63px;height:25px;margin-left:5px;">
								<select name='send_time_hour'>
									<?
										for($i=0;$i < 24;$i++){
									?>
										<option value=<?=sprintf("%02d", $i)?> <?=($sTime == sprintf("%02d", $i) ? "selected":"")?> > <?=sprintf("%02d", $i)."시"?></option>
									<?
									  }
									?>
								</select>
							</div>
							<div class="select_warp" style="width:63px;height:25px; margin-left:5px;">
								<select name='send_time_minite'>
									<?
										for($i=0;$i < 60;$i++){
									?>
										<option value=<?=sprintf("%02d", $i)?> <?=($sMinute == sprintf("%02d", $i) ? "selected":"")?> > <?=sprintf("%02d", $i)."분"?></option>
									<?
									  }
									?>
								</select>
							</div>

						</li>
					</ul>
				</td>
			</tr>-->
			<tr height="43">
				<th>
					<strong>메일발송 제목</strong>
				</th>
				<td>
					<input type="text" name="email_subject" class="title" />
				</td>
			</tr>
			<tr height="185">
				<td colspan="2" style="padding:0;">
					<textarea  name="mail_content" id="mail_content" class="mail_content_text"></textarea>
				</td>
			</tr>
			
		</table>
		<div class="btn_area">
			<input type="image" src="../images/btn/mail_confirm.gif" alt="지급"  style="padding-right:10px; vertical-align:top; cursor:pointer;"/>
			<img src="../images/btn/btn_cancel.gif" alt="취소"onclick="$('.close_image').trigger('click');" style="cursor:pointer;"  />
		</div>
	</div>
	</form>
</div>
<!--<table border="0" cellpadding="0" cellspacing="0" style="margin:70px auto;font-size:12px;font-family:dotum;color:636363;table-layout:fixed;" width="620">
		<colgroup>
			<col width="150" />
			<col width="*" />
		</colgroup>
		<tbody>
			<tr>
				<th colspan="2" style="position:relative;"><img src="http://daiso.forbiz.co.kr/data/daiso_data/templet/daiso/images/korea/common/mailing_header.gif" /> <span style="position:absolute;top:50px;right:9px;font-size:18px;">{=date(&#39;Y.m.d&#39;)}</span></th>
			</tr>
			<tr>
				<td colspan="2"><img src="http://daiso.forbiz.co.kr/data/daiso_data/templet/daiso/images/korea/common/mailing_qna.gif" /></td>
			</tr>
			<tr>
				<td align="center" colspan="2" style="padding:60px 30px 90px 30px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody>
						<tr>
							<td style="padding:30px 0 24px 0;"><strong style="font-size:14px;color:#2a2a2a;">{mem_name}</strong>님께서 질문해주신 내용입니다.</td>
						</tr>
					</tbody>
				</table>

				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<colgroup>
						<col width="118" />
						<col width="*" />
					</colgroup>
					<tbody>
						<tr>
							<th style="padding-left:15px;height:31px;text-align:left;background:#f7f7f7;border-bottom:1px solid #e2e2e2;border-top:2px solid #828282;">문의유형</th>
							<td style="padding-left:12px;border-left:1px solid #e2e2e2;border-bottom:1px solid #e2e2e2;border-top:2px solid #828282;">{rname}</td>
						</tr>
						<tr>
							<th style="padding-left:15px;height:31px;text-align:left;background:#f7f7f7;border-bottom:1px solid #e2e2e2;">제목</th>
							<td style="padding-left:12px;border-left:1px solid #e2e2e2;border-bottom:1px solid #e2e2e2;">{rmobile}</td>
						</tr>
						<tr>
							<th style="padding-left:15px;height:31px;text-align:left;background:#f7f7f7;border-bottom:1px solid #e2e2e2;">내용</th>
							<td style="padding-left:12px;border-left:1px solid #e2e2e2;border-bottom:1px solid #e2e2e2;">{addr}</td>
						</tr>
					</tbody>
				</table>

				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tbody>
						<tr>
							<td style="padding-top:30px;padding-bottom:10px;"><img align="absmiddle" alt="" src="http://daisomall.mallstory.com/data/daiso_data/templet/daiso/images/common/mypage_title_bg.png" title="" /> <strong style="vertical-align:middle;">답변</strong></td>
						</tr>
						<tr>
							<td style="height:50px;padding:16px 0 0 16px;background:#f6f6f6;border:1px solid #eaeaea;" valign="top">답변나오는 부분입니다.</td>
						</tr>
					</tbody>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:20px 0 17px 20px;background:#e6e6e6;">* 본 메일은 발신전용이므로 회신되지 않습니다.<br />
				* 문의사항은 [<a href="#" style="text-decoration:underline;color:#d00000;">{_shopcfg[&quot;shop_name&quot;]}</a>]또는 다이소몰 고객센터(<strong style="text-decoration:underline;">{_shopcfg[&quot;phone&quot;]}</strong>)를 이용해 주시기 바랍니다.</td>
			</tr>
			<tr>
				<th style="padding:23px 0 37px 0;background:#f7f7f7;"><img src="http://daiso.forbiz.co.kr/data/daiso_data/templet/daiso/images/korea/common/mailing_logo.gif" /></th>
				<td style="padding:23px 0 37px 0;background:#f7f7f7;">{_shopcfg[&quot;company_address&quot;]} l 개인정보보호 관리책임자 {_shopcfg[&quot;officer_name&quot;]}<br />
				대표이사 {_shopcfg[&quot;ceo&quot;]} | 사업자등록번호 {_shopcfg[&quot;biz_no&quot;]} l 통신판매업신고번호 : {_shopcfg[&quot;online_biz_no&quot;]}<br />
				주소 {_shopcfg[&quot;company_address&quot;]}<br />
				COPYRIGHT ⓒ 2013 {_shopcfg[&quot;com_name&quot;]}. All rights reserved. {_SERVER[&quot;HTTP_HOST&quot;]}</td>
			</tr>
		</tbody>
	</table>-->
<script type="text/javascript">

$(document).ready(function(){

	//CKEDITOR.replace('mail_content').config.height = '400px';
	
	$('#mail_form').submit(function(){
		
		if($('input[name="mail"]').val() == ''){
			alert('수신주소를 입력해주세요');
			$(this).focus();
			return false;
		}else if ($('input[name="email_subject"]').val() == ''){
			alert('이메일 제목을 입력해주세요');
			$(this).focus();
			return false;
		}else if($('#mail_content').val()==''){
			alert('이메일 내용을 입력해주세요');
			$(this).focus();
			return false;
		}
		
		var valuesToSubmit = $(this).serialize();
		//var contents = CKEDITOR.instances['mail_content'].getData();
		//	valuesToSubmit.append('mail_content', contents);

		$.ajax({
			url: $(this).attr('action'), 
			data: valuesToSubmit,
			dataType: "html" 
		}).success(function(data){
			alert(data);
			$('.close_image').trigger('click');
		});

		return false;
			
	})
	
	
	 $('input[name=send_time_mail]').attr('disabled',true).css('background-color','#fff');
	 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
	 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');

	 $('#btn_now').click(function(){
        if(this.checked){
             $('input[name=send_time_mail]').attr('disabled',true).css('background-color','#fff');
			 $('select[name=send_time_hour]').attr('disabled',true).css('color','#ccc');
			 $('select[name=send_time_minite]').attr('disabled',true).css('color','#ccc');
        }
    });
	
	$('#btn_reservation').click(function(){
        if(this.checked){
            $('input[name=send_time_mail]').attr('disabled',false).css('background-color','#fff7da');
            $('select[name=send_time_hour]').attr('disabled',false).css('color','#666');
			$('select[name=send_time_minite]').attr('disabled',false).css('color','#666');
        }
    });
	
	/*
	$( "#send_mail_date" ).datepicker({
		monthNames: ['1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd'
	});
	*/
});
</script>