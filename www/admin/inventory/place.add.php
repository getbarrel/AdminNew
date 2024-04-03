<?
include("../class/layout.class");
include("inventory.lib.php");

$title = "창고등록";

$db = new Database;

if($pi_ix == ""){
	$act = "insert";
}else{
	$db->query("SELECT * FROM inventory_place_info WHERE pi_ix = '".$pi_ix."'");
	$act = "update";
	$db->fetch();
	$place_info = $db->dt;
	$phone = explode("-",$db->dt[place_tel]);
	$fax = explode("-",$db->dt[place_fax]);
}



$Contents01 = "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$title", "재고관리 > $title")."</td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:0px 0px 5px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 창고 정보</b></div>")."</td>
	  </tr>
	</table>";

	$Contents02 = get_place($place_info,$required3_path);

$ContentsDesc01 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>

	</td>
</tr>
</table>
";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>";
}else{
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a></td></tr>
</table>";
}

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




$Script = "<script language='javascript' src='../basic/company.add.js'></script>
<script language='javascript'>

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = 'block';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}

function loadperson(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var key = sel.getAttribute('name');

	window.frames['act'].location.href = './person.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&key='+key;

}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

</script>
";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = inventory_menu();
$P->strContents = $Contents;
$P->Navigation = "재고관리 > 거래업체관리 > 창고관리 > $title";
$P->title = "$title";
echo $P->PrintLayOut();



?>