<?
include_once("../class/layout.class");
include_once("inventory.lib.php");



$db = new Database;
$pdb = new Database;

if($ps_ix){
	$sql = "select pi.company_id, ps.* from
				inventory_place_info pi,
				inventory_place_section ps
				where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$ps_ix."' ";
	$db->query($sql);
	$db->fetch();
	$place_info = $db->dt;
}

if($ps_ix){
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='15%' />
		<col width='30%' />
		<col width='*' />
	  <tr >
		<td align='left' colspan=3> ".GetTitleNavigation("보관장소", "기초정보관리 > 보관장소 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>보관장소 추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	  <col width = 15% >
	  <col width = 35% >
	  <col width = 15% >
	  <col width = 35% >
	  <tr bgcolor=#ffffff height=25>
		<td class='search_box_title' > <b>창고 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' >
		".SelectEstablishment($place_info[company_id],"et_company_id",'select',"true","  onChange=\"loadPlace(this,'pi_ix')\" ")."
		".SelectInventoryInfo($place_info[company_id],$place_info[pi_ix],'pi_ix','select')."
		</td>
		<td class='search_box_title' > <b>보관 장소명 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' style='padding:5px;'><input type=text class='textbox' name='section_name' id='section_name' value='".$place_info[section_name]."' style='width:130px;' validation='true' dup_check=false title='보관 장소명' onKeyup=\"javascript:valueCheck(this,'보관 장소명')\" ><!--onblur=\"javascript:valueCheck(this,'보관 장소명')\" --> <div style='margin-top:3px;' id='section_name_check_text' >보관 장소명은 영문&숫자(3자~16자) ,한글(1자~5자) 로 입력해 주세요.</div></td>
	  </tr>";

$Contents01 .= "
	  <tr bgcolor=#ffffff height=25>
	    <td class='input_box_title'> <b>보관장소 타입</b></td>
		<td class='input_box_item'>";
		if($place_info[section_type] == "G" || $place_info[section_type] == ""){
			$Contents01 .= "
			<input type='radio' name='section_type' id='section_type_G' value='G' ".($place_info[section_type] == "G" || $place_info[section_type] == "" ? "checked":"")." ><label for='section_type_G'>일반장소</label>";

			$Contents01 .= "
			<!--input type='radio' name='section_type' id='section_type_S' value='S' ".($place_info[section_type] == "S" ? "checked":"")." ><label for='section_type_S'>입고 보관장소
			</label>
			<input type='radio' name='section_type' id='section_type_D' value='D' ".($place_info[section_type] == "D"  ? "checked":"")." ><label for='section_type_D'>출고 보관장소</label-->";
		}else{
			$Contents01 .= "
			<input type='hidden' name='section_type' value='".$place_info[section_type]."' >";
			
			if($place_info[section_type] == "S"){
				$Contents01 .= "입고 보관장소";
			}elseif($place_info[section_type] == "D"){
				$Contents01 .= "출고 보관장소";
			}elseif($place_info[section_type] == "P"){
				$Contents01 .= "반품 보관장소 (양호)";
			}elseif($place_info[section_type] == "B"){
				$Contents01 .= "반품 보관장소 (불량)";
			}else{
				$Contents01 .= "일반 보관장소";
			}
		}
		$Contents01 .= "
		</td>
		<td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item'>
	    	<input type=radio name='disp' value='1' id='disp_1' ".($place_info[disp] == "1" || $place_info[section_type] == "" ? "checked":"")." ><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' id='disp_0' ".($place_info[disp] == "0" ? "checked":"")." ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=25>
	    <td class='input_box_title'> <b> 부피 (cm) </b></td>
		<td class='input_box_item' colspan='3'>
			<input type=text class='textbox number' name='width_length' id='width_length' style='width:30px;' value='".$place_info[width_length]."'> 가로(W) *
			<input type=text class='textbox number' name='depth_length' id='depth_length' style='width:30px;' value='".$place_info[depth_length]."'> 세로(D) *
			<input type=text class='textbox number' name='height_length' id='height_length' style='width:30px;' value='".$place_info[height_length]."'> 높이(H)
		</td>
	  </tr>
	  </table>";

$ContentsDesc01 =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>";
}else{
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a></td></tr>
</table>";
}


$Contents = "<form name='section_frm' action='place_section.act.php' method='post' onsubmit='return CheckFormValue(this)'  target='act'><!--enctype='multipart/form-data'-->
<input name='act' type='hidden' value='".$act."'>
<input name='ps_ix' type='hidden' value='".$ps_ix."'>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td   >창고 내에 보관장소를 구분하여 관리 하실 수 있습니다..</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > </td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("보관장소", $help_text, 60);

 $Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
 <script language='javascript'>
 function updatesectionInfo(ps_ix,pi_ix,section_type,section_name,is_basic,disp){

 	var frm = document.section_frm;

 	frm.act.value = 'update';
 	frm.ps_ix.value = ps_ix;
	//alert(pi_ix);
	$('#pi_ix').val(pi_ix);
	$('input:radio[name=section_type]:input[value='+section_type+']').attr('checked',true);
	/*
	for(i=0;i < frm.pi_ix.length;i++){
 		if(frm.pi_ix[i].value == pi_ix){
 			frm.pi_ix[i].selected = true;
 		}
 	}
	*/

 	frm.section_name.value = section_name;

 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function DeleteSectionInfo(act, ps_ix){
 	if(confirm('해당보관장소 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.section_frm;
 		frm.act.value = act;
 		frm.ps_ix.value = ps_ix;
 		frm.submit();
 	}
}



function valueCheck(this_, obj_name){
	//alert(this_.id);
	//alert($(this_).val());
	
		$.post('place_section.act.php', {
		  value: $('#'+this_.id).val(),
		  act: 'value_check_jquery',
		  value_name:this_.id
		}, function(data){
			//alert(data);
			
			if(data == '300') {
				$('#'+this_.id+'_check_text').css('color','#00B050').html('사용 가능한 '+obj_name+' 입니다.');
				$('#'+this_.id+'_flag').val('1');
				$('#'+this_.id).attr('dup_check','true');
				//alert($('#'+this_.id).attr('dup_check'));
			} else if(data == '130') {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html(''+obj_name+'는 영문&숫자(3자~16자) ,한글(1자~5자) 로 입력해 주세요.'); 
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			} else if(data == '120') {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html('이미 사용중인 '+obj_name+' 입니다.');
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			} else if(data == '110') {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html('첫글자는 영문으로, 다음은 영문(소문자)과 숫자의 조합만 가능합니다.');
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			} else {
				$('#'+this_.id+'_check_text').css('color','#FF5A00').html('이미 사용중인 '+obj_name+' 입니다.');
				//$('#'+this_.id+'_check_text').html(data);
				$('#'+this_.id+'_flag').val('0');
				$('#'+this_.id).attr('dup_check','false');
				return false;
			}
			
		});
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 기초정보관리 > 보관장소";
$P->title = "보관장소";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*
CREATE TABLE  `dev`.`inventory_place_section` (
`ps_ix` INT( 8 ) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  '인덱스',
`pi_ix` INT( 6 ) NOT NULL COMMENT  '창고인덱스',
`section_name` VARCHAR( 200 ) NOT NULL COMMENT  '보관장소명',
`disp` CHAR( 1 ) NOT NULL COMMENT  '사용여부',
`regdate` DATETIME NOT NULL COMMENT  '등록일',
INDEX (  `pi_ix` )
) ENGINE = MYISAM COMMENT =  '보관장소정보';
*/
?>