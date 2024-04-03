<html>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title></title>
<style>
TD{font-size:12px;font-family:돋움}
.bg_line {background: url(./image/bg_line.gif) no-repeat left top; }
.bg_color2 {background: url(./image/bg_color.gif) repeat-x left top; }
.bg_main {background: url(./image/bg.gif) no-repeat left top; }
.bg_login {background:url(./image/bg_login.gif) no-repeat center top; }
</style>
<Script Language='JavaScript'>
var language = "<?=$admininfo[language]?>";
function focusIn()
{
    document.login_frm.id.focus();
}
window.onload=focusIn;
</Script>
<script language='JavaScript' src='./js/jquery-1.4.js'></Script>
<script language='javascript' src='./js/jquery.blockUI.js'></script>
<script language='JavaScript' src='./js/auto.validation.js'></Script>
<body topmargin=0 leftmargin=0 class="bg_color2">
<table cellpadding=0 border=0 cellspacing=0 width="100%" height="100%" class="bg_login">
	<tr height=290><td></td></tr>
	<tr>
		<td valign=top align=center style="padding-left:150px;">
			<div style="position:relative;width:300px;hegiht:200px;"  align=center >
			<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> <input type=hidden name="act" value="verify">
			<table cellpadding=0 border=0 cellspacing=0 width="280" height="50" >
				<tr height=24>
					<td width=60>아이디 </td>
					<td width=100><input type=text name="id" value="<?=$_COOKIE['ck_adminSaveID']?>" style='width:120px;border:1px solid silver;ime-mode:disabled ;' onfocus="this.style.border='2px solid orange'" onfocusout="this.style.border='1px solid silver'" validation='true' title='아이디' tabindex=1> </td>
					<td width=78 rowspan=2><input type=image src="./image/btn_login.gif" size=14 tabindex=3> </td>
				</tr>
				<tr height=24>
					<td>비밀번호 </td>
					<td><input type=password name="pw" style='width:120px;border:1px solid silver' onfocus="this.style.border='2px solid orange'" onfocusout="this.style.border='1px solid silver'"  validation='true' title='비밀번호' tabindex=2> </td>
				</tr>
				<tr height="24">
					<td colspan="2"><input type="checkbox" id="chk_saveID" name="chk_saveID" value="Y"<?=($_COOKIE['ck_adminSaveID'])	?	' checked':'';?> /> <label for="chk_saveID">아이디 저장</label></td>
				</tr>
			</table>
			</form>
			</div>
		</td>
	</tr>
	<tr height=200><td></td></tr>
</table>
<div id='loading'></div>
</body>
</html>