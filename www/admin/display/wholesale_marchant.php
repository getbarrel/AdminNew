<?
include("../class/layout.class");

$db = new Database;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("사입관리", "도매처관리 ")."</td>
	  </tr>
	  
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 상권 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'>
	  <col width='30%'>
	  <col width='20%'>
	  <col width='30%'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상권코드 </td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox' name='ca_code' value='".$db->dt[ca_code]."' style='width:230px;'>
	    </td>	 
	    <td class='input_box_title'> 상권명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='ca_name' value='".$db->dt[ca_name]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 노출순서 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	  
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
		  <u>$board_name </u> 에 이용할 도매처관리제조사명을  입력해주세요
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
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u>  상권 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=10%>
		<col width='10%'>
	    <col width=*>
	    <col width=10%>
	    <col width=10%>
	    <col width=20%>
		<col width=15%>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 구분</td>
		<td class='m_td'> 상권코드</td>
		<td class='m_td'> 상권명</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

if($ca_code){
	$where = " where ca_code = '".$ca_code."' ";
}

$sql = "select ca.* , case when depth = 1 then ca_ix  else parent_ca_ix end as group_order
		from buyingservice_commercial_area ca $where
		order by  group_order asc ,depth asc , vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then ca_ix  else parent_ca_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.ca_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by ca_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/
$total = $db->total;

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>

			<td class='list_box_td list_bg_gray'>".($no)."</td>
			<td class='list_box_td'><b>".($db->dt[ca_code])."</b></td>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[ca_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<a href=\"javascript:updateManufacturer('".$db->dt[ca_ix]."','".$db->dt[ca_code]."','".$db->dt[ca_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteManufacturer('delete','".$db->dt[ca_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 상권정보가 없습니다. </td>
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
$Contents = $Contents."<form name='manufacturer_form' action='commercial_area.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='ca_ix' type='hidden' value=''>";
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

	$help_text = HelpBox("<div style='padding-top:6px;'>자동차제조사</div>", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

 function updateManufacturer(ca_ix,ca_code, ca_name,depth, vieworder,group_order,disp){
 	var frm = document.manufacturer_form;

 	frm.act.value = 'update';
 	frm.ca_ix.value = ca_ix;
 	frm.ca_name.value = ca_name;
	frm.ca_code.value = ca_code;
 	frm.vieworder.value = vieworder;
	

	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

 	if(depth == '1'){
 		frm.depth[0].checked = true;
		document.getElementById('parent_ca_ix').disabled=true;
		document.getElementById('parent_ca_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		document.getElementById('parent_ca_ix').disabled=false;
		document.getElementById('parent_ca_ix').value=group_order;
 	}

}

 function deleteManufacturer(act, ca_ix){
 	if(confirm(language_data['manufacturer.php']['A'][language])){//'해당 자동차 제조사  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.manufacturer_form;
 		frm.act.value = act;
 		frm.ca_ix.value = ca_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = buyingservice_menu();
	$P->Navigation = "사입관리 > 도매처관리";
	$P->title = "도매처관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = buyingservice_menu();
	$P->Navigation = "사입관리 > 도매처관리";
	$P->title = "도매처관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `shop_address_group` (
  `ca_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_ca_ix` int(4) unsigned DEFAULT NULL,
  `ca_name` varchar(20) DEFAULT NULL,
  `depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ca_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>