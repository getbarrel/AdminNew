<?
include("../class/layout.class");


$db = new Database;
$mdb= new Database;

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($FromYY == ""){

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}

if ($vFromYY == ""){

	$sDate2 = date("Y/m/d", $before10day);
	$eDate2 = date("Y/m/d");

	$startDate2 = date("Ymd", $before10day);
	$endDate2 = date("Ymd");

}else{

	$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
	$startDate2 = $vFromYY.$vFromMM.$vFromDD;
	$endDate2 = $vToYY.$vToMM.$vToDD;

}

if ($birYY == ""){

	$sDate3 = date("Y/m/d");
	$eDate3 = date("Y/m/d");

	$startDate3 = date("Ymd");
	$endDate3 = date("Ymd");
}else{

	$sDate3 = $birYY."/".$birMM."/".$birDD;
	$eDate3 = "none";
	$startDate3 = $birYY.$birMM.$birDD;
	$endDate3 = "none";
	$birDate = $birYY.$birMM.$birDD;
}


$Script ="
<script language='JavaScript' >

function DeleteDropMemberDiv(drop_ix){
	if(confirm('정말로 삭제하시겠습니까?')){
		window.frames['iframe_act'].location.href='dropmember.act.php?drop_ix='+drop_ix+'&act=delete&info_type=".$info_type."';
	}
}
function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}
}
function ChangeBirDate(frm){
	if(frm.bir.checked){
		frm.birYY.disabled = false;
		frm.birMM.disabled = false;
		frm.birDD.disabled = false;
	}else{
		frm.birYY.disabled = true;
		frm.birMM.disabled = true;
		frm.birDD.disabled = true;
	}
}

function init(){

	var frm = document.eidt_form;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}
if($visitdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}
if($bir != "1"){
$Script .= "
/*
	frm.birYY.disabled = true;
	frm.birMM.disabled = true;
	frm.birDD.disabled = true;
*/";
}
$Script .= "
}
</Script>";

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("탈퇴회원관리", "회원관리 > 탈퇴회원관리 ")."</td>
	  </tr>
	</table>";

$Contents02 .= "

	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "list" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='dropmember.php?info_type=list'>탈퇴회원리스트</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "add" || $info_type == ""? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents02 .= "<a href='dropmember_setup.php?info_type=add'>탈퇴사유분류설정</a>";

						$Contents02 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	  </table>
";

if($drop_ix){
	$sql = "select * from common_dropmember_setup where drop_ix = '".$drop_ix."'";
	//echo "$sql";
	$db->query($sql);
	$db->fetch();

	$act = 'update';
}else{
$act = 'insert';
}

$Contents02 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
	  <tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 탈퇴사유분류설정</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
	  </tr>
	</table>";

$Contents02 .= "
<form name='edit_form' action='dropmember.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target=''><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='drop_ix' type='hidden' value='$drop_ix'>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr bgcolor=#ffffff  height='30' >
	    <td class='search_box_title' width='16%'> <b>탈퇴사유코드 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' >
		<input type=text class='textbox point_color' name='dp_code' value='".$db->dt[dp_code]."' style='width:80px;' maxlength=2 size=2 validation='true' title='탈퇴사유코드'> * 2자리사용</td>
	    <td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
	    <td class='search_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff  height='30' >
	    <td class='search_box_title' width='16%'> <b>탈퇴사유분류명 <img src='".$required3_path."'></b> </td>
	    <td class='search_box_item' colspan='3'><input type=text class='textbox point_color' name='dp_name' value='".$db->dt[dp_name]."' style='width:300px;' validation='true' title='탈퇴사유명'></td>
	  </tr>
	  <tr bgcolor=#ffffff height=110>
		<td class='input_box_title'><b>사유상세설명</b></td>
		<td class='input_box_item' colspan=3><textarea type=text class='textbox' name='dp_msg' value='".$db->dt[dp_msg]."' style='width:98%;height:85px;padding:2px;'>".$db->dt[dp_msg]."</textarea></td>
	  </tr>
	</table>
	<table><br><br>";

$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td height='20'></td></tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td height='20'></td></tr>
</table>
";
$Contents02 .= "
</form>";

$Contents02 .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
  <tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 탈퇴사유 리스트</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
  </tr>
</table>";

$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width='5%'>
	<col width='7%'>
	<col width='15%'>
	<col width='20%'>
	<col width=10%>
	<col width=10%>
	<col width=10%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>탈퇴사유코드</td>
		<td class='m_td'>탈퇴사유분류명</td>
		<td class='m_td'>탈퇴사유분류설명</td>
		<td class='m_td'>사용유무</td>
		<td class='m_td'>등록일</td>
		<td class='e_td'>관리</td>
		</tr>";

$sql = "select * from common_dropmember_setup where 1 order by drop_ix asc";

$db->query($sql);
$total = $db->total;
$dropmember_array = $db->fetchall();

if(count($dropmember_array) > 0){
	for($i=0;$i<count($dropmember_array); $i++){

		switch($dropmember_array[$i][disp]){
			case '1':
				$disp = "사용";
				break;
			case '0':
				$disp = "미사용";
				break;
		}
		$Contents02 .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$dropmember_array[$i][dp_code]."</td>
					<td class='list_box_td list_bg_gray'>".$dropmember_array[$i][dp_name]."</td>
					<td class='list_box_td ' style='padding-left:10px; text-align:left;'>".$dropmember_array[$i][dp_msg]."</td>
					<td class='list_box_td list_bg_gray'>".$disp."</td>
					<td class='list_box_td'>".$dropmember_array[$i][regdate]."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .="<a href='dropmember_setup.php?drop_ix=".$dropmember_array[$i][drop_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents02 .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .="<a href='javascript:DeleteDropMemberDiv(\"".$dropmember_array[$i][drop_ix]."\");'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents02 .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents02 .="
					</td>
				</tr>";
	}
	$Contents02 .=	"</table>";
	$Contents02 .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents02 .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents02 .= "</table>";
$Contents02 .= "<table>";




if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents02 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents02 .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents02 .="</table><br>";
$Contents = $Contents02;


/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >탈퇴한 고객들이 작성한 내역입니다. </td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("탈퇴회원관리", $help_text);




$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 탈퇴회원관리";
$P->title = "탈퇴회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();




?>