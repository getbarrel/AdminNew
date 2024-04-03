<?
include("../class/layout.class");

$db = new Database;

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("카드정보관리", "카드별할인/무이자관리 > 카드정보관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 카드정보 추가/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 카드명 : <img src='".$required3_path."'></td>
		<td class='input_box_item'><input type=text class='textbox' name='card_name' value='' style='width:230px;'> </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 결제모듈카드key : </td>
		<td class='input_box_item'><input type=text class='textbox' name='card_code' value='' style='width:230px;'> <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents02 = "
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> ".$sattle_module[$admininfo[sattle_module]]." 카드정보 목록</b> <!--span class=small> &nbsp;&nbsp;* 현재 고객님꼐서 사용하고 계신 결제 모듈은 <b>".$sattle_module[$admininfo[sattle_module]]."</b> 입니다.</span--></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=10%>
		<col width='*'>
		<col width=25%>
		<col width=15%>
		<col width=15%>
		<col width=15%>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 번호</td>
		<td class='m_td'> 카드명</td>
	    <td class='m_td'> 결제모듈카드key</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

$sql = "select * from shop_card_info where sattle_module='".$admininfo[sattle_module]."' ";

$db->query($sql);

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td list_bg_gray'>".($i+1)."</td>
			<td class='list_box_td point' style='padding-left:20px;'>".$db->dt[card_name]."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[card_code]."</td>
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<a href=\"javascript:updatecardinfo('".$db->dt[ci_ix]."','".$db->dt[card_name]."','".$db->dt[card_code]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deletecardinfo('card_delete','".$db->dt[ci_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 카드정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<form name='cardinfo_form' action='card_promotion.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='card_insert'>
<input name='card_sattle_module' type='hidden' value='".$admininfo[sattle_module]."'>
<input name='ci_ix' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카드별할인/무이자프로모션 카드정보관리 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>카드정보관리명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("카드정보관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

 function updatecardinfo(ci_ix,card_name,card_code,disp){

 	var frm = document.cardinfo_form;

 	frm.act.value = 'card_update';
 	frm.ci_ix.value = ci_ix;
 	frm.card_name.value = card_name;
 	frm.card_code.value = card_code;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

}

 function deletecardinfo(act, ci_ix){
 	if(confirm('해당카드 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.cardinfo_form;
 		frm.act.value = act;
 		frm.ci_ix.value = ci_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = display_menu();
	$P->Navigation = "프로모션/전시 > 카드별할인/무이자관리 > 카드정보관리";
	$P->title = "카드정보관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = display_menu();
	$P->Navigation = "프로모션/전시 > 카드별할인/무이자관리 > 카드정보관리";
	$P->title = "카드정보관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*


CREATE TABLE IF NOT EXISTS `shop_card_info` (
  `ci_ix` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `sattle_module` varchar(255) NOT NULL COMMENT '결제모듈',
  `card_name` varchar(255) NOT NULL COMMENT '카드이름',
  `card_code` varchar(255) NOT NULL COMMENT '결제모듈에카드key',
  `disp` char(1) DEFAULT NULL COMMENT '사용여부',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ci_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='카드정보' AUTO_INCREMENT=1 ;


*/
?>