<?
include("../class/layout.class");


$db = new Database;
$db2 = new Database;
if($act == "update"){
	$db->query("SELECT * FROM ".TBL_SHOP_MANAGE_FLASH."  where mf_ix = '".$mf_ix."' ");
	$db->fetch();
	$path = $admin_config[mall_data_root]."/images";
	$path = $path."/flash_data/";
} else {
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("움직이는 배너생성", "프로모션/전시 > 움직이는 배너생성 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>프로모션 정보</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col  width='15%'>
	  <col  width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>구분코드 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' >".($act == "update" ? "<input type='hidden' name='mf_type' value='".$db->dt['mf_type']."' validation=true title='플래쉬타입'>".$db->dt['mf_type']." ":"<input type='text' name='mf_type' value='".$db->dt['mf_type']."' class='textbox' validation=true title='구분코드'>")."
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>효과선택 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<select name='mf_effect' style='250px;' validation=true title='효과선택'>
				<option value='' >선택하세요</option>
				<option value='S' ".CompareReturnValue(S,$db->dt['mf_effect']).">슬라이드</option>
				<option value='F' ".CompareReturnValue(F,$db->dt['mf_effect']).">패이드인</option>
				<option value='R' ".CompareReturnValue(R,$db->dt['mf_effect']).">랜덤</option>
				<option value='T' ".CompareReturnValue(T,$db->dt['mf_effect']).">지그재그</option>
			</select>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>플래쉬제목 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='mf_name' value='".$db->dt['mf_name']."' style='width:230px;' validation=true title='플래쉬제목'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title' valign='middle'>
			<table cellpadding=0 cellspacing=0 height='78px;'>
				<tr>
					<td width='70px'><b style='color:#000000;'>상세내용 <img src='".$required3_path."'></b><td>
					<td><div style='margin-left:15px;margin-top:5px;'><img src='../images/".$admininfo["language"]."/btn_add.gif' alt='옵션추가' id='flash_addbtn'> <img src='../images/".$admininfo["language"]."/btn_del.gif' alt='옵션삭제' id='flash_delbtn'><div>
					</td>
				</tr>
			</table>
		</td>
		<td class='input_box_item'>

		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' id='flash_table'>
		";
	//$mfdArr = array();
	$db2->query("SELECT * FROM shop_manage_flash_detail  where mf_ix = '".$mf_ix."' order by mfd_ix ASC ");//order by 가 regdate 로 되어 있던 것을 고침 kbk 13/02/15
	if($db2->total){
		$mfdArr = $db2->fetchall();
	}
$clon_no = 0;
if(is_array($mfdArr)){
	foreach($mfdArr as $_key=>$_value){

		if($_key == 0) {
		$Contents01 .= "<tbody>";
		} else if($_key == 1){
		$Contents01 .= "<tfoot>";
		}
		$Contents01 .= "
				  <tr bgcolor=#ffffff  class='clone_tr'>

					<td height='25' style='padding:10px 0; solid #d3d3d3;'>
					<input type=hidden name='mfd_ix[]' class='mfd_ix' value='".$mfdArr[$_key][mfd_ix]."' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='mf_file[]' style='width:255px;' validation=false title='파일'> <span class='file_text'>".$mfdArr[$_key][mf_file]."<input type='checkbox' name='nondelete[".$mfdArr[$_key][mfd_ix]."]' value='1' checked>업로드된 파일유지</span><br><br>
					 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox mf_link' name='mf_link[]' value='".$mfdArr[$_key][mf_link]."' style='width:248px;' validation=true title='링크'>
					 타 이 틀 : <input type=text class='textbox mf_title' name='mf_title[]' value='".$mfdArr[$_key][mf_title]."' style='width:230px;' validation=true title='타이틀'>
					</td>
				  </tr>
				  ";
		if($_key == 0) {
		$Contents01 .= "</tbody>";
		} else {
			$clon_no++;
		}
	}
} else {
		$Contents01 .= "
				 <tbody>
				  <tr bgcolor=#ffffff  class='clone_tr'>
					<td height='25' style='padding:10px 0; solid #d3d3d3;'>
					<input type=hidden name='mfd_ix[]' value='' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='mf_file[]' style='width:255px;' validation=true title='파일'> <br><br>
					 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox mf_link' name='mf_link[]' value='' style='width:248px;' validation=true title='링크'>
					 타 이 틀 : <input type=text class='textbox mf_title' name='mf_title[]' value='' style='width:230px;' validation=true title='타이틀'>
					</td>
				  </tr>
				 </tbody>
				 ";
}
if($clon_no == 0){
$Contents01 .= "<tfoot>";
}
$Contents01 .= "
		</tfoot>
		</table>

		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b> 사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' ".($db->dt[disp] == 1 ? "checked" : "")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ".($db->dt[disp] == 0 ? "checked" : "")."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}else{
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_write_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a></td></tr>
</table>
";    
}

$Contents = "<form name='mf_form' action='main_flash.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
					<input name='act' type='hidden' value='".$act."'>
					<input name='mf_ix' type='hidden' value='".$mf_ix."'>";
$Contents = $Contents. "<table width='100%' border=0 style='margin-bottom:200px;'>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

 $Script = "
 <Script Language='JavaScript' src='design.js'></Script>
 <Script Language='JavaScript'>
var eqIndex = $clon_no;
$(document).ready(function () {
	var copy_text;
	$('#flash_addbtn').click(function(){
		eqIndex++;
		copy_text = $('#flash_table tbody:first').html();
		$(copy_text).clone().appendTo('#flash_table tfoot');
		$('.file_text:eq('+eqIndex+')').text('');
		$('.mf_link:eq('+eqIndex+')').val('');
		$('.mf_title:eq('+eqIndex+')').val('');
		$('.mfd_ix:eq('+eqIndex+')').val('');
	});

	$('#flash_delbtn').click(function(){
		var len = $('#flash_table .clone_tr').length;
		if(len > 1){
			eqIndex--;
			$('#flash_table .clone_tr:last').remove();
		}else{
			return false;
		}
	});

});
</script>
 ";

$P = new LayOut();
$P->prototype_use = false;
$P->jquery_use = true;
$P->addScript = $Script;
$P->strLeftMenu = display_menu("/admin",$category_str);
$P->Navigation = "프로모션/전시 > 배너관리 > 움직이는 배너생성";
$P->title = "움직이는 배너생성";
$P->strContents = $Contents;
echo $P->PrintLayOut();


/*

create table ".TBL_OAASYS_MANAGE_FLASH." (
mf_ix int(4) unsigned not null auto_increment  ,
mf_type varchar(2) not null,
mf_name varchar(20) null default null,
mf_link varchar(255) null default null,
mf_file varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(mf_ix));
*/
?>