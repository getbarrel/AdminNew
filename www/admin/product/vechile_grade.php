<?
include("../class/layout.class");
include_once("car.lib.php");

$db = new Database;

if(!$vechile_div){
	//$vechile_div = "C";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("자동차 등급", "스페셜카테고리 > 자동차 등급 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	".car_tab("vechile_grade")."
	    </td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 자동차 등급 추가</b></div>")."</td>
	  </tr>
	   </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 자동차/오토바이 구분 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='vechile_div' id='vechile_div_c' value='C' onclick=\"ChangeModel('모델 선택',this.value)\" checked><label for='vechile_div_c'>자동차</label>
	    	<input type=radio name='vechile_div' id='vechile_div_b' value='B' onclick=\"ChangeModel('모델 선택',this.value)\" ><label for='vechile_div_b'>오토바이</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 자동차 모델 : </td>
		<td class='input_box_item'>".makeModelSelectBox($vechile_div, "md_ix",$db->dt[md_ix],"all","모델 선택","style='width:110px;font-size:11px;' validation='true' title='자동차 모델'")."</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 자동차 등급명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='grade_name' value='".$db->dt[grade_name]."' validation='true' title='자동차 등급명' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 노출순서 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' validation='true' title='노출순서' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
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
		  <u>$board_name </u> 에 이용할 자동차 등급명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  자동차 등급 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  자동차 등급 목록</b></div>")."</div>";

$Contents02 .= "
	".VechileDiv($vechile_div)."
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width=100>
		<col width=100>
		<col width='*'>
	    <col width=90>
	    <col width=90>
	    <col width=150>
	    <col width=150>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 구분</td>
		<td class='m_td'> 모델</td>
		<td class='m_td'> 자동차 등급명</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";


if($vechile_div){
	$where = " and mg.vechile_div = '".$vechile_div."' ";
}


$sql = "select mg.* , md.model_name,  case when mg.depth = 1 then mg.gr_ix  else mg.parent_gr_ix end as group_order
		from shop_car_grade mg
		left join shop_car_model md on mg.md_ix = md.md_ix
		where mg.disp = 1 $where
		order by  mg.vieworder asc ,mg.depth asc , mg.vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then md_ix  else parent_md_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.md_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by md_ix
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
			<td class='list_box_td '>".$db->dt[model_name]."</td>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[grade_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td '>".$db->dt[vieworder]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
		    	<a href=\"javascript:updateModel('".$db->dt[gr_ix]."','".$db->dt[vechile_div]."','".$db->dt[md_ix]."','".$db->dt[grade_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteModel('delete','".$db->dt[gr_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 자동차 등급가 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=7 class='td_underline'></td></tr>	  ";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='grade_form' action='vechile_grade.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><!--target=act--><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='gr_ix' type='hidden' value=''>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >스페셜카테고리 자동차 등급 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>자동차 등급명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("자동차 등급", $help_text);
$Contents = $Contents.$help_text;

 $Script = " <script language='javascript' src='goods_input.special.js'></script>
 <script language='javascript'>

 function updateModel(gr_ix,vechile_div,md_ix,grade_name,depth, vieworder,group_order,disp){
 	var frm = document.grade_form;

 	frm.act.value = 'update';
 	frm.gr_ix.value = gr_ix;
 	frm.grade_name.value = grade_name;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

	if(vechile_div=='C') {
		frm.vechile_div[0].checked = true;
	} else {
		frm.vechile_div[1].checked = true;
	}

	ChangeModel('모델 선택',vechile_div, md_ix);
	$('#md_ix option:eq('+md_ix+')').attr('selected', 'selected');

	/*
 	if(depth == '1'){
 		frm.depth[0].checked = true;
		document.getElementById('parent_gr_ix').disabled=true;
		document.getElementById('parent_gr_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		document.getElementById('parent_gr_ix').disabled=false;
		document.getElementById('parent_gr_ix').value=group_order;
 	}
	*/

}

 function deleteModel(act, gr_ix){
 	if(confirm(language_data['vechile_grade.php']['A'][language])){//'해당자동차 등급  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.grade_form;
 		frm.act.value = act;
 		frm.gr_ix.value = gr_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 스페셜카테고리 > 자동차/오토바이 등급";
	$P->title = "자동차/오토바이 등급";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 스페셜카테고리 > 자동차/오토바이 등급";
	$P->title = "자동차/오토바이 등급";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `shop_address_group` (
  `gr_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_gr_ix` int(4) unsigned DEFAULT NULL,
  `grade_name` varchar(20) DEFAULT NULL,
  `depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`gr_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>