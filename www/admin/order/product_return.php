<?
include("../class/layout.class");

if($act == "return"){
	$db = new Database;
	
	$db->query("insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, status, status_message, regdate ) values ('','".$oid."','".ORDER_STATUS_RETURN_COMPLETE."','$return_message',NOW())");
	$db->query("SELECT os_ix FROM ".TBL_SHOP_ORDER_STATUS." WHERE os_ix=LAST_INSERT_ID()");
	$db->fetch();
	$os_ix = $db->dt[os_ix];
	
	
	$db->query("update ".TBL_SHOP_ORDER." set os_ix='".$os_ix."', status = '".ORDER_STATUS_RETURN_COMPLETE."', return_message='$return_message', return_date = NOW() where oid ='".$oid."'");
		
	
	echo "<script>alert(language_data['product_return.php']['A'][language]);opener.document.location.reload();self.close();</script>";	//반품 요청이 정상적으로 처리 되었습니다.
	exit;
}

?>
<html>
<title>반품하기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<body align=center valign=middle topmargin=0 leftmargin=0 >
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 반품하기
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
			<tr height=30><td class="p11 ls1" style='padding:0 0 0 20;' > - 반품사유를  입력하신후 반품하기를 클릭해주세요.</td></tr>	
			<tr>				
				<td align=center style='padding: 0 10 0 10'>
					<table class="box_shadow" style='width:100%;' >
						<tr>
							<th class="box_01"></th>
							<td class="box_02"></td>
							<th class="box_03"></th>
						</tr>
						<tr><form name='retrun' >
							<th class="box_04"></th>
							<td class="box_05" align=right style='padding: 10 20 10 20'>	
								
									<table cellpadding=3 cellspacing=0 align=center valign=middle><input type=hidden name='act' value='return'><input type=hidden name='oid' value='<?=$oid?>'>
									<tr bgcolor=#ffffff>
										<td >반품사유</td>
										<td><input name='return_message' type='text' style='border:1px gray solid;width:170px'></td>
										<td><input type=image src='../image/btn_do_return.gif' ></td>
									</tr>
									</table>								
							</td>
							<th class="box_06"></th>
						</tr></form>
						<tr>
							<th class="box_07"></th>
							<td class="box_08"></td>
							<th class="box_09"></th>
						</tr>
				
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 0' colspan=2>
			<a href='javascript:self.close();'><img src='../image/close.gif' border=0></a>
		</td>
	</tr>
</table>
</body>
</html>