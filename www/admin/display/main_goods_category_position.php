<?
include("../class/layout.class");
//include_once("car.lib.php");
if(!$agent_type){
	$agent_type = "W";
}
$db = new Database;

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("메인전시 그룹 위치관리", "메인페이지 전시관리 > 메인전시 그룹 위치관리 ")."</td>
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
								<td class='box_02'  ><a href='main_goods_category.php'>메인전시 그룹관리</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='main_goods_category_position.php'>메인전시 그룹 위치관리</a></td>
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
	    <td class='input_box_title'> 전시분류</td>
		<td class='input_box_item'>".getMainGroupSelect("div_ix",$db->dt[div_ix],"validation=true title='전시분류'")."</td>
	 
	    <td class='input_box_title'> 배너위치</td>
		<td class='input_box_item'><input type=text class='textbox' name='mp_name' value='".$db->dt[mp_name]."' validation='true' title='자동차 등급명' style='width:230px;'> <span class=small></span></td>
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
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  배너 위치 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width=15%>
		<col width=15%>
		<col width='15%'>
	    <col width=*>
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


$sql = "select md.* , bp.*
		from shop_main_div md, shop_main_position bp 
		where md.disp = 1 and md.div_ix = bp.div_ix and md.agent_type = '".$agent_type."' $where  ";
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
			<td class='list_box_td '>".$db->dt[div_name]."</td>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[mp_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td '>".$db->dt[vieworder]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
		    	<a href=\"javascript:updateMainGroupPosition('".$db->dt[mp_ix]."','".$db->dt[div_ix]."','".$db->dt[mp_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteBannerPosition('delete','".$db->dt[mp_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 ".($agent_type == "M" ? "모바일":"")." 전시 위치 정보가 없습니다. </td>
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
$Contents = $Contents."<form name='position_form' action='../display/main_goods_category_position.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><!--target=act--><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='mp_ix' type='hidden' value=''><input name='agent_type' type='hidden' value='".$agent_type."'>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >메인페이지 전시관리 자동차 등급 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>자동차 등급명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("배너 위치", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

 function updateMainGroupPosition(mp_ix,md_ix,mp_name,depth, vieworder,group_order,disp){
 	var frm = document.position_form;

 	frm.act.value = 'update';
 	frm.mp_ix.value = mp_ix;
 	frm.mp_name.value = mp_name;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}
 
 
	$('select#div_ix').val(md_ix);
 

}

 function deleteBannerPosition(act, mp_ix){
 	if(confirm(language_data['vechile_grade.php']['A'][language])){//'해당자동차 등급  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.position_form;
 		frm.act.value = act;
 		frm.mp_ix.value = mp_ix;
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
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = "전시관리 > 메인페이지 전시관리 > 메인전시 그룹 위치관리";
		$P->title = "메인전시 그룹 위치관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = "상품관리 > 메인페이지 전시관리 > 메인전시 그룹 위치관리";
		$P->title = "메인전시 그룹 위치관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}

/*

CREATE TABLE IF NOT EXISTS `shop_main_position` (
  `mp_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `div_ix` int(4) unsigned DEFAULT NULL COMMENT '배너분류키값',
  `mp_name` varchar(70) DEFAULT NULL COMMENT '배너위치명',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `vieworder` int(8) DEFAULT '0' COMMENT '노출순서',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`mp_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='배너위치'   ;

*/



function getMainGroupSelect($select_name,$div_ix='', $property=''){
	global $admininfo, $agent_type;

	$mdb = new Database;

	$sql = "SELECT * FROM shop_main_div where disp=1 and agent_type = '".$agent_type."'  ";
	
	$mdb->query($sql);

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	$mstring .= "<option value=''>메인전시 그룹 선택</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[div_ix]."' ".($mdb->dt[div_ix] == $div_ix ? "selected":"").">".$mdb->dt[div_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}


?>