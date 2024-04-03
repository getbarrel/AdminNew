<? 
include("../class/layout.class");

$db = new Database;

if($div_ix){
	$db->query("SELECT * FROM admin_menu_div where div_ix = '$div_ix' ");
	$db->fetch();
	
	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("메뉴분류관리", "상점관리 > 메뉴분류관리 ")."</td>
	  </tr>	  
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>	 
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width=20%>
	  <col width=*>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>GNB 이름 <span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'><input type=text class='textbox2' name='gnb_name' value='".$db->dt[gnb_name]."' style='width:230px;' validation=true title='GNB 명'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>GNB 구분 <span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'><input type=text class='textbox2' name='div_name' value='".$db->dt[div_name]."' style='width:230px;' validation=true title='분류명'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>기본링크<span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'><input type=text class='textbox2' name='basic_link' value='".$db->dt[basic_link]."' style='width:530px;' validation=true title='기본링크'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>솔루션 타입별 사용권한 <span style='color:red'>(*)</span></b> </td>
	    <td class='input_box_item'>
	    	<input type=checkbox name='gnb_use_home' id='gnb_use_home'  value='Y' ".(($db->dt[gnb_use_home] == "Y") ? "checked":"" )."><label for='gnb_use_home'>홈빌더</label>
			<input type=checkbox name='gnb_use_soho' id='gnb_use_soho'  value='Y' ".(($db->dt[gnb_use_soho] == "Y") ? "checked":"" )."><label for='gnb_use_soho'>소호형</label>
	    	<input type=checkbox name='gnb_use_biz' id='gnb_use_biz' value='Y' ".(($db->dt[gnb_use_biz] == "Y") ? "checked":"" )."><label for='gnb_use_biz'>비지니스형</label>
			<input type=checkbox name='gnb_use_wholesale' id='gnb_use_wholesale' value='Y' ".(($db->dt[gnb_use_wholesale] == "Y") ? "checked":"" )."><label for='gnb_use_wholesale'>도매형</label>
			<input type=checkbox name='gnb_use_openmarket' id='gnb_use_openmarket' value='Y' ".(($db->dt[gnb_use_openmarket] == "Y") ? "checked":"" )."><label for='gnb_use_openmarket'>오픈마켓형(스탠다드)</label>
			<input type=checkbox name='gnb_use_enterprise' id='gnb_use_enterprise' value='Y' ".(($db->dt[gnb_use_enterprise] == "Y") ? "checked":"" )."><label for='gnb_use_enterprise'>오픈마켓형(Enterprise)</label>
	    </td>
	  </tr>	
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서 <img src='".$required_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:60px;' validation='true' title='노출순서'></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무 <span style='color:red'>(*)</span></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' ".(($db->dt[disp] == "" || $db->dt[disp] == 1) ? "checked":"" )."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".(($db->dt[disp] == 0) ? "checked":"" )."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";
	  



$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>	  
	  <tr>
	    <td align='left' colspan=11 style='padding:3px 0px'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  분류 목록</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>	  
	    <col style='width:5%;'>
		<col style='width:9%;'>
		<col style='width:10%;'>
	    <col style='width:6%;'>
		<col style='width:6%;'>
		<col style='width:6%;'>
		<col style='width:6%;'>
		<col style='width:6%;'>
		<col style='width:8%;'>
		<col style='width:8%;'>
		<col style='width:8%;'>
	    <col style='width:10%;'>
	    <col style='width:7%;'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> NO</td>
		<td class='m_td'> GNB 이름</td>
		<td class='m_td'> GNB 구분</td>
	    <td class='m_td'> 노출순서</td>
		<td class='m_td'> 홈빌더</td>
		<td class='m_td'> 소호형</td>
		<td class='m_td'> 비즈형</td>
		<td class='m_td'> 도매형</td>
		<td class='m_td'> 오픈마켓형<br>(스탠다드형)</td>
		<td class='m_td'> 오픈마켓형<br>(Enterprise)</td>
		<td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";





$sql = 	"SELECT * FROM admin_menu_div order by vieworder asc ";


//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>	    
		    <td class='list_box_td list_bg_gray'>".($i+1)."</td>
			<td class='list_box_td ' align=center >".$db->dt[gnb_name]."</td>
			<td class='list_box_td list_bg_gray' align=center ><span style='width:".(30*$db->dt[div_depth])."px;'></span>".$db->dt[div_name]."</td>
			<td class='list_box_td '>".$db->dt[vieworder]."</td>			
		    <td class='list_box_td list_bg_gray'>".($db->dt[gnb_use_home] == "Y" ?  "사용":"사용안함")."</td>
			<td class='list_box_td '>".($db->dt[gnb_use_soho] == "Y" ?  "사용":"사용안함")."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[gnb_use_biz] == "Y" ?  "사용":"사용안함")."</td>
			<td class='list_box_td '>".($db->dt[gnb_use_wholesale] == "Y" ?  "사용":"사용안함")."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[gnb_use_openmarket] == "Y" ?  "사용":"사용안함")."</td>
			<td class='list_box_td list_bg_gray'>".($db->dt[gnb_use_enterprise] == "Y" ?  "사용":"사용안함")."</td>
			<td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
			
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td ' >
		    	<!--a href=\"javascript:updateBankInfo('".$db->dt[div_ix]."','".$db->dt[div_name]."','".$db->dt[div_bbs_cnt]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo['language']."/btc_modify.gif' border=0></a-->
		    	<a href=\"?div_ix=".$db->dt[div_ix]."\"><img src='../images/".$admininfo['language']."/btc_modify.gif' border=0></a>  
		    	
	    		<a href=\"javascript:deleteBankInfo('delete','".$db->dt[div_ix]."')\"><img src='../images/".$admininfo['language']."/btc_del.gif' border=0></a>
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=9>등록된 분류가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "</table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo['language']."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='menu_div.act.php' method='post' onsubmit='return CheckFormValue(this)'><input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>						
						<input name='div_ix' type='hidden' value='$div_ix'>";
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

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>		
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>분류명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >분류의 노출을 원하지 않으시면 사용안함으로 설정 하시면 됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 분류는 프로모션 상품 관리에서도 노출 되지 않습니다.</td></tr>
	</table>
	";

	
	$help_text = HelpBox("메뉴분류관리", $help_text);				
$Contents = $Contents.$help_text;	

 $Script = "
 <script language='javascript'>
 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;
 	
 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
 
}
 
 function deleteBankInfo(act, div_ix){
 	if(confirm('해당카테고리  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.div_form; 	
 		frm.act.value = act;
 		frm.div_ix.value = div_ix;
 		frm.submit();
 	}	
}
 </script>
 ";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->Navigation = "상점관리 > 메뉴분류관리";
	$P->NaviTitle = "메뉴분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->Navigation = "상점관리 > 메뉴분류관리";
	$P->title = "메뉴분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getFirstDIV($bm_ix, $selected=""){
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM admin_menu_div
			where disp=1 ";
	
	$mdb->query($sql);
	
	$mstring = "<select name='parent_div_ix' id='parent_div_ix' disabled>";
	$mstring .= "<option value=''>1차분류</option>";
	if($mdb->total){
		
		
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}
		
	}	
	$mstring .= "</select>";
	
	return $mstring;
}

/*

CREATE TABLE IF NOT EXISTS `admin_menu_div` (
  `div_ix` int(4) unsigned NOT NULL auto_increment,
  `div_name` varchar(20) default NULL,
  `disp` char(1) default '1',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`div_ix`),
  KEY `disp` (`disp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8
*/
?>