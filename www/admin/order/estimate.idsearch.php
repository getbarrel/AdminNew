<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="description" content="몰스토리에 방문해 주셔서 감사합니다.">
<meta name="keywords" content="쇼핑몰 쇼핑몰 제작 임대형 쇼핑몰">
<title>아이디 검색</title>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language="javascript" src="./estimate.idsearch.js"></script>
<script language="javascript" src="/admin/js/auto.validation.js"></script>
<body topmargin=0 leftmargin=0 >
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<TD align=center >
			<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
				<tr><td  align=left class='top_orange'  ></td></tr>
				<tr height=35 bgcolor=#efefef>
					<td  style='padding:0 0 0 0;'> 
						<table width='100%' border='0' cellspacing='0' cellpadding='0' >
							<tr> 
								<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
									<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 아이디 검색
								</td>
								<td width='90%' align='right' valign='top' >
									&nbsp;
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height=20><td class="p11 ls1"></td></tr>	
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
											<td align="center" width="80"><b>아이디</b></td>
											<td align="center" >
												<input type="text" class="input" name="qstr" size="30" value='' validation=true title="아이디">
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
			<table border="0" width="600" cellpadding="0" cellspacing="1" align="center">
				<tr>
					<td >
					<div style='overflow:auto;width:100%;height:155px;'>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<col width='50'>
							<col width='70'>
							<col width='70'>
							<col width='70'>
							<col width='70'>
							<col width='80'>
							<col width='*'>
							<tr  height=2><td align=center bgcolor=gray colspan=7></td></tr>
							<tr bgcolor="#efefef" height=25>
								<th align="center">그룹</th>
								<th>학교명</th>
								<th>이름</th>
								<th>아이디</th>
								<th>전화번호</th>
								<th>휴대폰</th>
								<th>주소</th>
							</tr>
							<tr  height=1><td align=center bgcolor=silver colspan=7></td></tr>
<?
$db = new Database;
$qstr = trim($qstr);

if($act == "search"){
	$sql = "SELECT mg.gp_name, cs.sc_nm, cs.com_number, cs.zip_code, cs.addr as cs_addr1, cs.addr2 as cs_addr2, cs.com_ceo, mm.code, mm.name, mm.id, mm.tel, mm.pcs, mm.zip, mm.addr1, mm.addr2, mm.mail  
	FROM ".TBL_MALLSTORY_MEMBER." mm, ".TBL_MALLSTORY_GROUPINFO." mg, mallstory_comm_sc cs WHERE mm.gp_ix = mg.gp_ix and mm.sc_code = cs.sc_code and id LIKE '%$qstr%'  ORDER BY name";
	$db->query($sql);
	
	if($db->total){
		for($i=0;$i < $db->total ; $i++){
			$db->fetch($i);
			$code = $db->dt[code];
			$gp_name = $db->dt[gp_name];
			$sc_nm = $db->dt[sc_nm];
			$name = $db->dt[name];
			$id = $db->dt[id];
			$tel = $db->dt[tel];
			$pcs = $db->dt[pcs];
			$zip = $db->dt[zip];
			$addr1 = $db->dt[addr1];
			$addr2 = $db->dt[addr2];
			$mail = $db->dt[mail];
			$com_number = $db->dt[com_number];
			$zip_code = $db->dt[zip_code];
			$cs_addr1 = $db->dt[cs_addr1];
			$cs_addr2 = $db->dt[cs_addr2];
			$com_ceo = $db->dt[com_ceo];
			
			$list = "
					<tr height=25 bgcolor='#F8F9FA' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='hand';\" onMouseOut=\"this.style.backgroundColor='';\" onClick=\"javscript:idsearch_in(opener.document.form, '$code', '$name','$zip','$addr1','$addr2','$tel','$pcs','$mail','$sc_nm','$com_number','$zip_code','$cs_addr1','$cs_addr2','$com_ceo');\">
								<td align='center'>$gp_name</td>
								<td>$sc_nm</td>
								<td align='center'>$name</td>
								<td align='center'>$id</td>
								<td align='center'>$tel</td>
								<td align='center'>$pcs</td>
								<td align='center'>$addr1 $addr2</td>
							</tr>
							<tr  height=1><td align=center background='/admin/image/dot.gif' colspan=7></td></tr>";
			echo $list;
		}
	}else{
		$list = "
			<tr bgcolor='#F8F9FA' height=70>
				<td align='center' colspan=7>							
					아이디 입력해주시기 바랍니다.							
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