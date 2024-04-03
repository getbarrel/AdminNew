<?
include("../class/layout.class");
include("./category_main.lib.php");

$db = new Database;

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("분류별 전시위치관리", "메인페이지 전시관리 > 분류별 전시위치관리 ")."</td>
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
								<td class='box_02'  ><a href='category_main_div.php'>분류별 전시그룹 관리</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='category_main_position.php'>분류별 전시위치관리</a></td>
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
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 전시위치 추가</b></div>")."</td>
	  </tr>
	   </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'> 전시그룹</td>
		<td class='input_box_item'>".getCategoryMainDiv("")."</td>
	    <td class='input_box_title'> 전시위치</td>
		<td class='input_box_item'><input type=text class='textbox' name='cmp_name' value='".$db->dt[cmp_name]."' validation='true' title='전시위치' style='width:230px;'> <span class=small></span></td>
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
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  전시위치 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <!--col width=15%-->
		<col width=15%>
		<col width=*>
		<col width='10%'>
	    
	    <col width=10%>
	    <col width=15%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
		<td class='m_td'> 전시그룹</td>
		<td class='m_td'> 전시위치</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

 

$sql = "select cmp.*,cmd.div_name
		from shop_category_main_position cmp left join shop_category_main_div cmd on (cmd.div_ix=cmp.div_ix)
		where 1
		$where  ";//cmp.disp = 1  

$db->query($sql);
 
if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center> 
			<td class='list_box_td' style='padding-left:20px;'>
				".$db->dt[div_name]."
			</td>
			<td class='list_box_td point' style='padding-left:20px;'>
				".$db->dt[cmp_name]."
			</td>
		    <td class='list_box_td '>".$db->dt[vieworder]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용안함")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
		    	<a href=\"javascript:updateCategoryMainPosition('".$db->dt[cmp_ix]."','".$db->dt[div_ix]."','".$db->dt[cmp_name]."','".$db->dt[vieworder]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteCategoryMainPosition('delete','".$db->dt[cmp_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 전시위치 정보가 없습니다. </td>
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
$Contents = $Contents."<form name='cm_position_frm' action='category_main_position.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><!--target=act--><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='cmp_ix' type='hidden' value=''>";
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

	$help_text = HelpBox("분류별 전시위치", $help_text);
$Contents = $Contents.$help_text;

 $Script = " <script language='javascript' src='goods_input.special.js'></script>
 <script language='javascript'>

 function updateCategoryMainPosition(cmp_ix,div_ix,cmp_name,vieworder,disp){
 	var frm = document.cm_position_frm;

 	frm.act.value = 'update';
 	frm.cmp_ix.value = cmp_ix;
 	frm.cmp_name.value = cmp_name;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}
	
	frm.div_ix.value = div_ix;

}

 function deleteCategoryMainPosition(act, cmp_ix){
 	if(confirm('해당 전시위치를 정말로 삭제하시겠습니까?')){
 		var frm = document.cm_position_frm;
 		frm.act.value = act;
 		frm.cmp_ix.value = cmp_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = display_menu();
	$P->Navigation = "전시관리 > 메인페이지 전시관리 > 분류별 전시위치관리";
	$P->title = "분류별 전시위치관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = display_menu();
	$P->Navigation = "상품관리 > 메인페이지 전시관리 > 분류별 전시위치관리";
	$P->title = "분류별 전시위치관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE IF NOT EXISTS `shop_category_main_position` (
  `ccmp_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '키값',
  `cmp_name` varchar(70) DEFAULT NULL COMMENT '전시위치명',
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `vieworder` int(8) DEFAULT '0' COMMENT '노출순서',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ccmp_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='분류별 전시위치'   ;

*/



?>