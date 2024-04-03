<?
include("../class/layout.class");

$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("택배송장등록", "택배관리 > 택배송장등록 ")."</td>
	  </tr>
	  <tr >
	    <td align='left' colspan=4>";
if($mmode == ""){
$Contents01 .= "
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='application.php'\">택배접수</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='application_excel.php'\">택배송장등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
				<td class='btn'>

				</td>
			</tr>
			</table>
		</div>";
}
$Contents01 .= "
		<div class='t_no' style='margin: 10px 0px 20px; padding:5px 0px;border-top: solid 3px #c6c6c6; '>
			<!-- my_movie start -->
			<div class='my_box' style='height:150px;'>
				<form name='div_form' action='application_excel.act.php' method='post' onsubmit='return CheckForm(this)' enctype='multipart/form-data'  style='display:inline;'>
				<input name='act' type='hidden' value='excel_input'>
				<input name='ab_ix' type='hidden' value='$ab_ix'>
					<table width=100% cellpadding=0 cellspacing=0 border=0 class='input_table_box'>
					<col width=130>
					<col width=*>
					<col width=130>
					<col width=*>

				  <tr bgcolor=#ffffff >
				    <td class='input_box_title'><b> 엑셀파일 : </b></td>
				    <td colspan=3  class='input_box_item' style='padding:10px 0 10px 5px;'><input type=file class='textbox' name='excel_file' value='' style='width:230px;'>
				    <!--a href='./addressbook.act.php?act=sample_excel_down'><img src='../image/btn_sample_excel_save.gif' align=absmiddle></a><br-->
				    <div class=small>배송처리된 엑셀을 등록하시면 자동으로 배송완료 처리되며 송장번호가 자동 등록됩니다.</div></td>
				  </tr>

				  <!---tr bgcolor=#ffffff >
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> 메일수신여부 : </b></td>
				    <td>
				    	<input type=radio name='mail_yn' id='mail_yn_1' value='1' ".(($db->dt[mail_yn] == "1" || $db->dt[mail_yn] == "") ? "checked":"")."><label for='mail_yn_1'>수신</label>
				    	<input type=radio name='mail_yn' id='mail_yn_0' value='0' ".($db->dt[mail_yn] == "0" ? "checked":"")."><label for='mail_yn_0'>수신거부</label>
				    </td>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> SMS 수신여부 : </b></td>
				    <td>
				    	<input type=radio name='sms_yn' id='sms_yn_1' value='1' ".(($db->dt[sms_yn] == "1" || $db->dt[sms_yn] == "") ? "checked":"")."><label for='sms_yn_1'>수신</label>
				    	<input type=radio name='sms_yn' id='sms_yn_0' value='0' ".($db->dt[sms_yn] == "0" ? "checked":"")."><label for='sms_yn_0'>수신거부</label>
				    </td>
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr-->
				  </table>
				  <table width=100% cellpadding=5 cellspacing=0 border=0 >
					<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
				  </table>
				  </form>
				</div>

		</div>
	    </td>
	  </tr>

	  </table>";




$Contents = "<table width='100%' border=0>";
$Contents = $Contents."";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >택배송장엑셀을 등록하시면 자동 배송 처리 및 송장번호가 자동입력됩니다..</td></tr>

	</table>
	";


$help_text = HelpBox("택배송장등록", $help_text);
$Contents = $Contents.$help_text;
 $Script = "
 <script  id='dynamic'></script>
 <script language='javascript'>
function CheckForm(frm){
	if(frm.excel_file.value.length < 1){
	 		alert('엑셀 파일을 입력하신 후 확인버튼을 눌러주세요 ');
	 		return false;
 	}

	return true;
}


 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = delivery_menu();
	$P->Navigation = "택배관리 > 택배송장등록";
	$P->strContents = $Contents;
	$P->NaviTitle = "택배접수";

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = delivery_menu();
	$P->Navigation = "택배관리 > 택배송장등록";
	$P->strContents = $Contents;
	$P->title = "택배송장등록";
	echo $P->PrintLayOut();
}

function getCampaignGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM delivery_group abg
				where group_depth = '$depth'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM delivery_group abg
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'
				group by group_ix ";
	}
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
?>