<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

	$from = $_GET[from];
?>
<html>
<title>신규거래처등록</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
<script language='JavaScript' src='/admin/tax/tax.js'></Script>

<script language='JavaScript' >
$(document).ready(function(){
	$("#frm").submit(function(){
		var s_val = $(':input:radio:[id=s_type]:checked').val();
		var no1 = $("#no1").val();
		var no2 = $("#no2").val();
		var no3 = $("#no3").val();
		
		if(s_val == 1)
		{
			if(no1 == "")
			{
				alert ("사업자번호를 입력해주세요."); 
				$("#no1").focus(); 
				return false;
			}
			if(no2 == "") 
			{
				alert ("사업자번호를 입력해주세요."); 
				$("#no2").focus(); 
				return false;
			}
			if(no3 == "") 
			{
				alert ("사업자번호를 입력해주세요."); 
				$("#no3").focus(); 
				return false;
			}
		}
		else
		{

		}
	});

});

function show_view()
{
	var s_val = $(':input:radio:[id=s_type]:checked').val();
	if(s_val == "1")
	{
		$('#view1').attr("style","display:");
		$('#view2').attr("style","display:none");
	}
	else
	{
		$('#view1').attr("style","display:none");
		$('#view2').attr("style","display");
	}
}

window.resizeTo("550","330");
</Script>

<body style="margin:0px 0px 0px 0px">
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 신규 거래처 등록
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
		<td>
		
			<table width="100%">
				<tr>
					<td align='center' height="50">신규 등록할 거래처의 사업자번호(주민번호)를 입력하세요</td>
				</tr>
				<tr>
					<td>
						<form name='frm' id='frm' action='./company_write_step1_act.php' method='post' target="PROC">
						<input type="hidden" name="from" value="<?=$from?>">
						<table width='100%' cellSpacing=1 cellPadding=0 border='0' bgcolor='#CCCCCC'>
							<tr height='70' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='150'>등록하기</td>
								<td align='center'>
									<table width="90%">
										<tr>
											<td width='250'><input type='radio' name='s_type' id='s_type' value='1' checked onclick='show_view()'>사업자번호 <input type='radio' name='s_type' id='s_type' value='2' onclick='show_view()'>주민번호</td>
											<td rowspan='2'><input type='image' src='/admin/image/search01.gif'></td>
										</tr>
										<tr>
											<td>
												<span id='view1'><input type='text' name='no1' id='no1' size='10'> - <input type='text' name='no2' id='no2' size='7'> - <input type='text' name='no3' id='no3' size='10'></span>
												<span id='view2' style='display:none'><input type='text' name='jumin1' id='jumin1' size='10'> - <input type='text' name='jumin2' id='jumin2' size='10'></span>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
				<tr>
					<td height="20" align="center"><img src='/admin/image/close.gif' onclick='window.close();' style='cursor:hand'></td>
				</tr>
			</table>

		</td>
	</tR>
</table>

<iframe name="PROC" width="0"></iframe>
</body>