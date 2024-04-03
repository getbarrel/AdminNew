<?
include("../class/layout.class");

$title = "보관장소등록";

$db = new Database;

if($pi_ix == ""){
	$act = "insert";
}else{
	$db->query("SELECT * FROM inventory_place_info WHERE pi_ix = '".$pi_ix."'");
	$act = "update";
}

$db->fetch();
$place_info = $db->dt;
$phone = explode("-",$db->dt[place_tel]);
$fax = explode("-",$db->dt[place_fax]);

$Contents01 = "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$title", "재고관리 > $title")."</td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:0px 0px 5px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 보관장소 정보</b></div>")."</td>
	  </tr>
	</table>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'  style='border-collapse:separate; border-spacing:1px;'>
	  <col width=18% />
	  <col width=32% />
	  <col width=18% />
	  <col width=32% />
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>보관장소명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' ><input type=text name='place_name' value='".$place_info[place_name]."' class='textbox'  style='width:97%' validation='true' title='보관장소명'></td>
		<td class='input_box_title'> <b>사용여부</b>   </td>
		<td class='input_box_item'>
		<input type='radio' name='disp' id='disp_y' value='Y' ".($place_info[disp] == "Y"  ? "checked":"")." ><label for='disp_y'>사용</label>
		<input type='radio' name='disp' id='disp_n' value='N' ".($place_info[disp] == "N"  ? "checked":"")." ><label for='disp_n'>사용안함</label>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>보관장소 유형</b>    </td>
		<td class='input_box_item'>
		<input type='radio' name='place_type' id='place_type_1' value='1' ".($place_info[place_type] == "1" || $place_info[place_type] == "" ? "checked":"")." ><label for='place_type_1'>창고</label>
		<input type='radio' name='place_type' id='place_type_2' value='2' ".($place_info[place_type] == "2"  ? "checked":"")." ><label for='place_type_2'>선반</label>
		<input type='radio' name='place_type' id='place_type_2' value='3' ".($place_info[place_type] == "3"  ? "checked":"")." ><label for='place_type_3'>공장</label>
		<input type='radio' name='place_type' id='place_type_3' value='4' ".($place_info[place_type] == "4"  ? "checked":"")." ><label for='place_type_4'>외주공장</label>
		<input type='radio' name='place_type' id='place_type_9' value='9' ".($place_info[place_type] == "9"  ? "checked":"")." ><label for='place_type_9'>기타</label>
		</td>
	    <td class='input_box_title'> <b>반품지정창고</b>   </td>
		<td class='input_box_item'>
		<input type='radio' name='return_position' id='return_position_y' value='Y' ".($place_info[return_position] == "Y"  ? "checked":"")." ><label for='return_position_y'>사용</label>
		<input type='radio' name='return_position' id='return_position_n' value='N' ".($place_info[return_position] == "N"  ? "checked":"")." ><label for='return_position_n'>사용안함</label>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>보관장소 전화번호 </b></td>
		<td class='input_box_item'><input type=text name='place_tel' value='".$place_info[place_tel]."' class='textbox'  style='width:95%' validation='false' title='보관장소 전화번호'></td>
		<td class='input_box_title'> <b>보관장소 팩스 </b></td>
		<td class='input_box_item'><input type=text name='place_fax' value='".$place_info[팩스]."' class='textbox'  style='width:95%' validation='false' title='보관장소 팩스'></td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>보관장소 설명</b>   </td>
	    <td class='input_box_item' style='padding:5px;' colspan=3><textarea name='place_msg'  style='padding:3px;width:97%;height:70px;' validation=false title='상점 설명'>".$place_info[place_msg]."</textarea></td>
	  </tr>
	  </table>";

$ContentsDesc01 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>

	</td>
</tr>
</table>
";

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='place.act.php' method='post' onsubmit='return CheckFormValue(document.edit_form)' enctype='multipart/form-data' target='act'>
<input name='act' type='hidden' value='$act'><input name='pi_ix' type='hidden' value='".$place_info[pi_ix]."'>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."</table >";




$Script = "<script language='javascript' src='company.add.js'></script>
<script language='javascript'>
function zipcode() {
	var zip = window.open('../member/zipcode.php','','width=440,height=300,scrollbars=yes,status=no');
}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = 'block';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}
</script>
";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = inventory_menu();
$P->strContents = $Contents;
$P->Navigation = "재고관리 > 거래업체관리 > 보관장소관리 > $title";
$P->title = "$title";
echo $P->PrintLayOut();



?>