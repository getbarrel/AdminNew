<?
include("../class/layout.class");
include_once("origin.lib.php");

$db = new Database;


$sql = "select od.* , case when depth = 1 then od_ix  else parent_od_ix end as group_order from common_origin_div od where od_ix = '".$od_ix."'   ";
$db->query($sql);
if($db->total){
	$db->fetch();
	
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<col width='15%'>
	<col width='35%'>
	<col width='15%'>
	<col width='35%'>
	<tr >
		<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("원산지 분류관리", "상품분류관리 > 원산지 분류관리 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
			".origin_tab("div")."
		</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 원산지 분류 추가/수정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b> 원산지 분류 : </b></td>
		<td class='input_box_item'>
			<input type=radio name='depth' value='1' id='depth_1' onclick=\"document.getElementById('parent_od_ix').disabled=true;\" checked><label for='depth_1'>1차 원산지 분류</label>
			<input type=radio name='depth' value='2' id='depth_2' onclick=\"document.getElementById('parent_od_ix').disabled=false;\" ><label for='depth_2'>2차 원산지 분류</label>
			".getFirstDIV($db->dt[od_ix])." <span class='small'><!--2차 원산지 분류 등록하기 위해서는 반드시 1차원산지 분류를 선택하셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
		</td>	  
		<td class='input_box_title'> <b>원산지 분류명 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='div_name' value='".$db->dt[div_name]."' style='width:230px;'> <span class=small></span></td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>노출순서 : </b></td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	 
		<td class='input_box_title'> <b>사용유무 : </b></td>
		<td class='input_box_item'>
			<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
			<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
		</td>
	</tr>
	</table>";

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  원산지 분류 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
		<col width='*'>
		<col width=100>
		<col width=150>
		<col width=150>
		<col width=150>
	<tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
		<td class='m_td'> 원산지 분류명</td>
		<td class='m_td'> 노출순서</td>
		<td class='m_td'> 사용유무</td>
		<td class='m_td'> 등록일자</td>
		<td class='m_td'> 관리</td>
	</tr>";

$sql = "select od.* , case when depth = 1 then od_ix  else parent_od_ix end as group_order from common_origin_div od  order by  group_order asc ,depth asc , vieworder asc ";
$db->query($sql);

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		<tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[div_name]."</td>
					</tr>
				</table>
			</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[vieworder]."</td>
			<td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
			<td class='list_box_td '>
				<!--a href=\"javascript:updateGroupInfo('".$db->dt[od_ix]."','".$db->dt[div_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
				<a href=\"?od_ix=".$db->dt[od_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
				<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[od_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
			</td>
		</tr> ";
	}
}else{
	$Contents02 .= "
		<tr bgcolor=#ffffff height=50>
			<td align=center colspan=6>등록된 원산지 분류이 없습니다. </td>
		</tr>";
}

$Contents02 .= "
	</table>";

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";

$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='origin_form' action='origin_div.act.php' method='post' onsubmit='return CheckValue(this)' enctype='multipart/form-data' target='act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='".$act."'>
<input name='od_ix' type='hidden' value='".$od_ix."'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("원산지 분류관리", $help_text);
$Contents = $Contents.$help_text;

$Script = "
<script language='javascript'>

function CheckValue(frm){
	if(frm.depth[1].checked){
		if(frm.parent_od_ix.value == ''){
			alert(language_data['region.php']['A'][language]);
			//'2차 원산지 분류을 등록하기 위해서는 1차원산지 분류을 반드시 선택하셔야 합니다.'
			return false;
		}
	}

	if(frm.div_name.value.length < 1){
		alert(language_data['region.php']['B'][language]);
		//'등록하시고자 하는 상품분류관리 원산지 분류명을 입력해주세요'
		frm.div_name.focus();
		return false;
	}

}

function updateGroupInfo(od_ix,div_name,depth, vieworder,group_order,disp){
	var frm = document.origin_form;

	frm.act.value = 'update';
	frm.od_ix.value = od_ix;
	frm.div_name.value = div_name;
	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

	if(depth == '1'){
		frm.depth[0].checked = true;
		document.getElementById('parent_od_ix').disabled=true;
		document.getElementById('parent_od_ix').options[0].selected='selected';
	}else{
		frm.depth[1].checked = true;
		document.getElementById('parent_od_ix').disabled=false;
		document.getElementById('parent_od_ix').value=group_order;
	}

}

function deleteGroupInfo(act, od_ix){
	if(confirm(language_data['region.php']['C'][language])){//'해당원산지 분류 정보를 정말로 삭제하시겠습니까?'
		var frm = document.origin_form;
		frm.act.value = act;
		frm.od_ix.value = od_ix;
		frm.submit();
	}
}

</script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 상품분류관리 > 원산지 분류관리";
	$P->NaviTitle = "원산지 분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 상품분류관리 > 원산지 분류관리";
	$P->title = "원산지 분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*
CREATE TABLE IF NOT EXISTS `common_origin_div` (
  `od_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `parent_od_ix` int(4) unsigned DEFAULT NULL COMMENT '상위인덱스값',
  `div_name` varchar(20) DEFAULT NULL COMMENT '분류명',
  `depth` int(2) unsigned DEFAULT '1' COMMENT '카테고리depth',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `vieworder` int(8) DEFAULT '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`od_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='원산지 분류정보' 
*/
?>