<?
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
$db2 = new Database;
$db2->query("update shop_idea set confirm = 'Y' where ix = '$ix'");
$db2->fetch();
$db->query("select * from shop_idea where ix = '$ix'");



$db->fetch();

if($db->dt[idea_choice] == '1') {
	$idea_choice = '상품제안';
}else $idea_choice = '아이디어 공모';

?>

<html>
<title>버그신고 자세히보기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<SCRIPT LANGUAGE="JavaScript">
<!--
function go_con_del(){
	yes = confirm("정말로 삭제하시 겠습니까?");	
	if(yes){
		location.href = "/company/contacus.act.php?act=delete&con_ix=<?=$con_ix?>";
	}else{
		return;
	}
}
//-->
</SCRIPT>
<body topmargin=0 leftmargin=0>
<form name="aaa" method="POST" action="idea_deatil.act.php">
<input type="hidden" name="act" value="select">
<input type="hidden" name="ix" value="<?=$_GET[ix]?>">
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 상품제안&아이디어 공모 자세히보기
							</td>
							<td width='90%' align='right' valign='top'>
								&nbsp;
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>				
				<td align=center style='padding: 20 10 0 10'>
					<table class="box_shadow" style='width:100%;'  align=top>
						<tr>
							<th class="box_01"></th>
							<td class="box_02"></td>
							<th class="box_03"></th>
						</tr>
						<tr>
							<th class="box_04"></th>
							<td class="box_05" align=right style='height:400px;padding: 0 10 0 10' valign=top>	
								<table border="0" width="100%" cellspacing="1" cellpadding="0">
									<tr height="28" valign="middle">
										<td align="left" style="width:100"><img src="/admin/images/icon/dot_orange_triangle.gif" align=absmiddle> <b>고객명</b></td>							
										<td><?=$db->dt[name]?></td>
										<td align="left" ><img src="/admin/images/icon/dot_orange_triangle.gif" align=absmiddle> <b>분류명</b></td>
										<td><?=$idea_choice?></td>
									</tr>
									<tr><td colspan=4 class=dot></td></tr>

									<tr height="28" valign="middle">
										<td align="left" style="width:100"><img src="/admin/images/icon/dot_orange_triangle.gif" align=absmiddle> <b>제목 </b></td>									
										<td colspan="3"><?=$db->dt[subject]?></td>
									</tr>
									<tr><td colspan=4 class=dot></td></tr>
				
									<tr height='150'valign="middle">
										<td align="left" ><img src="/admin/images/icon/dot_orange_triangle.gif" align=absmiddle> <b>내용</b></td>		
															
										<td colspan="3" style='padding:10 0 0 10'><textarea rows=20 cols=50><?=$db->dt[content]?></textarea></td>
									</tr>
									<tr>
										<td height="10"></td>
									</tr>
									<tr height="28" valign="middle">
										<td align="left" style="width:100"><img src="/admin/images/icon/dot_orange_triangle.gif" align=absmiddle> <b>채택여부 </b></td>								
										<td colspan="3" style="padding-left:5px;"><input type="checkbox" name="select" value='Y'></td>
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
				</td>			
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10 0 0 0' colspan=2>
			 <input type="image" src='../image/ok.gif' border=0>
			 <a href="idea_deatil.act.php?ix=<?=$_GET[ix]?>&mode=del"><img src='../image/btc_del.gif' border=0></a>
		</td>
	</tr>
</TABLE>
</form>
<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>





