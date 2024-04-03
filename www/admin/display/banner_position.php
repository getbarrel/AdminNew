<?
include("../class/layout.class");

if(!$agent_type){
	$agent_type = "W";
}
$db = new Database;

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("배너 위치관리", "배너관리 > 배너 위치관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>

							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='banner_category.php'>배너 분류관리</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='banner_position.php'>배너 위치관리</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
	    </td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 배너 위치 추가</b></div>")."</td>
	  </tr>
	   </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 배너분류</td>
		<td class='input_box_item'>".getBannerCategorySelect("div_ix",$db->dt[div_ix],"validation=true title='배너분류'")."</td>
	   
	    <td class='input_box_title'> 배너위치</td>
		<td class='input_box_item'><input type=text class='textbox' name='bp_name' value='".$db->dt[bp_name]."' validation='true' title='배너위치' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 노출순서 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' validation='true' title='노출순서' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	   
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";
 
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$Contents02 = "
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  배너 위치 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width=15%>
		<col width=*>
		<col width='10%'>
	    <col width=10%>
	    <col width=15%>
	    <col width=15%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 배너분류</td>
		<td class='m_td'> 배너위치</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";


if($vechile_div){
	$where = " and mg.vechile_div = '".$vechile_div."' ";
}


$sql = "select bd.* , bp.*
		from shop_banner_div bd, shop_banner_position bp 
		where bd.disp = 1 and bd.div_ix = bp.div_ix and bd.agent_type = '".$agent_type."'
		$where  ";
$db->query($sql);
 
if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td '>".$db->dt[div_name]."</td>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[bp_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td '>".$db->dt[vieworder]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "
		    	<a href=\"javascript:updateBannerPosition('".$db->dt[bp_ix]."','".$db->dt[div_ix]."','".$db->dt[bp_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents02 .= "
	    		<a href=\"javascript:deleteBannerPosition('delete','".$db->dt[bp_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
				$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 배너위치 정보가 없습니다. </td>
		  </tr> ";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='grade_form' action='../display/banner_position.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><!--target=act--><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='bp_ix' type='hidden' value=''><input name='agent_type' type='hidden' value='$agent_type'>";
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

	$help_text = HelpBox("배너 위치", $help_text);
$Contents = $Contents.$help_text;

 $Script = " <script language='javascript' src='goods_input.special.js'></script>
 <script language='javascript'>

 function updateBannerPosition(bp_ix,div_ix,bp_name,depth, vieworder,group_order,disp){
 	var frm = document.grade_form;

 	frm.act.value = 'update';
 	frm.bp_ix.value = bp_ix;
 	frm.bp_name.value = bp_name;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}
 
	$('#div_ix').val(div_ix);

	/*
 	if(depth == '1'){
 		frm.depth[0].checked = true;
		document.getElementById('parent_bp_ix').disabled=true;
		document.getElementById('parent_bp_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		document.getElementById('parent_bp_ix').disabled=false;
		document.getElementById('parent_bp_ix').value=group_order;
 	}
	*/

}

 function deleteBannerPosition(act, bp_ix){
 	if(confirm('배너 위치정보를 정말로 삭제하시겠습니까? ')){
 		var frm = document.grade_form;
 		frm.act.value = act;
 		frm.bp_ix.value = bp_ix;
 		frm.submit();
 	}
}
 </script>
 ";
if($agent_type == "M"  || $agent_type == "mobile"){
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = $navigation;
		$P->NaviTitle = $title;
		$P->strContents = $Contents;
		echo $P->PrintLayOut();	
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{
	if($mmode == "pop"  || $agent_type == "mobile"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = "전시관리 > 배너관리 > 배너 위치관리";
		$P->title = "배너 위치관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = "상품관리 > 배너관리 > 배너 위치관리";
		$P->title = "배너 위치관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}

/*

CREATE TABLE IF NOT EXISTS `shop_banner_position` (
  `bp_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `div_ix` int(4) unsigned DEFAULT NULL COMMENT '배너분류키값',
  `bp_name` varchar(20) DEFAULT NULL COMMENT '배너위치명',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `vieworder` int(8) DEFAULT '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`bp_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='배너위치'  ;
*/



function getBannerCategorySelect($select_name,$div_ix='', $property=''){
	global $admininfo;
	global $agent_type;

	$mdb = new Database;

	$sql = "SELECT * FROM shop_banner_div where disp=1 and agent_type = '".$agent_type."' ";
	//echo $sql ;
	$mdb->query($sql);

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	$mstring .= "<option value=''>배너분류 선택</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[div_ix]."' ".($mdb->dt[div_ix] == $div_ix ? "selected":"").">".$mdb->dt[div_name]."</option>";
		}
	}
	$mstring .= "</select>";

	return $mstring;
}


?>