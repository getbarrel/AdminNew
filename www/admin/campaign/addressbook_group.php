<?
include("../class/layout.class");

$db = new Database;

//$db->query("SELECT * FROM ".TBL_BBS_MANAGE_CONFIG." where bm_ix ='$bm_ix' ");
//$db->fetch();
$board_name = $db->dt[board_name];
$board_ename = $db->dt[board_ename];

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("메일링/SMS 주소록 그룹관리", "메일링/SMS > 주소록 그룹관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_list.php'\">주소록 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add.php'\">주소록 개별등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add_excel.php'\">주소록 대량등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_01' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_group.php'\" >주소록 그룹관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td style='width:300px;text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 그룹 추가</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='margin-top:3px;' class='input_table_box' >
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title' style='width:10%'>  그룹 타입<img src='".$required3_path."'></td>
	    <td class='input_box_item'>
	    	<input type=radio name='group_depth' value='1' id='group_depth_1' onclick=\"document.getElementById('parent_group_ix').disabled=true;\" checked><label for='group_depth_1'>1차그룹</label>
	    	<input type=radio name='group_depth' value='2' id='group_depth_2' onclick=\"document.getElementById('parent_group_ix').disabled=false;\" ><label for='group_depth_2'>2차그룹</label>
	    	".getFirstDIV()."
	    </td>
	  </tr>
	  <tr>
	    <td class='input_box_title' style='width:10%'> 그룹명<img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='group_name' value='".$db->dt[group_name]."' style='width:230px;'>
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title' style='width:10%'> 노출순서<img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> 
		</td>
	  </tr>
	  <tr>
	    <td class='input_box_title' style='width:10%'> 사용유무</td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  <tr>
	    <td class='input_box_title' style='width:10%'>그룹설명</td>
	    <td class='input_box_item'>
	    	<textarea  name='memo'  style='width:90%;height:70px;'>".$db->dt[memo]."</textarea>
	    </td>
	  </tr>
	  </table>";


$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  그룹 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='margin-top:3px;' class='list_table_box'>
	 <col width='15%'>
	 <col width='25%'>
	 <col width='10%'>
	 <col width='10%'>
	 <col width='10%'>
	 <col width='10%'>
	 <col width='10%'>

	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
		<td class='s_td'> 그룹코드</td>
	    <td class='s_td'> 그룹명</td>
	    <td class='m_td'> 등록수</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

$sql = "select abg.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order from shop_addressbook_group abg where abg.company_id = '".$admininfo[company_id]."' order by  group_order asc ,group_depth asc , vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.group_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by group_ix
		order by group_order asc, group_depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td'></td>
			<td class='list_box_td point' style='text-align:left;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%' >
					<tr>
						<td width=".(20*$db->dt[group_depth])."></td>
						<td width='*' align='left'>".$db->dt[group_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td'>".$db->dt[group_mem_cnt]."</td>
		    <td class='list_box_td'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "O":"X")."</td>
		    <td class='list_box_td'>".$db->dt[regdate]."</td>
		    <td class='list_box_td'>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02.="                
		    	<a href=\"javascript:updateGroupInfo('".$db->dt[group_ix]."','".$db->dt[group_name]."','".$db->dt[group_depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{
                $Contents02.="                
		    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents02.="
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[group_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
            }else{
                $Contents02.="
	    		<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
            }
            $Contents02.="
		    </td>
		  </tr>	  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 그룹가 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=6 class='td_underline'></td></tr>	  ";
}
$Contents02 .= "

	  </table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
    </table>
    ";
}else{
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></a></td></tr>
    </table>
    ";
}


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='group_form' action='addressbook_group.act.php' method='post' onsubmit='return CheckValue(this)' target=act><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='bm_ix' type='hidden' value='$bm_ix'><input name='group_ix' type='hidden' value=''>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >메일링/SMS 그룹 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>그룹명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>

	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'E');

	$help_text = HelpBox("메일링/SMS 그룹 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function CheckValue(frm){
 	if(frm.group_depth[1].checked){
 		if(frm.parent_group_ix.value == ''){
	 		alert('2차 그룹을 등록하기 위해서는 1차그룹을 반드시 선택하셔야 합니다.');
	 		return false;
 		}
 	}

 	if(frm.group_name.value.length < 1){
 		alert('등록하시고자 하는 메일링/SMS 그룹명을 입력해주세요');
 		frm.group_name.focus();
 		return false;
 	}

 }
 function updateGroupInfo(group_ix,group_name,group_depth, vieworder,group_order,disp){
 	var frm = document.group_form;

 	frm.act.value = 'update';
 	frm.group_ix.value = group_ix;
 	frm.group_name.value = group_name;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

 	if(group_depth == '1'){
 		frm.group_depth[0].checked = true;
		document.getElementById('parent_group_ix').disabled=true;
		document.getElementById('parent_group_ix').options[0].selected='selected';
 	}else{
 		frm.group_depth[1].checked = true;
		document.getElementById('parent_group_ix').disabled=false;
		document.getElementById('parent_group_ix').value=group_order;
 	}

}

 function deleteGroupInfo(act, group_ix){
 	if(confirm('해당그룹  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.group_form;
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
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 주소록 그룹관리";
	$P->title = "주소록 그룹관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 주소록 그룹관리";
	$P->title = "주소록 그룹관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT abg.*
			FROM shop_addressbook_group abg
			where group_depth = 1 and abg.company_id = '".$admininfo[company_id]."'
			order by vieworder asc ";
			//group by group_ix

	$mdb->query($sql);

	$mstring = "<select name='parent_group_ix' id='parent_group_ix' disabled>";
	$mstring .= "<option value=''>1차그룹</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

/*

CREATE TABLE `shop_address_group` (
  `group_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_group_ix` int(4) unsigned DEFAULT NULL,
  `group_name` varchar(20) DEFAULT NULL,
  `group_depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`group_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>