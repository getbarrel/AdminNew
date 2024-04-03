<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$idx = $_REQUEST[idx];
	$r_email = explode("@",$_REQUEST[email]);
	$company = iconv("euc-kr","utf-8",$_REQUEST[company]);
	$name = iconv("euc-kr","utf-8",$_REQUEST[name]);
?>
<html>
<title>메일재전송</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
<script language='JavaScript' src='/admin/tax/tax.js'></Script>
<script>
function frm_submit()
{
	if($("#r_email1").val() == "")
	{
		alert ("이메일 아이디를 입력해주세요");
		$("#r_email1").focus();
		return;
	}
	if($("#r_email2").val() == "")
	{
		alert ("이메일 제공업체를 입력해주세요");
		$("#r_email2").focus();
		return;
	}

	$("#frm").attr("action","proc.email_send.php");
	$("#frm").attr("method","post");
	$("#frm").attr("target","PROC");
	$("#frm").submit();
}
</script>


<body style="margin:0px 0px 0px 0px" onload="resize()">

<form id="frm" name="frm">

<input type="hidden" name="company" value="<?=$company?>">
<input type="hidden" name="name" value="<?=$name?>">
<input type="hidden" name="idx" value="<?=$idx?>">

<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 메일재전송
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align="center">

			<table cellSpacing=1 cellPadding=0 width="90%" border=0 bgcolor="#CCCCCC" style="margin:10px 0 10px 0">
			  <tr>
			    <td bgcolor='#FFFFFF' height="100" align="center">
					<?=$company?> <?=$name?>님에게 이메일을 전송합니다. </br></br>
					<input type='text' name='r_email1' id='r_email1' value='<?=$r_email[0]?>' style='width:70px;'> @ <input type='text'  name='r_email2' id='r_email2' value='<?=$r_email[1]?>' style='width:70px;'>
					<select name='email_com2' id='email_com2' class='sb'>
						<option value=''>직접입력</option>
						<option value='chol.com'>chol.com</option>
						<option value='dreamwiz.com'>dreamwiz.com</option>
						<option value='empal.com'>empal.com</option>
						<option value='freechal.com'>freechal.com</option>
						<option value='gmail.com'>gmail.com</option>
						<option value='hanafos.com'>hanafos.com</option>
						<option value='hanmail.net'>hanmail.net</option>
						<option value='hanmir.com'>hanmir.com</option>
						<option value='hitel.net'>hitel.net</option>
						<option value='hotmail.com'>hotmail.com</option>
						<option value='korea.com'>korea.com</option>
						<option value='kornet.net'>kornet.net</option>
						<option value='lycos.co.kr'>lycos.co.kr</option>
						<option value='nate.com'>nate.com</option>
						<option value='naver.com'>naver.com</option>
						<option value='netian.com'>netian.com</option>
						<option value='nownuri.net'>nownuri.net</option>
						<option value='paran.com'>paran.com</option>
						<option value='unitel.co.kr'>unitel.co.kr</option>
						<option value='yahoo.com'>yahoo.com</option>
						<option value='yahoo.co.kr'>yahoo.co.kr</option>
					</select>
				</td>
			  </tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center"><img src="/admin/image/btn_email.gif" onclick="frm_submit();"> <img src="/admin/image/close.gif" onclick="window.close()"></td>
	</tr>
</table>
</form>
</body>

<iframe name="PROC" id="PROC" width="0" height="0"></iframe>