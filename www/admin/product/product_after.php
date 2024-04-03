<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

?>

<html>
<title>상품평쓰기</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='input_pop.js'></Script>
<script language='JavaScript' src='/admin/js/auto.validation.js'></Script>

<style>

input {border:1px solid #c6c6c6}
</style>

<body topmargin=0 leftmargin=0 ><!--onload="Init(document.send_mail);"-->
<?
$db = new Database;
$mdb = new Database;
$sdb = new Database;
$idb = new Database;
$db->query("SELECT pname FROM ".TBL_SHOP_PRODUCT."  where id = '".$id."'");	
$db->fetch();


?>	
<form name=after_form method=get  action='product_after.act.php' >
 <input type=hidden name=act value=insert>
 <input type=hidden name=pid value="<?=$id?>">
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 상품평쓰기
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
	


<tr height=25>
	<td style="padding:10px 0px 0px 15px"><img src='../images/dot_org.gif' align=absmiddle> <b><?=$db->dt[pname]?> 상품평쓰기</b></td>
</tr>
<tr height=10>
	<td></td>
</tr>
<tr>
  <td valign="top" align="center" style="width:100%;">
	  <table class='box_shadow' style='width:98%;height:20px' ><!---mbox04-->
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05 align=center'  width='100%'>
				  <table border=0 cellspacing=0 cellpadding=0 width="621">
					  <tr>
						  <td width="10%" align="left" style="padding-left:10px;">평가</td>
						  <td style="padding-left:30px;"><input type="radio" name="uf_valuation" value='5' checked> <img src="../image/start1.gif" align="absmiddle">&nbsp;&nbsp;<input type="radio" name="uf_valuation" value='4'> <img src="../image/star5.gif" align="absmiddle">&nbsp;&nbsp;<input type="radio" name="uf_valuation" value='3'> <img src="../image/start4.gif" align="absmiddle">&nbsp;&nbsp;<input type="radio" name="uf_valuation" value='2'> <img src="../image/start3.gif" align="absmiddle">&nbsp;&nbsp; <input type="radio" name="uf_valuation" value='1'> <img src="../image/start2.gif" align="absmiddle"></td>
					  </tr>
					  
					  <tr>
						  <td width="10%" align="left" style="padding-left:10px;">제목</td>
						  <td style="padding-left:30px;"><input type="text" name="uf_subject" style="width:495px;height:18px;border:1px solid #eeeeee;background-color:#f5f5f5"></td>
					  </tr>
					  <tr>
						  <td width="10%" align="left" style="padding-left:10px;">작성자</td>
						  <td style="padding-left:30px;"><input type="text" name="uf_name" style="width:495px;height:18px;border:1px solid #eeeeee;background-color:#f5f5f5"></td>
					  </tr>
					  
					  <tr>
						  <td width="10%" align="left" style="padding-left:10px;padding-top:3px;" valign="top">내용</td>
						  <td style="padding-left:30px;"><textarea name="uf_contents" id="CommentTextAreaLay"' class="textarea_board" style="width:495px;height:300px;border:1px solid #eeeeee;background-color:#f5f5f5"></textarea></td>
					  </tr>
				  </table>
				 </td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
		</table>
  </td>
</tr>
<tr>
	<td align="right" style="padding:10px 12px 0px 0px;"><input type="image" src="../image/b_save.gif"></td>
</tr>

</TABLE>
</form>
<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
</body>
</html>

