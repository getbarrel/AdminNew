<?
include("../webedit/webedit.lib.php");
include("../../class/database.class");

$db = new Database;

$db->query("select * from ".TBL_SHOP_HTML_LIBRARY." where hl_ix = '$hl_ix' ");
if($db->total){
	$db->fetch();
	$act = "update";
	
	$hl_ix = $db->dt[hl_ix];
	$hl_name = $db->dt[hl_name];
	$hl_desc = $db->dt[hl_desc];
	$html_code = $db->dt[html_code];	
}else{
	$act = "insert";	
}
?>

<html>
<title>치환변수</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='../include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='../webedit/webedit.js'></script>	
<script language='JavaScript' src='../js/admin.js'></Script>
<Script Language='JavaScript' src='design.js'></Script>
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
		<form name="z" method="post"  action='html_library.act.php'  ><!--onSubmit="CheckSearch(this)"-->
		<input type='hidden' name='act' value='<?=$act?>'>
		<input type='hidden' name='hl_ix' value='<?=$hl_ix?>'>
		
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> HTML LIBRARY 저장하기
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
			<tr height=30><td class="p11 ls1" style='padding:0 0 0 20;' > - 입력하고자 하는 html  라이브러리 정보를 입력후 확인버튼을 눌러주세요</td></tr>	
			<tr>				
				<td align=left style='padding: 0 10 0 20'>
				
				<table cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td width=30%>	HTML LIBRARY 이름 : </td><td><input type=text class='textbox'  name='hl_name' value="<?=$hl_name?>"></td>
					</tr>
					<tr>
						<td>	HTML LIBRARY 설명 : </td><td><textarea name='hl_desc' cols=20><?=$hl_desc?></textarea></td>
					</tr>
					<tr>
						<td>	HTML LIBRARY </td><td><textarea onkeydown="textarea_useTab( this, event );" name='html_code' wrap='off'  cols=20 style='height:300px;'><?=$html_code?></textarea></td>
					</tr>
				</table>
				
					<!--table class="box_shadow" style='width:100%;' >
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
						</table-->			
			
				</td>
			</tr>
		</table>
		</td>
	</tr>	
	<tr>
		<td style='padding: 0 10 0 10' width=100% colspan=2>
	
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 200px;' colspan=2>
			<input type="image" src="../images/<?=$admininfo["language"]?>/btn_ok.gif" style="border:0px;" align=absmiddle>
			<a href='javascript:self.close();'><img src='../images/<?=$admininfo["language"]?>/btn_close.gif' border=0 align=absmiddle></a>
		</td>
	</tr></form>
</TABLE>

<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>






