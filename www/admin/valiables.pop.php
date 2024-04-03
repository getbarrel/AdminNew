<?
include($_SERVER["DOCUMENT_ROOT"]."/manage/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
?>

<html>
<title>치환변수</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/manage/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='/manage/webedit/webedit.js'></script>	
<script language='JavaScript' src='/manage/funny/js/admin.js'></Script>
<script language='JavaScript' >
function CheckSMS(frm){
	
	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');	
		return false;
	}
	
	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 회원이 한명이상이어야 합니다.');	
		return false;
	}
	
	return true;
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');	
		return false;
	}
}
</Script>
<body topmargin=0 leftmargin=0 ><!--onload="Init(document.send_mail);"-->
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr height=40><td bgcolor=#000000 style='padding:0 0 0 20;font-weight:bold;color:#efefef;'> ⊙ 치환변수 </td></tr>
			<tr height=30><td class="p11 ls1" style='padding:0 0 0 20;' > - 찾으실 치환변수의 변수명 또는 설명을 입력해주세요</td></tr>	
			<tr>				
				<td align=center style='padding: 0 10 0 10'>
				<form name="z" method="post"  action='sms.pop.php'  onSubmit="CheckSearch(this)">
				<input type='hidden' name='act' value='search'>
					<table class="box_shadow" style='width:100%;' >
						<tr>
							<th class="box_01"></th>
							<td class="box_02"></td>
							<th class="box_03"></th>
						</tr>
						<tr>
							<th class="box_04"></th>
							<td class="box_05" align=right>	
								<table border="0" width="100%" cellspacing="1" cellpadding="0">
									<tr height="30" valign="middle">
										<td align="center"  colspan=2><b>치환변수검색</b>
											<input type="radio"  name="search_type" value='id'> 변수
											<input type="radio"  name="search_type" value='name' checked> 내용
										</td>									
										<td>
											<input type="text" class="input" name="search_text" size="20" value=''>
										</td>
										<!--td><input type="image" src='/data/sample/templet/basic/images/bt_search.gif' ></td-->
										<td>	<input type="image" src='/manage/image/search01.gif' align=absmiddle></td>
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
		</td>
	</tr>	
	<tr>
		<td style='padding: 0 10 0 10' width=100% colspan=2>
		{templet.src} : 템플릿 경로 <br>
		{product.src} : 상품폴더 경로 <br>		
		{header.top} : 최상단 메뉴 <br>
		{header.menu} : 상단 메뉴 <br>
		{center.leftmenu} : 좌측메뉴 메뉴 <br>
		{center.contents} : 컨텐츠 <br>
		{center.history} : 오른쪽 메뉴 <br>
		{footer.menu} : 하단 메뉴 <br>
		{footer.desc} : 최하단 메뉴 <br>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 0' colspan=2>
			<a href='javascript:self.close();'><img src='/manage/image/close.gif' border=0></a>
		</td>
	</tr></form>
</TABLE>

<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>






