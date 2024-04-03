<?
include("../class/layout.class");
include("buying.lib.php");

$db = new Database;
$db = new Database;
$sql = "SELECT * FROM buyingservice_wholesaler
			  where ws_ix = '".$ws_ix."'  ";
$db->query($sql);
if($db->total){
	$db->fetch();
	$wholesaler_info = $db->dt;
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("사입관리", "도매처관리 ")."</td>
	  </tr>
	  
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 도매처 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15%'>
	  <col width='35%'>	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가 </td>
	    <td class='input_box_item'>
	    	".getShoppingCenter($wholesaler_info[sc_ix], "select"," onchange=\"loadShoppingCenterInfo(this, 'floor')\" validation='true' ")."
	    </td>	 
	    <td class='input_box_title'> 층 / 라인 / 호 </td>
		<td class='input_box_item'>
		<table>
			<tr>
				<td>".getShoppingCenterFloorInfo($wholesaler_info[sc_ix], $wholesaler_info[floor], "select", "validation='true'")."</td>
				<td>".getShoppingCenterLineInfo($wholesaler_info[sc_ix], $wholesaler_info[line], "select", "validation='true'")."</td>
				<td>".getShoppingCenterNoInfo($wholesaler_info[sc_ix], $wholesaler_info[no], "select", "validation='true'")."</td>
			</tr>
		</table>
		</td>
		
	  </tr>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'> 도매처명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='ws_name' value='".$wholesaler_info[ws_name]."' style='width:90%;'> <span class=small></span></td>
	    <td class='input_box_title'> 연락처 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='ws_tel' value='".$wholesaler_info[ws_tel]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr>
		<td class='input_box_title'> 이메일 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='ws_email' value='".$wholesaler_info[ws_email]."' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_y' value='Y' ".($wholesaler_info[disp] == "Y" || $wholesaler_info[disp] == "" ? "checked":"")." ><label for='disp_y'>사용</label>
	    	<input type=radio name='disp' id='disp_n' value='N' ".($wholesaler_info[disp] == "N"  ? "":"")."><label for='disp_n'>사용하지않음</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
				<td class='input_box_title'> 사입자 정보 </td>
				<td class='input_box_item' colspan=3>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=hidden name='mem_ix' id='mem_ix' value=''".$buyingservice_apply_info[mem_ix]."'' style='width:100px;'></td>
							<td ><input type=text class='textbox' id='buying_mem_name' name='buying_mem_name' value='".$buyingservice_apply_info[buying_mem_name]."' style='width:100px;' onclick=\"PoPWindow('./member_search.php?code=".$db->dt[code]."',600,380,'member_search')\" readonly></td>
							<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_member_search.gif' align=absmiddle onclick=\"PoPWindow('./member_search.php?code=".$db->dt[code]."',600,380,'member_search')\"  style='cursor:pointer;'></td>
						</tr>
					</table>
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
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u>  도매처관리 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=5%>
		<col width=*>
		<col width='10%'>	    
	    <col width=13%>
		<col width=5%>
		<col width=5%>
		<col width=5%>
	    <col width=10%>
	    <col width=15%>
		<col width=10%>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 구분</td>
		<td class='m_td'> 도매처명</td>
	    <td class='m_td'> 전화번호</td>
	    <td class='m_td'> 이메일</td>
		<td class='m_td'> 층</td>
	    <td class='m_td'> 라인</td>
		<td class='m_td'> 호</td>
		<td class='m_td'> 사용여부</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

if($ws_code){
	$where = " where ws_code = '".$ws_code."' ";
}

$sql = "select ws.* 
		from buyingservice_wholesaler ws $where
		order by  regdate desc ";
$db->query($sql);

$total = $db->total;

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>

			<td class='list_box_td list_bg_gray'>".($no)."</td>
			<td class='list_box_td point' >
				".$db->dt[ws_name]."
			</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[ws_tel]."</td>
			<td class='list_box_td '>".$db->dt[ws_email]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[floor]."</td>
			<td class='list_box_td '>".$db->dt[line]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[no]."</td>
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<!--a href=\"javascript:updateWholeSaler('".$db->dt[ws_ix]."','".$db->dt[ws_code]."','".$db->dt[ws_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
				<a href=\"?ws_ix=".$db->dt[ws_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>

	    		<a href=\"javascript:deleteWholeSaler('delete','".$db->dt[ws_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=10>등록된 도매처 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff >
	<td colspan=4 align=center>
		<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
		<a href='?'><img src='../images/".$admininfo["language"]."/btn_add_new.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle valign='top'></a>
		</td>
</tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='manufacturer_form' action='wholesaler.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='".$act ."'><input name='ws_ix' type='hidden' value='".$ws_ix."'>";
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

	$help_text = HelpBox("<div style='padding-top:6px;'>도매처</div>", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
function loadShoppingCenterInfo(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	//var depth = sel.getAttribute('depth');
	//document.write('shopping_center_info.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = 'shopping_center_info.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';


}

 function updateWholeSaler(ws_ix,ws_code, ws_name,depth, vieworder,group_order,disp){
 	var frm = document.manufacturer_form;

 	frm.act.value = 'update';
 	frm.ws_ix.value = ws_ix;
 	frm.ws_name.value = ws_name;
	frm.ws_code.value = ws_code;
 	frm.vieworder.value = vieworder;
	

	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

 	if(depth == '1'){
 		frm.depth[0].checked = true;
		document.getElementById('parent_ws_ix').disabled=true;
		document.getElementById('parent_ws_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		document.getElementById('parent_ws_ix').disabled=false;
		document.getElementById('parent_ws_ix').value=group_order;
 	}

}

 function deleteWholeSaler(act, ws_ix){
 	if(confirm(language_data['manufacturer.php']['A'][language])){//'해당 자동차 제조사  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.manufacturer_form;
 		frm.act.value = act;
 		frm.ws_ix.value = ws_ix;
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

CREATE TABLE IF NOT EXISTS buyingservice_wholesaler (
  `ws_ix` int(6) NOT NULL auto_increment COMMENT '상가정보 관리키',
  `sc_ix` varchar(5) NOT NULL COMMENT '상가코드',
  `mem_ix` varchar(32) NOT NULL COMMENT '회원코드',
  `ws_name` varchar(100) default NULL COMMENT '도매처명',
  `ws_tel` varchar(13) default NULL COMMENT '전화번호',
  `ws_fax` varchar(13) default NULL COMMENT '팩스번호', 
  `ws_email` varchar(100) default NULL COMMENT '이메일',
  `floor` varchar(5) NOT NULL default '' COMMENT '층',
  `line` varchar(5) NOT NULL default '' COMMENT '라인',
  `no` varchar(5) NOT NULL default '' COMMENT '호수',
  `homepage` VARCHAR( 100 ) NOT NULL COMMENT '홈페이지 URL' ,
  `ws_zip` VARCHAR( 20 ) NOT NULL COMMENT '도매처 우편번호' ,
  `ws_addr1` VARCHAR( 100 ) NOT NULL COMMENT '도매처 주소',
  `ws_addr2` VARCHAR( 100 ) NOT NULL COMMENT '도매처주소 상세',
  `ws_msg` mediumtext COMMENT '설명', 
  `disp` enum('Y','N') default 'Y' COMMENT '사용여부',
  `regdate` datetime default NULL COMMENT '등록일',
  PRIMARY KEY  (`ws_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='도매처 정보'  ;

*/
?>