<?
include("../class/layout.class");


$db = new Database;

$sql = "select * from tax_receipt where re_ix= '$re_ix'";

//echo $sql;
		$db->query($sql);
		$db->fetch();



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
		<input type='hidden' name='company_id' value='".$db2->dt[company_id]."' >
		<input type='hidden' name='company_name' value='".$db2->dt[com_name]."' >
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("증빙자료관리", "세무관리 > 증빙자료관리 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	 <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px; text-align:right;'><img src='../image/btn_down.gif' align=absmiddle><img src='../image/btn_print2.gif' align=absmiddle></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
		<col width = '30%' />
		<col width = '70%' />
	  <tr>
		<td align=center colspan='2'>";
		if(is_file($_SERVER[DOCUMENT_ROOT]."/".$admin_config[mall_data_root]."/images/tax_receipt/".$re_ix."/".$db->dt[receipt_file])){
			//echo 1;
			$info = getimagesize($_SERVER[DOCUMENT_ROOT]."/".$admin_config[mall_data_root]."/images/tax_receipt/".$re_ix."/".$db->dt[receipt_file]);
			if($info[0] > 500){
				$img_width = 'width:500px;';
			}
			$Contents01 .= "
			<img src = '".$admin_config[mall_data_root]."/images/tax_receipt/".$re_ix."/".$db->dt[receipt_file]."' style='$img_width'>";
		}else{
			//echo 2;
			$Contents01 .= "
			<img src = '".$admin_config[mall_data_root]."/images/tax_receipt/tax_no_img.gif'>";
		}$Contents01 .= "
		</td>
	  </tr>
	  <tr>
		<td class='input_box_title'> <b>항목지정 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<select name='receipt_div' style='width:50%'>
				<option value=''>-- 선택 --</option>
				<option value='1'  ".CompareReturnValue("1",$receipt_div,"selected").">신용카드</option>
				<option value='2'  ".CompareReturnValue("2",$receipt_div,"selected").">현금영수증</option>
				</select>
		  </td>
		</td>
	  </tr>
	  <tr>
		<td class='input_box_title'> <b>처리현황 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<select name='status' style='width:50%'>
				<option value=''>-- 선택 --</option>
				<option value='N'  ".CompareReturnValue("N",$status,"selected").">미처리</option>
				<option value='Y'  ".CompareReturnValue("Y",$status,"selected").">처리완료</option>
				</select>
		  </td>
		</td>
	  </tr>
	</table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}else{
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>
	";

}



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='bank_form' action='tax_document_input.act.php' method='post' onsubmit='return CheckFormValue(this)' target='' enctype='multipart/form-data'>
<input name='act' type='hidden' value='update'>
<input name='re_ix' type='hidden' value='$re_ix'>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";


$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>
 function updateIconInfo(idx,icon_name,disp){
 	var frm = document.bank_form;

 	frm.act.value = 'update';
 	frm.idx.value = idx;
 	frm.icon_name.value = icon_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	frm.icon_file.setAttribute('validation','false'); //수정인 경우 무조건 이미지 파일을 업로드하라고 나오는 것을 방지 kbk

}

 function deleteIconInfo(act, idx){
 	if(confirm('해당계좌 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.bank_form;
 		frm.act.value = act;
 		frm.idx.value = idx;
 		frm.submit();
 	}
}

 </script>
 ";
	
	
 

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "세무관리 > 증빙자료관리 > 세무서비스";
	$P->NaviTitle = "증빙자료관리";
	$P->strLeftMenu = tax_receipt();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();



/*

create table ".TBL_SHOP_BANKINFO." (
bank_ix int(4) unsigned not null auto_increment  ,
bank_name varchar(20) null default null,
bank_number varchar(20) null default null,
bank_owner varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(bank_ix));
*/
?>