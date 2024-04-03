<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
$db = new Database;
?>

<html>
<title>출고 창고 지정하기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='member.js'></Script>
<style>

input {border:1px solid #c6c6c6}
</style>
<body topmargin=0 leftmargin=0 ><!--onload="Init(document.send_mail);"-->
<form name='input_pop' method='post' action='main_inventory.act.php'>
<input type='hidden' name='pid' value='<?=$id?>'>
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 출고 창고 지정
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border=0 cellpadding=0 cellspacing=0>
						<tr height=30px>
							<td width=100px align=center style="border-right:1px solid #eaeaea;border-bottom:1px solid #eaeaea;border-left:1px solid #eaeaea">창고</td>
							<td colspan=4 style="border-right:1px solid #eaeaea;border-bottom:1px solid #eaeaea">&nbsp;&nbsp;<?=makeSelectBox($db,'inventory_place_info','','inventory_code','등록된창고 가 없습니다.')?></td>
						</tr>
						<tr>
							<td colspan=2 align=center style="padding-top:10px"><input type='image' src='../images/btn/ok.gif' style="border:0"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	
</TABLE>
</form>
<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>

<?
function makeSelectBox($db,$table,$where,$select_name,$msg){
	$sdb = new Database;
	$sdb->query("select * from shop_product where id = '".$pid."'");
	$sdb->fetch();

	$db->query("SELECT * FROM ".$table." ".$where." ");
	
	$mstring = "<select name='$select_name' class=small style='width:150px;'>";
	
		if($db->total){
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
				$mstring .= "<option value='".$db->dt[inventory_code]."' ".($db->dt[inventory_code] == $sdb->dt[main_inventory] ? "selected":"").">".$db->dt[inventory_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";
	
	return $mstring;
}
?>
