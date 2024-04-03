<?
include("../class/layout.class");

$db = new Database;

if($dt_ix){

	$db->query("SELECT * FROM inventory_delivery_type where dt_ix = '$dt_ix' ");

	$db->fetch();

	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("출고타입 관리", "재고관리 > 기본정보관리 > 출고형태 관리")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'><u>$board_name</u> 출고타입 추가</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	  <col width='20%'>
	  <col width='*'>
	  <tr height='30' >
	    <td class='search_box_title' > <b>출고타입 <img src='".$required3_path."'></b></td>
		<td class='search_box_item'><input type=text class='textbox' name='delivery_type' value='".$db->dt[delivery_type]."' style='width:230px;' validation='true' title='출고타입' ".($db->dt[is_basic] == "1" ?  "onclick=\"alert('기본 출고타입은 수정 하실 수 없습니다.');\" readonly":"")."> <span class=small></span></td>
	  </tr>
	  <tr height='30' >
	    <td class='search_box_title' > <b>출고타입코드 <img src='".$required3_path."'></b></td>
		<td class='search_box_item'><input type=text class='textbox' name='delivery_type_code' value='".$db->dt[delivery_type_code]."' style='width:230px;' validation='true' title='출고타입코드' ".($db->dt[is_basic] == "1" ?  "onclick=\"alert('기본 출고타입은 수정 하실 수 없습니다.');\" readonly":"")."> <span class=small></span></td>
	  </tr>
	  <tr height='30' >
	    <td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b>  </td>
	    <td class='search_box_item'>";
			if($db->dt[is_basic] == "1"){
				$Contents01 .= "<b>기본출고형태</b> 는 수정 하실 수 없습니다 ";
			}else{
			$Contents01 .= "
	    	<input type=radio name='disp' id='disp_1' value='1' ".(($db->dt[disp] == "" || $db->dt[disp] == 1) ? "checked":"" )."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0'  value='0' ".(($db->dt[disp] == 0) ? "checked":"" )."><label for='disp_0'>사용하지않음</label>";
			}
			$Contents01 .= "
	    </td>
	  </tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <!--그룹명을  입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' )."
	</td>
</tr>
</table>
";

if($db->dt[is_basic] != "1"){
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}else{
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick=\"alert('기본 출고타입은 수정 하실 수 없습니다.');\" ></td></tr>
	</table>
	";

}

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'><u>$board_name</u>  그룹 목록</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0'  class='list_table_box'>
	  <tr height=25 align=center style='font-weight:bold'>
	    <td class='s_td'>출고타입</td>
	    <td class='m_td'>출고타입 코드</td>
		<td class='m_td'>기본타입</td>
		<td class='m_td'>사용유무</td>
	    <td class='m_td'>등록일자</td>
	    <td class='e_td'>관리</td>
	  </tr>";




if($admininfo[charger_id] == "forbiz"){
	$sql = 	"SELECT * FROM inventory_delivery_type order by is_basic , regdate";
}else{
	$sql = 	"SELECT * FROM inventory_delivery_type where disp = 1 order by is_basic , regdate ";
}

//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td point' align=left ><span style='width:".(30*$db->dt[div_depth])."px;'></span>".$db->dt[delivery_type]."</td>
			<td class='list_box_td list_bg_gray' align=left >".$db->dt[delivery_type_code]."</td>
		    <td class='list_box_td'>".($db->dt[is_basic] == "1" ?  "기본":"사용자추가")."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>";
			
			if($db->dt[is_basic] == "1"){
				//$Contents02 .= "기본출고형태";
				$Contents02 .= "<a href=\"?dt_ix=".$db->dt[dt_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"?dt_ix=".$db->dt[dt_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
			}
				$Contents02 .= "
	    		<!-- <a href=\"javascript:deleteDeliveryTypeInfo('delete','".$db->dt[dt_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a> -->
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td class='list_box_td' align=center colspan=4>등록된 출고타입 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "


	  </table>";




$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='delivery_type.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'><input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>
						<input name='dt_ix' type='hidden' value='$dt_ix'>";
$Contents = $Contents."<tr><td>";
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

/*$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>그룹명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >그룹의 노출을 원하지 않으시면 사용 안함으로 판정 하시면 됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 그룹는 게시판 설정에서 또한 노출 되지 않는다.</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

	$help_text = HelpBox("출고타입 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>


 function deleteDeliveryTypeInfo(act, dt_ix){
 	if(confirm('해당 출고타입  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.div_form;
 		frm.act.value = act;
 		frm.dt_ix.value = dt_ix;
		frm.target='act';
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 기초정보 관리 > 출고타입 관리";
	$P->title = "출고타입 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 출고타입 관리";
	$P->title = "출고타입 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
/*

CREATE TABLE IF NOT EXISTS inventory_delivery_type (
  `dt_ix` int(4) unsigned NOT NULL auto_increment COMMENT '출고형태 키',
  delivery_type varchar(20) default NULL COMMENT '출고형태',
  `disp` char(1) default '1' COMMENT '사용여부',
  `is_basic` enum('1','0') NOT NULL default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`dt_ix`),
  KEY `IDX_MBG_DIV_NAME` (delivery_type),
  KEY `IDX_MBG_DISP` (`disp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='출고형태 정보'  ;


*/

?>