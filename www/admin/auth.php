<html>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title></title>
<style>
TD{font-size:12px;font-family:돋움}
.bg_line {background: url(./image/bg_line.gif) no-repeat left top; }
.bg_color2 {background: url(./image/bg_color.gif) repeat-x left top; }
.bg_main {background: url(./image/bg.gif) no-repeat left top; }
.bg_auth {background:url(./image/bg_auth.gif) no-repeat center top; }


</style>
<Script Language='JavaScript'>
function focusIn() 
{
    login_frm.id.focus();
}
window.onload=focusIn;
</Script>
<script language='JavaScript' src='./js/auto.validation.js'></Script>
<body topmargin=0 leftmargin=0 class="bg_color2">
<table cellpadding=0 border=0 cellspacing=0 width="100%" height="100%" class="bg_auth">
	<tr height=290><td></td></tr>
	<tr>
		<td valign=top align=center style="padding-left:150px;padding-top:10px;">
			<div style="position:relative;width:300px;hegiht:200px;"  align=center >
			<form name="login_frm"  onsubmit="return CheckFormValue(this);" action="auth.act.php"> <input type=hidden name="act" value="verify">
			<table cellpadding=0 border=0 cellspacing=0 width="280" height="50" >
				<tr>
					<td width=60>몰아이디 </td>
					<td width=100><input type=text name="mall_domain_id" size=14 validation='true' title='몰아이디' tabindex=1> </td>
					<td width=78 rowspan=2><input type=image src="./image/btn_auth.gif" size=14 tabindex=3> </td>
				</tr>
				<tr>
					<td>라이센스 </td>
					<td><input type=text name="mall_domain_key" size=14 validation='true' title='라이센스' tabindex=2> </td>
				</tr>
			</table>
			</div>
		</td>
	</tr>
	<tr height=200><td></td></tr>
</table>
</body>
</html>