<?
include("../class/layout.class");
include_once("car.lib.php");

$db = new Database;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("자동차제조사", "스페셜카테고리 > 자동차제조사 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	 ".car_tab("manufacturer")."
	    </td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 자동차제조사 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 자동차/오토바이 구분 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='vechile_div' id='vechile_div_c' value='C' checked><label for='vechile_div_c'>자동차</label>
	    	<input type=radio name='vechile_div' id='vechile_div_b' value='B' ><label for='vechile_div_b'>오토바이</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 자동차 제조사명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='manufacturer_name' value='".$db->dt[manufacturer_name]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 노출순서 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 자동차제조사명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  자동차제조사 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  자동차제조사 목록</b></div>")."</div>";

$Contents02 .= "".VechileDiv($vechile_div)."
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=130>
		<col width='*'>
	    <col width=100>
	    <col width=100>
	    <col width=150>
	    <col width=150>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 구분</td>
		<td class='m_td'> 자동차 제조사명</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

if($vechile_div){
	$where = " where vechile_div = '".$vechile_div."' ";
}

$sql = "select mf.* , case when depth = 1 then mf_ix  else parent_mf_ix end as group_order
		from shop_car_manufacturer mf $where
		order by  group_order asc ,depth asc , vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then mf_ix  else parent_mf_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.mf_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by mf_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>

			<td class='list_box_td list_bg_gray'>".($db->dt[vechile_div] == "C" ?  "자동차":"오토바이")."</td>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[manufacturer_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<a href=\"javascript:updateManufacturer('".$db->dt[mf_ix]."','".$db->dt[vechile_div]."','".$db->dt[manufacturer_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteManufacturer('delete','".$db->dt[mf_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 자동차 제조사가 없습니다. </td>
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
$Contents = $Contents."<form name='manufacturer_form' action='manufacturer.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='mf_ix' type='hidden' value=''>";
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
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >스페셜카테고리 자동차제조사 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>자동차제조사명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("자동차제조사", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

 function updateManufacturer(mf_ix,vechile_div, manufacturer_name,depth, vieworder,group_order,disp){
 	var frm = document.manufacturer_form;

 	frm.act.value = 'update';
 	frm.mf_ix.value = mf_ix;
 	frm.manufacturer_name.value = manufacturer_name;
 	frm.vieworder.value = vieworder;
	if(vechile_div=='C') {
		frm.vechile_div[0].checked = true;
	} else {
		frm.vechile_div[1].checked = true;
	}

	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

 	if(depth == '1'){
 		frm.depth[0].checked = true;
		document.getElementById('parent_mf_ix').disabled=true;
		document.getElementById('parent_mf_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		document.getElementById('parent_mf_ix').disabled=false;
		document.getElementById('parent_mf_ix').value=group_order;
 	}

}

 function deleteManufacturer(act, mf_ix){
 	if(confirm(language_data['manufacturer.php']['A'][language])){//'해당 자동차 제조사  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.manufacturer_form;
 		frm.act.value = act;
 		frm.mf_ix.value = mf_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 스페셜카테고리 > 자동차";
	$P->title = "자동차";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 스페셜카테고리 > 자동차";
	$P->title = "자동차";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `shop_address_group` (
  `mf_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_mf_ix` int(4) unsigned DEFAULT NULL,
  `manufacturer_name` varchar(20) DEFAULT NULL,
  `depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`mf_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>