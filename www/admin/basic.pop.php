<?
include($_SERVER["DOCUMENT_ROOT"]."/manage/webedit/webedit.lib.php");
?>

<html>
<title>메일보내기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/manage/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>

<body topmargin=0 leftmargin=0 >
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<TD align=center ><script language="javascript" src="/data/sample/templet/basic//js/ms_order_zipcode.js"></script>
<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
	<tr height=40><td bgcolor=#000000 style='padding:0 0 0 20;font-weight:bold;color:#efefef;'> ⊙ 메일보내기 </td></tr>
	<tr height=30><td class="p11 ls1" style='padding:0 0 0 20;'> - 찾으실 아이디 또는 이름을 입력하세요.</td></tr>	
	<tr>
		<td align=center>
		<form name="z" method="post"  onSubmit="check()">
		<input type='hidden' name='act' value='search'>
		<input type='hidden' name='zip_type' value='1'>
			<table class="box_shadow" style='width:90%;' >
				<tr>
					<th class="box_01"></th>
					<td class="box_02"></td>
					<th class="box_03"></th>
				</tr>
				<tr>
					<th class="box_04"></th>
					<td class="box_05" align=center>	
						<table border="0" width="100%" cellspacing="1" cellpadding="0">
							<tr height="30" valign="middle">
								<td align="center" width="70"><b>회원검색</b></td>
								<td align="center" >
									<input type="radio"  name="search_type" value='id'> 아이디
									<input type="radio"  name="search_type" value='name' checked> 이름
								</td>
								<td>
									<input type="text" class="input" name="qstr" size="20" value=''>
								</td>
								<td>	<input type="image" src='/data/sample/templet/basic//images/bt_search.gif' ></td>
							</tr>
						</table>
					</td>
					<th class="box_06"></th>
				</tr>
				<tr>
					<th class="box_07"></th>
					<td class="box_08"></td>
					<th class="box_09"></th>
				</tr>
				</table>			
		</form>
		</td>
	</tr>
</table>
<div id="comment" align="center"></div>
<table border="0" width="400" cellpadding="0" cellspacing="1" align="center">
	<tr>
		<td >
		<div style='overflow:auto;width:100%;height:155px;'>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr  height=2><td align=center bgcolor=gray colspan=6></td></tr>
				<tr bgcolor="#efefef" height=25>
					<td width="80" align="center"><b>회원아이디</b></td>
					<td>&nbsp;<b>회원이름</b></td>
				</tr>
				<tr  height=1><td align=center bgcolor=silver colspan=6></td></tr>
				<tr bgcolor="#F8F9FA" height=70>
					<td align="center" colspan=2>							
						검색어를 입력해주시기 바랍니다.							
					</td>
				</tr>
				</table>
		</div>
		</td>
	</tr>
</table>
</TD>
</TR>
</TABLE>
<?=miniWebEdit("/manage")?>
<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>





