<?
include("../class/layout.class");


$db2 = new Database;

$sql = "select ccd.com_name, ccd.company_id from 
		".TBL_COMMON_USER." cu 
		left join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
		where cu.code = '".$code."'";
//echo $sql;
		$db2->query($sql);
$db2->fetch();



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
		<input type='hidden' name='code' value='".$code."' >
		<input type='hidden' name='company_id' value='".$db2->dt[company_id]."' >
		<input type='hidden' name='company_name' value='".$db2->dt[com_name]."' >
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("기장자료등록", "세무관리 > 기장자료등록 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	 <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>{".$db2->dt[com_name]."} 의 기장자료 등록</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
	  <tr>
	    <td class='input_box_title' style='width:150px;'> <b>해당 분기 선택 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class=textbox name='year' style='width:50px' id='year' value='' validation='true' title='년' /> 년
			<input type='text' class=textbox name='quarter' style='width:50px' id='quarter' value='' validation='true' title='분기' /> 분기
	     </td>
	  </tr>
	  <tr >
	    <td class='input_box_title'> <b>재무재표 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=file class='textbox' name='tax_file' value='' style='width:230px;' validation=true title='재무재표'> </td>
	  </tr>
	  <tr >
	    <td class='input_box_title'> <b>손익계산서 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=file class='textbox' name='income_bill' value='' style='width:230px;' validation=true title='손익계산서'> </td>
	  </tr>
	  <tr  >
	    <td class='input_box_title'> <b>세무사조언 <img src='".$required3_path."'></b> </td>
	    <td >
	    	<textarea rows='20' cols='30' style='width:500px;' name='contents'></textarea>
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
$Contents = $Contents."<form name='bank_form' action='tax_document_input.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act' enctype='multipart/form-data'>
<input name='act' type='hidden' value='insert'>
<input name='idx' type='hidden' value=''>";
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
	$P->Navigation = "세무관리 > 세무기장관리 > 기장자료등록";
	$P->NaviTitle = "기장자료등록";
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