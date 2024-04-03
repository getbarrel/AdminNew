<? 
include("../class/layout.class");

$db = new Database;

if(!$agent_type){
	$agent_type = "W";
}

if($group_ix){
	$db->query("SELECT * FROM econtract_group where group_ix = '$group_ix'   ");
	$db->fetch();
	
	$act = "update";
	$disp = $db->dt[disp];
}else{
	$act = "insert";
	$disp = 1;
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>	 
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15%'>
	  <col width='35%'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>분류명 <span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'><input type=text class='textbox2' name='group_name' value='".$db->dt[group_name]."' style='width:230px;' validation=true title='분류명'> <span class=small></span></td>
		<td class='input_box_title'> <b>분류코드 <span style='color:red'>(*)</span></b> </td>
		<td class='input_box_item'>".($db->dt[group_ix] ? $db->dt[group_ix]:"분류 정보 입력시 자동부여됩니다.")." <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무 <span style='color:red'>(*)</span></b> </td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='disp' id='disp_1'  value='1' ".(($disp == "" || $disp == 1) ? "checked":"" )."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".(($disp == 0) ? "checked":"" )."><label for='disp_0'>사용안함</label>
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
		<col style='width:20%;'>
	    <col style='width:20%;'>
	    <col style='width:20%;'>
	    <col style='width:20%;'>
		<col style='width:20%;'>
	  <tr height=30 align=center >
	    <td class='m_td'> 분류코드</td>
		<td class='m_td'> 분류명</td>		
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";





$sql = 	"SELECT * FROM econtract_group where  1		";


//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>	    
		    <td class='list_box_td point' ></span>".$db->dt[group_ix]."</td>
			<td class='list_box_td point' ></span>".$db->dt[group_name]."</td>
		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용안함")." ".($db->dt[is_basic] == "1" ?  "(기본)":"")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td'>
		    	<!--a href=\"javascript:updateContractGroup('".$db->dt[group_ix]."','".$db->dt[group_name]."','".$db->dt[div_bbs_cnt]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
		    	<a href=\"?group_ix=".$db->dt[group_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
		    	if($db->dt[is_basic] == "0"){
	$Contents02 .= " <a href=\"javascript:deleteMainGoodsCategory('delete','".$db->dt[group_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
	$Contents02 .= "    		
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=5>등록된  계약서 분류가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "	   
	  </table>";

 

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='../econtract/contract_group.act.php' method='post' onsubmit='return CheckFormValue(this)' target='iframe_act'>
						<input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>						
						<input name='group_ix' type='hidden' value='$group_ix'>
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 분류는 계약서 상품 관리에서도 노출 되지 않습니다.</td></tr>
	</table>
	";

	
	$help_text = HelpBox("계약서 분류관리", $help_text);				
$Contents = $Contents.$help_text;	

 $Script = "
 <script language='javascript'>
 function updateContractGroup(group_ix,group_name,disp){
 	var frm = document.div_form;
 	
 	frm.act.value = 'update';
 	frm.group_ix.value = group_ix;
 	frm.group_name.value = group_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
 
}
 
 function deleteMainGoodsCategory(act, group_ix){
 	if(confirm('해당카테고리  정보를 정말로 삭제하시겠습니까?')){//language_data['main_goods_category.php']['A'][language]
 		var frm = document.div_form; 	
 		frm.act.value = act;
 		frm.group_ix.value = group_ix;
 		frm.submit();
 	}	
}
 </script>
 ";
 
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = econtract_menu();
		$P->Navigation = "전자계약관리 > 계약서 분류관리";
		$P->NaviTitle = "계약서 분류관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();	
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = econtract_menu();
		$P->Navigation = "전자계약관리 > 계약서 분류관리";
		$P->title = "계약서 분류관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}

 
 
?>