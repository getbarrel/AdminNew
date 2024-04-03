<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>
<title>상품평쓰기</title>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='/admin/js/auto.validation.js'></Script>
*/
//print_r($_SESSION);
$Script = "
<style>
input {border:1px solid #c6c6c6}
</style>
<Script Language='JavaScript'>
function SubmitX(frm){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	return true;
}

</Script>";


$db = new Database;
$mdb = new Database;
$sdb = new Database;
$idb = new Database;
$db->query("SELECT id,admin,pname,sellprice,coprice,regdate FROM ".TBL_SHOP_PRODUCT."  where id = '".$pid."'");
$db->fetch();

$Contents = "
<form name='input_pop' method='post' action='after_pop.act.php' onSubmit='return SubmitX(this);'>
<input type='hidden' name='act' value='insert'>
<input type='hidden' name='pid' value=".$pid.">
<input type='hidden' name='pname' value='".$db->dt[pname]."'>
<input type='hidden' name='admin' value='".$db->dt[admin]."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("상품평쓰기", "상품관리 > 상품평쓰기", false)."</td>
			</tr>
			<tr>
				<td align='left' style='padding:10px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>상품평쓰기</b></td>
			</tr>
			<tr>
				<td style='padding:10px 0px 0px 0px'>
					<table width='100%' cellpadding=5 cellspacing=1 class='input_table_box'>
						<col width='25%' >
						<col width='*' >
						<tr height=35 >
							<th class='input_box_title'>평가 : </th>
							<td class='input_box_item'><input type='radio' class='input' name='uf_valuation' id='uf_5' size='30' value='5' style='border:0px' validation='true' title='평가'> <label for='uf_5'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",5)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-5)."</label> <input type='radio' class='input' name='uf_valuation' id='uf_4' size='30' value='4' style='border:0px' validation='true' title='평가'> <label for='uf_4'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",4)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-4)."</label> <input type='radio' class='input' name='uf_valuation' id='uf_3' size='30' value='3'  style='border:0px' validation='true' title='평가'> <label for='uf_3'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",3)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-3)."</label> <input type='radio' class='input' name='uf_valuation' id='uf_2' size='30' value='2' style='border:0px' validation='true' title='평가'> <label for='uf_2'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",2)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-2)."</label> <input type='radio' class='input' name='uf_valuation' id='uf_1' size='30' value='1' style='border:0px' validation='true' title='평가'><label for='uf_1'> ".str_repeat("<img src='/admin/images/icon_score_01.gif'>",1)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-1)."</label> <input type='radio' class='input' name='uf_valuation' id='uf_0' size='30' value='0' style='border:0px' validation='true' title='평가'><label for='uf_0'> ".str_repeat("<img src='/admin/images/icon_score_01.gif'>",0)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5-0)."</label></td>
						</tr>
						<tr height=35 >
							<th class='input_box_title'>작성자 : </th>
							<td class='input_box_item'><input type='text' name='uf_name' class='textbox' size=40 validation='true' title='작성자'></td>
						</tr>
						<tr height=35 >
							<th class='input_box_title' >제목 : </th>
							<td class='input_box_item'><input type='text' name='uf_subject' class='textbox' size=60 validation='true' title='제목'></td>
						</tr>
						<tr height=155 >
							<th class='input_box_title' >내용 : </th>
							<td class='input_box_item'><textarea name='uf_contents' style='width:95%;height:120px;margin:5px auto;' validation='true' title='내용'></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=center style='padding:10px'><input type='image' src='../images/".$admininfo['language']."/b_save.gif' align=absmiddle style='border:0px;'></td>
			</tr>
		</table>
		</td>
	</tr>

</TABLE>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $addScript;
$P->Navigation = "상품관리 > 상품평쓰기";
$P->NaviTitle = "상품평쓰기 ";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

