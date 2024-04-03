<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
?>

<html>
<title>회원검색</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>

<script language='JavaScript' src='/manage/webedit/webedit.js'></script>	
<script language='JavaScript' src='/manage/funny/js/admin.js'></Script>
<script language='JavaScript' >
/*
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
*/

function SelectMember(idstr, mem_ix){
	opener.document.form_cupon.mem_id.value = idstr;
	opener.document.form_cupon.mem_ix.value = mem_ix;
	self.close();
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
			<tr height=40><td class='top_orange'></td></tr>
			<tr height=30>
				<td class="p11 ls1" style='padding:0 0 0 20;' > - 찾으실 아이디 또는 이름을 입력하세요.</td></tr>	
			<tr>				
				<td align=center style='padding: 0 10 0 10'>
				<form name="z" method="post"  action='member.find.php'  onSubmit="return CheckSearch(this)">
				<input type='hidden' name='act' value='search'>
					<table class="box_shadow" style='width:100%;' >
						<tr>
							<th class="box_01"></th>
							<td class="box_02"></td>
							<th class="box_03"></th>
						</tr>
						<tr>
							<th class="box_04"></th>
							<td class="box_05" align=right style='padding: 0 20 0 20'>	
								<table border="0" width="100%" cellspacing="1" cellpadding="0">
									<tr height="30" valign="middle">
										<td align="center"  colspan=2><b>회원검색</b>
											<select name='search_type'>
												<option value='id'> 아이디</option>
												<option value='name' selected> 이름 </option>
												<option value='pcs'> 핸드폰 </option>
											</select>
										</td>									
										<td>
											<input type="text" class="input" name="search_text" size="20" value=''>
										</td>
										<!--td><input type="image" src='/data/sample/templet/basic/images/bt_search.gif' ></td-->
										<td>	<input type="image" src='/admin/image/btc_search.gif' align=absmiddle></td>
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
	<!--tr height=10>
		<td class="p11 ls1" style='padding:0 0 0 10;' > - 메세지 내용을 <b>80</b> 자 이내료 입력해주세요.</td>
		<td class="p11 ls1" style='padding:0 0 0 10;' > - 메세지를 보낼 <b>회원 목록</b>입니다.</td>
	</tr-->	
	<tr height='150'>
		<td colspan=2 style='padding:0 20 0 10' width=50% valign=top>
		<table width='100%'>
<?
$db = new Database;

if($search_text != ""){
	
	$db->query("select * from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code = cmd. code and $search_type LIKE '%$search_text%' ");	
}


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		
		echo "<tr><td >".$db->dt[name]."(".$db->dt[id].")	&nbsp;&nbsp;&nbsp;".$db->dt[pcs]."</td><td align=right><a href=\"javascript:SelectMember('".$db->dt[name]."(".$db->dt[id].")','".$db->dt[code]."');\">선택</a></td></tr>";
	}	
}else{
	if($search_text == ""){
		echo "<tr><td>검색할 회원을 입력해주세요</td></tr>";
	}else{
		echo "<tr><td>'$search_text' 로 검색한 결과가 존재 하지 않습니다.</td></tr>";
	}
		
}
?>			
			
		</table>
		</td>
	</tr>	
	<tr>
		<td align=center style='padding:0 10 0 10' colspan=2>
<?//=miniWebEdit("/manage")?>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 0' colspan=2>
			<a href='javascript:self.close();'><img src='/admin/image/close.gif' border=0></a>
		</td>
	</tr>
</TABLE>

<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>





