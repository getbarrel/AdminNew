<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
$db->query("select * from work_report wr, common_member_detail cmd where wl_ix ='$wl_ix' and wr_ix ='$wr_ix' and wr.charger_ix = cmd.code order by wr.regdate desc   ");		
$db->fetch();

$b_contents = $db->dt[report_desc];
?>

<html>
<title>메일보내기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='../webedit/webedit.js'></script>	
<script language='JavaScript' src='../js/admin.js'></Script>
<script language='JavaScript' >
function CheckMail(frm){
	frm.content.value = iView.document.body.innerHTML;	
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');	
		return false;
	}
}

window.onload = function(){
	//alert(1);
	Init(document.send_mail);
	iView.document.body.innerHTML = "<br><br><br><br><br>"+document.send_mail.b_contents.value;
	
}
</Script>
<body topmargin=0 leftmargin=0 >
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>
		<td align=center >
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<!--tr height=40><td bgcolor=#000000 style='padding:0 0 0 20;font-weight:bold;color:#efefef;'> ⊙ 메일보내기 </td></tr-->
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 보고서 프린트
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
		<td align=center style='padding:10px'>
<?=$b_contents?>
		</td>
	</tr>
	<!--tr>
		<td align=center style='padding:10 0 0 0'>
			<input type=image src='../image/btn_email.gif' border=0> <a href='javascript:self.close();'><img src='../image/close.gif' border=0 align=absmiddle></a>
		</td>
	</tr--></form>
</TABLE>

<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>





