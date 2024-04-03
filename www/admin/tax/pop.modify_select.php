<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	if($_GET[tax_no] != "")
	{
		# 국세청 전송 결과 테이블 연결 (연동모듈테이블 이용필요)
		/*
		$SQL = "SELECT publish_type FROM tax_result WHERE national_tax_no = '$_GET[tax_no]'";

		$db->query($SQL);
		$db->fetch();
		
		$publish_type = $db->dt[publish_type];

		if($publish_type == "")
		{
			echo "<script>alert ('해당 승인번호가 존재하지 않습니다.');window.close();</script>";
		}
		*/
	}
	else
	{
		$publish_type = $_GET[publish_type];
	}

?>
<html>
<title>세금계산서</title>
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
	var kind = $("input[name=\"kind\"]:checked").val();
	var publish_type = $("#publish_type").val();

	if($("#tax_no").val() != "")
	{
		var tax_no = "&tax_no=" + $("#tax_no").val();
	}
	
	var go_url = "/admin/tax/tax_mdfy.php?publish_typ=" + publish_type + "&mKind=" + kind;
	if(tax_no) go_url += tax_no;
	opener.location.href = go_url;
}
</script>

<body style="margin:0px 0px 0px 0px" onload="resize()">

<form id="frm" name="frm">
<input type="hidden" name="tax_no" id="tax_no" value="<?=$_GET[tax_no]?>">
<input type="hidden" name="publish_type" id="publish_type" value="<?=$publish_type?>">

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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 수정전자세금계산서 발행사유
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->

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
		<td style="padding:10px 10px 10px 10px">* 수정세금계산서 발행 사유를 선택하세요!</td>
	</tr>
	<tr>
		<td align="center">
			
			<table>
				<tr>
					<td><input type="radio" name="kind" id="kind" value="1" checked></td>
					<td>
						<table border="0" cellspacing="1" cellpadding="0" bgcolor="#CCCCCC">
							<tr>
								<td bgcolor="#F2F2F2" style="padding:10px 5px 10px 5px" width="80">기재사항<br>착오정정</td>
								<td bgcolor="#FFFFFF" style="padding:10px 5px 10px 5px" width="440"><b>기재사항 착오로 잘못 발행한 경우</b><br>당초 취소분 1장(자동발행), 수정분1장(직접입력) <b>총 2장</b> 수정세금계산서 발행</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><input type="radio" name="kind" id="kind" value="2"></td>
					<td>
						<table border="0" cellspacing="1" cellpadding="0" bgcolor="#CCCCCC">
							<tr>
								<td bgcolor="#F2F2F2" style="padding:10px 5px 10px 5px" width="80">공급가액<br>변동</td>
								<td bgcolor="#FFFFFF" style="padding:10px 5px 10px 5px" width="440"><b>당초 발행한 금액의 <font color="red">증감</font>이 발행한 경우</b><br><font color="red">증감시킬</font> 금액에 대해 정(+) 또는 부(-)의 수정세금계산서 <b>1장</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><input type="radio" name="kind" id="kind" value="3"></td>
					<td>
						<table border="0" cellspacing="1" cellpadding="0" bgcolor="#CCCCCC">
							<tr>
								<td bgcolor="#F2F2F2" style="padding:10px 5px 10px 5px" width="80">계약의<br>해제</td>
								<td bgcolor="#FFFFFF" style="padding:10px 5px 10px 5px" width="440"><b>당초 발행금액이 취소된 경우</b><br>당초 공급금액에 대한 부(-)의 수정세금계산서 <b>1장</b> 발행</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><input type="radio" name="kind" id="kind" value="4"></td>
					<td>
						<table border="0" cellspacing="1" cellpadding="0" bgcolor="#CCCCCC">
							<tr>
								<td bgcolor="#F2F2F2" style="padding:10px 5px 10px 5px" width="80">환입</td>
								<td bgcolor="#FFFFFF" style="padding:10px 5px 10px 5px" width="440"><b>반품등으로 당초 발행금액의 일부가 취소된 경우</b><br><font color="red">반품된 금액만큼만</font> 부(-)의 수정세금계산서 <b>1장</b> 발행</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><input type="radio" name="kind" id="kind" value="5"></td>
					<td>
						<table border="0" cellspacing="1" cellpadding="0" bgcolor="#CCCCCC">
							<tr>
								<td bgcolor="#F2F2F2" style="padding:10px 5px 10px 5px" width="80">내국신용장<br>등사후개설</td>
								<td bgcolor="#FFFFFF" style="padding:10px 5px 10px 5px" width="440"><b>내국신용장 등이 사후 개설된 경우</b><br>개설된 금액만큼 부(-1)로 1장, 영세율로 1장 총 <b>2장</b> 수정세금계산서 발행</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
		</td>
	</tr>
	<tr height="50">
		<td align="center"><img src="/admin/image/btn_ok.gif" onclick="frm_submit();"> <img src="/admin/image/close.gif" onclick="window.close()"></td>
	</tr>
</table>
</form>
</body>

<iframe name="PROC" id="PROC" width="0" height="0"></iframe>