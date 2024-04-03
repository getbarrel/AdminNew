<link rel='stylesheet' type='text/css' href='./css/mobile.css' />
<link rel='stylesheet' type='text/css' href='../v3/css/common.css' />
<script language='JavaScript' src='../js/jquery-1.7.1.min.js'></Script>
<script language='JavaScript' src='./js/jquery.imagetick.min.js'></Script>
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width">
<style type="text/css">
	.checkbox_image_tick03{width:26px;}
</style>

<script type="text/javascript">
<!--
$(document).ready(function(){

	$("#log_url").focus();
	$(".log_in_content input").each(function(){
		if ($(this).val() != "")
		{
			$(this).addClass("on_focus");
		}
	});
	
	$(".log_in_content input").focus(function(){
		$(this).addClass("on_focus");
	});
	$('.log_in_content input').blur(function(){
		if ($(this).val()=="")
		{
			$(this).removeClass("on_focus");
		}
	});

	$("input.check_img").imageTick({
		tick_image_path: "./images/checkbox_on.png",
		no_tick_image_path: "./images/checkbox.png",
		image_tick_class: "checkbox_image_tick03",
	});

});
	
//-->
</script>

<div class="log_in_mobile">
	<div class="log_in_title">
		<img src="./images/log_in_mobile_title.png" width="200" alt="몰스토리 관리자 로그인" />
	</div>
	<div class="log_in_content">
		<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> 
			<input type=hidden name="act" value="verify">
			<table cellspacing="" cellpadding="0" border="0" width="90%">
			<col width="90%" />
			<col width="10%" />
				<tr>
					<th><input type="text" id="log_id" name="id" value="<?=$_COOKIE['ck_adminSaveID']?>"></th>
					<td><input type="checkbox" name="chk_saveID" value="Y" id="chk_saveID" class="check_img" <?=($_COOKIE['ck_adminSaveID'])	?	' checked':'';?> /></td>
				</tr>
				<tr>
					<th><input type="password" id="log_pass" name="pw" value="<?=$_COOKIE['ck_adminSavePW']?>"></th>
					<td><input type="checkbox" name="chk_savePW" value="Y" id="chk_savePW" class="check_img" <?=($_COOKIE['ck_adminSavePW'])	?	' checked':'';?> /></td>
				</tr>
			</table>
			<table cellspacing="" cellpadding="0" border="0" width="94%" class="btn_login">
				<tr>
					<td style="padding-left:5px;"><input type="image" src="./images/btn_login.png" width="88" /></td>
					<!--td><input type="checkbox" name="chk_saveAUTO"  value="Y" id="auto_log" class="check_img" <?=($_COOKIE['ck_adminSaveAUTO'])	?	' checked':'';?> /></td>
					<td><label for="auto_log">자동로그인 사용</label></td-->
				</tr>
			</table> 
		</form>
	</div>
	<div class="call_center">
		<ul>
			<li><img src="./images/call_center.png" alt="고객센터안내" width="100" /></li>
			<li class="call_number"><b>1600-2058</b> (<span>Fax.</span>02-2058-2215)</li>
			<li class="time_shop">운영시간 10:00~19:00 / 점심시간(12:30~13:30)<br />토,일,공휴일은 쉽니다.</li>
		</ul>
		<p><img src="./images/btn_online_qna.png" width="47%" />&nbsp;<img src="./images/btn_faq.png" width="47%" /></p>
	</div>
</div>
<div style="text-align:center;padding:15px 0;border-top:1px solid #d3d3d3;color:#818181;">Copyright ⓒ Mallstory. All Rights Reserved.</div>
