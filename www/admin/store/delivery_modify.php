<?
include("../class/layout.class");

$db = new Database;


if(!empty($_GET['code_ix']))	{
	$db->query("select code_name, code_etc1, code_etc3, code_etc4, disp from ".TBL_SHOP_CODE." where code_gubun='02' and code_ix = '$code_ix' ");

	if($db->total){
		$db->fetch();
		$act = "delivery_update";
	}else{
		$act = "delivery_insert";
	}
}else{
	$act = "delivery_insert";
}



$Script = "
<script language='JavaScript' >
function ReserveReset(){
	var frm = document.forms['reserve'];

	frm.reset();
	frm.act.value = '".$act."';
	frm.code_ix.value = '".$code_ix."';
	frm.code_gubun.value = '2';
}

function CheckReserve(frm){
	if(frm.code_name.value.length < 1){
		alert('업체명을 입력해주세요');
		frm.code_name.focus();
		return false;
	}
	return true;
}

function DeleteDeliveryComapny(selected_name, code_ix){
	if(confirm('['+selected_name+'] 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='delivery.act.php?act=delivery_delete&code_ix='+code_ix;
	}
}
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr height=10>
	    <td align='left' colspan=2> ".GetTitleNavigation("택배사 추가/수정", "상점관리 > 택배사 추가/수정", false)."</td>
	</tr>
	<TR>
		<td align=center colspan=2 style='padding:0px 10px 0px 10px;'>

		<form name='reserve' method='post'  action='delivery.act.php'  onSubmit='return CheckReserve(this)' target=act>
		<input type='hidden' name='act' value='".$act."'>
		<input type='hidden' name='code_ix' value='".$code_ix."'>
		<input type='hidden' name='code_gubun' value='2'>
		<input type='hidden' name='id' value=''>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center' class='input_table_box'>
			<col width=140>
			<col width=*>
			<tr height=30>
				<td class='input_box_title'>업체명</td>
				<td class='input_box_item'> <input type='text' class=textbox  name='code_name' size=30  style='height:22px;' value='".$db->dt[code_name]."'></td>
			</tr>
			<tr height=30>
				<td class='input_box_title' nowrap> 배송정보추적 URL</td>
				<td class='input_box_item' ><textarea  name='code_etc1' style='width:95%;height:40px;padding:2px;margin:5px auto;'>".$db->dt[code_etc1]."</textarea></td>
			</tr>
			<tr height=30>
				<td class='input_box_title'> 전송방법</td>
				<td class='input_box_item'>
					<input type='radio'  name='code_etc3' value='GET' id='method_get' ".(($db->dt[code_etc3] == "GET" || !$db->dt[code_etc3]) ? "checked":"")."><label for='method_get'>GET</label>
					<input type='radio'  name='code_etc3' value='POST' id='method_post' ".($db->dt[code_etc3] == "POST" ? "checked":"")."><label for='method_post'>POST</label>
				</td>
			</tr>
			<tr height=30>
				<td class='input_box_title'> 송장번호 파라미터</td>
				<td class='input_box_item'> <input type='text'  class=textbox name='code_etc4' size=30  style='height:22px;' value='".$db->dt[code_etc4]."'></td>
			</tr>
			<tr height=30>
				<td class='input_box_title'> 사용유무</td>
				<td class='input_box_item'>
					<input type='radio'  name='disp' value=1 ".($db->dt[disp] == 1 ? "checked":"").">사용
					<input type='radio'  name='disp' value=0 ".($db->dt[disp] == 0 ? "checked":"").">미사용
				</td>
			</tr>
		</table>
		<table cellpadding=0 cellspacing=0 align=right>
			<tr>
				<td style='padding:10px 3px;'><input type='image' src='../images/".$admininfo["language"]."/btn_s_ok.gif' align=absmiddle><a href='javascript:self.close();'></td>
				<td style='padding:10px 3px'>
				<a href=\"javascript:DeleteDeliveryComapny('".$db->dt[code_name]."', '".$code_ix."');\"><img src='../images/".$admininfo["language"]."/del.gif' align=absmiddle border=0></a></td>
				<td style='padding:10px 3px'>
				<a href='javascript:self.close();'><img src='../images/".$admininfo["language"]."/btn_s_cancle.gif' align=absmiddle border=0></a></td>
			</tr>
		</td>
		</table>
		</form>
		</td>
	</tr>
</TABLE>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 상점관리 > 택배사 추가/수정";
$P->NaviTitle = "택배사 추가/수정";
$P->strContents = $Contents;
echo $P->PrintLayOut();










