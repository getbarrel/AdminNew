<? 
include("../class/layout.class");

$db = new Database;
if(!$agent_type){
	$agent_type = "W";
}
if($div_ix){
	$db->query("SELECT * FROM shop_promotion_div where div_ix = '$div_ix' and agent_type = '".$agent_type."'  ");
	$db->fetch();
	
	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("컨텐츠관리", "프로모션 전시관리 > 프로모션 분류관리")."</td>
	  </tr>	  
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>	 
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>전시 분류명 <span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'><input type=text class='textbox2' name='div_name' value='".$db->dt[div_name]."' style='width:230px;' validation=true title='전시 분류명'> <span class=small></span></td>
		<td class='input_box_title'> <b>전시 분류코드 <span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'><input type=text class='textbox2' name='div_code' value='".$db->dt[div_code]."' style='width:230px;' validation=true title='전시 분류코드'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>자주쓰는메뉴 </b> </td>
	    <td class='input_box_item' >
	    	<input type=checkbox name='tab_view' id='tab_view' value='1' ".($db->dt[tab_view] == 1 ? "checked":"" )."><label for='tab_view'>탭노출</label>
	    	<input type=checkbox name='leftmenu_view' id='leftmenu_view' value='1' ".(($db->dt[leftmenu_view] == 1) ? "checked":"" )."><label for='leftmenu_view'>좌측메뉴노출</label>
	    </td>
		 <td class='input_box_title'> <b>사용유무 <span style='color:red'>(*)</span></b> </td>
	    <td class='input_box_item' >
	    	<input type=radio name='disp' id='disp_1' value='1' ".(($db->dt[disp] == "" || $db->dt[disp] == 1) ? "checked":"" )."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".(($db->dt[disp] == 0) ? "checked":"" )."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>	  
	  <tr>
	    <td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u>  분류 목록</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col style='width:15%;'>
	    <col style='width:15%;'>
	    <col style='width:20%;'>
	    <col style='width:20%;'>
		<col style='width:15%;'>
		<col style='width:15%;'>
	  <tr height=30 align=center >
	    <td class='m_td'> 분류명</td>
		<td class='m_td'> 분류코드</td>
	    <td class='m_td'> 자주쓰는메뉴</td>
		<td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";





$sql = 	"SELECT * FROM shop_promotion_div where agent_type = '".$agent_type."'  	";


//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>	    
		    <td class='list_box_td point' ><span style='width:".(30*$db->dt[div_depth])."px;'></span>".$db->dt[div_name]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[div_code]."</td>
			<td class='list_box_td'>".($db->dt[tab_view] == "1" ?  "탭메뉴 노출":"")." ".($db->dt[leftmenu_view] == "1" ?  " 좌측메뉴노출":"")."</td>
		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")." ".($db->dt[is_basic] == "1" ?  "(기본)":"")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td'>
		    	<!--a href=\"javascript:updateBankInfo('".$db->dt[div_ix]."','".$db->dt[div_name]."','".$db->dt[div_bbs_cnt]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
		    	<a href=\"?div_ix=".$db->dt[div_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
		    	if($db->dt[is_basic] == "0"){
	$Contents02 .= " <a href=\"javascript:deleteBankInfo('delete','".$db->dt[div_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
	$Contents02 .= "    		
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 ".($agent_type == "M" ? "모바일":"")." 전시분류가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "	  
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->	  
	  
	  </table>";

 
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='../display/promotion_category.act.php' method='post' onsubmit='return CheckFormValue(this)'><input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>						
						<input name='div_ix' type='hidden' value='$div_ix'>
						<input name='agent_type' type='hidden' value='$agent_type'>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 분류는 프로모션상품 상품 관리에서도 노출 되지 않습니다.</td></tr>
	</table>
	";

	
	$help_text = HelpBox("프로모션상품 분류 관리", $help_text);				
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
 	if(confirm(language_data['promotion_category.php']['A'][language])){//'해당카테고리  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.div_form; 	
 		frm.act.value = act;
 		frm.div_ix.value = div_ix;
 		frm.submit();
 	}	
}
 </script>
 ";

if($agent_type == "M"){
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
		$P->Navigation = "컨텐츠관리 > 프로모션 전시관리 > 프로모션 분류관리";
		$P->NaviTitle = "프로모션 분류관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();	
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = "컨텐츠관리 > 프로모션 전시관리 > 프로모션 분류관리";
		$P->title = "프로모션 분류관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}


function getFirstDIV($bm_ix, $selected=""){
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM shop_promotion_div
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

create table bbs_manage_div (
div_ix int(4) unsigned not null auto_increment  ,
div_name varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null, 
primary key(div_ix));
*/
?>