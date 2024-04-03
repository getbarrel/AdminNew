<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

	$mobile = $_REQUEST[mobile];
	$company = iconv("euc-kr","utf-8",$_REQUEST[company]);
	$name = iconv("euc-kr","utf-8",$_REQUEST[name]);
?>
<html>
<title>SMS 보내기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='/admin/tax/tax.js'></Script>
<script language='JavaScript' >

function CheckLen()
{
	var frm = document.sms_form;
	var t = "";
	var msglen = 0;
	for(k=0;k<frm.to_message.value.length;k++){
		t = frm.to_message.value.charAt(k);
		if (escape(t).length > 4)
			msglen += 2;
		else
			msglen++;
	}
	//frm.smsByte.value = msglen;
	document.getElementById("smsByte").innerHTML = msglen;

	if(msglen > 79){
	  reserve = msglen-80;
	  alert("메시지 내용은 80바이트 이상은 전송하실수 없습니다.\r\n 쓰신 메세지는 "+reserve+"바이트가 초과되었습니다.\r\n ");
	  return;
	}
}

function sms_send()
{
	if($("#to_message").val() == "")
	{
		alert ("메세지를 입력해주세요.");
		$("#to_message").focus()
		return;
	}
	if($("#snd_no").val() == "")
	{
		alert ("발신자번호를 입력해주세요.");
		$("#snd_no").focus()
		return;
	}
	if($("#rcv_no").val() == "")
	{
		alert ("수신자번호를 입력해주세요.");
		$("#rcv_no").focus()
		return;
	}

	$("#sms_form").attr("action","./proc.sms.php");
	$("#sms_form").attr("method","post");
	$("#sms_form").attr("target","PROC");
	$("#sms_form").submit();
}
</Script>
<style>
.emoticon_bg {background:url(./img/emoticon_bg.gif) no-repeat center top;height:128px;text-align:center;}
.emoticon_area {width:98px;height:90px;background:transparent;border-style:none;padding:0;font-family:"돋움", Dotum, verdana;font-size:12px;color:#000000;line-height:14px;margin-top:18px;overflow:hidden;}
</style>

<body topmargin=0 leftmargin=0 onload="resize()"><!--onload="Init(document.send_mail);"-->
<form name="sms_form" id="sms_form">
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> SMS 보내기
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
		<td height="50" align="center">
		<?=$company?> <?=$name?>님에게 SMS를 전송합니다. </br></br>
		</td>
	</tr>
	<tr>
		<td align="center">
			
			<table cellspacing='1' width="90%" cellpadding="0" border="0" bgcolor="#CCCCCC" style="padding:10px 10px 10px 10px">
				<tr bgcolor="#FFFFFF">
					<td align="center">
						
						<table width="90%">
							<tr>
								<td>
									<table width="144" border="0" cellpadding="0" cellspacing="0" class="sms_mb10">
										<tr>
											<td class="emoticon_bg">
												<textarea name="to_message" id="to_message" class="emoticon_area" onKeyUp="javascript:CheckLen()" onKeyDown="javascript:CheckLen()" onChange="javascript:CheckLen()"></textarea>
											</td>
										</tr>
										<tr>
											<td align="center" class="dotum11_0"><span id="smsByte">0</span> / 80 Byte</td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td>발신자 : </td>
										</tr>
										<tr>
											<td><input type="text" name="snd_no" id="snd_no" size="20" value='<?=$mobile?>'></td>
										</tr>
										<tr>
											<td>수신자 : </td>
										</tr>
										<tr>
											<td><input type="text" name="rcv_no" id="rcv_no" size="20"></td>
										</tr>
										<tr>
											<td style='padding:10px 0 0 0'>번호에 "-" 기호는 포함 <br>가능합니다.</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>

					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 0' colspan=2>
			<img src='/admin/image/btn_sms.gif' border=0 align='absbottom' onclick="sms_send()" style="cursor:hand"> <a href='javascript:self.close();'><img src='/admin/image/close.gif' border=0 align='absbottom'></a>
		</td>
	</tr></form>
</TABLE>
</form>

<IFRAME id="PROC" name="PROC" src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>





