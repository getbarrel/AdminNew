<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="description" content="몰스토리에 방문해 주셔서 감사합니다.">
<meta name="keywords" content="쇼핑몰 쇼핑몰 제작 임대형 쇼핑몰">
<title>몰스토리에 방문해 주셔서 감사합니다.</title>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language="javascript" src="/admin/js/auto.validation.js"></script>
<body topmargin=0 leftmargin=0 >
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<TD align=center >
		<script language="javascript" src="/admin/js/zipcode.js"></script>
			<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
				<tr><td  align=left class='top_orange'  ></td></tr>
				<tr height=35 bgcolor=#efefef>
					<td  style='padding:0 0 0 0;'> 
						<table width='100%' border='0' cellspacing='0' cellpadding='0' >
							<tr> 
								<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
									<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 우편번호 찾기
								</td>
								<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
									&nbsp;
								</td>
							</tr>
							<!--tr height=10><td colspan=2></td></tr-->
						</table>
					</td>
				</tr>
				<tr height=30><td class="p11 ls1" style='padding:0 0 0 20;'> - 찾으실 주소지(동,읍/면단위)를 입력하세요.</td></tr>	
				<tr>
					<td align=center>
					<form name="z" method="post"  onSubmit="return CheckFormValue(z);">
					<input type='hidden' name='act' value='search'>
					
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
											<td align="center" width="80"><b>검색주소</b></td>
											<td align="center" >
												<input type="text" class="input" name="qstr" size="30" value='' validation=true title="검색주소">
											</td>
											<td>
												<input type="image" src='/admin/image/btn_search.gif' style="border:0px;">
											</td>
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
								<td width="80" align="center"><b>우편번호</b></td>
								<td> <b>주소</b></td>
							</tr>
							<tr  height=1><td align=center bgcolor=silver colspan=6></td></tr>
<?
$db = new Database;
$qstr = trim($qstr);

if($act == "search"){
	$db->query("SELECT left(zip_code,3) as code1, right(zip_code,3) as code2, address, CONCAT(sido,' ',sigugun,' ',dong,' ', building_name) as address_input FROM mallstory_zip WHERE sido LIKE '%$qstr%' OR sigugun LIKE '%$qstr%' OR dong LIKE '%$qstr%'  OR ri LIKE '%$qstr%'  ORDER BY sido, sigugun,dong");
	
	if($db->total){
		for($i=0;$i < $db->total ; $i++){
			$db->fetch($i);
			$code1 = $db->dt[code1];
			$code2 = $db->dt[code2];
			$address_input = $db->dt[address_input];
			$tel = $db->dt[tel];
			$address = $db->dt[address];
			
			$list = "
					<tr height=25 bgcolor='#F8F9FA' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='hand';\" onMouseOut=\"this.style.backgroundColor='';\" onClick=\"zipcode('$code1','$code2','$address_input','$tel','$type');\">
								<td align='center'><b>$code1 - $code2</b></td>
								<td>$address</td>
							</tr>
							<tr  height=1><td align=center background='/admin/image/dot.gif' colspan=6></td></tr>";
			echo $list;
		}
	}else{
		$list = "
			<tr bgcolor='#F8F9FA' height=70>
				<td align='center' colspan=2>							
					검색어를 입력해주시기 바랍니다.							
				</td>
			</tr>";
		echo $list;
	}
}
?>								
							
						</table>
					</div>
					</td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>
<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>