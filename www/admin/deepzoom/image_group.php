<? 
include("../class/layout.work.class");

$db = new Database;

//$db->query("SELECT * FROM ".TBL_BBS_MANAGE_CONFIG." where bm_ix ='$bm_ix' ");
//$db->fetch();


$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("이미지 그룹 관리", "마케팅지원 > 이미지 그룹 관리 ")."</td>
	  </tr>	  
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'> 
	    	<div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<table id='tab_02' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='image_list.php'\">이미지  관리</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_01' class='on'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='deepzoom_image_group_list.php'\" >이미지 그룹관리</td>
							<th class='box_03'></th>
						</tr>
						</table>
						
						<table id='tab_03' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='image_add.php'\">이미지 등록관리</td>
							<th class='box_03'></th>
						</tr>
						</table>
						
					</td>
					<td style='width:300px;text-align:right;vertical-align:bottom;padding:0 0 10 0'>						
						
					</td>
				</tr>
				</table>	
			</div>
	    </td>
	</tr>	
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 10;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 그룹 추가</b></div>")."</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle>  그룹 타입 : </td>
	    <td>
	    	<input type=radio name='group_depth' value='1' id='group_depth_1' onclick=\"document.getElementById('parent_group_ix').disabled=true;\" checked><label for='group_depth_1'>1차그룹</label>
	    	<input type=radio name='group_depth' value='2' id='group_depth_2' onclick=\"document.getElementById('parent_group_ix').disabled=false;\" ><label for='group_depth_2'>2차그룹</label>
	    	".getFirstDIV()." <span class='small'>2차 그룹 등록하기 위해서는 반드시 1차그룹를 선택하셔야 합니다.</span>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 class='dot-x'></td></tr>	 
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 그룹명 : </td><td><input type=text class='textbox' name='group_name' value='".$db->dt[group_name]."' style='width:230px;'> <span class=small></span></td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 class='dot-x'></td></tr>	  	
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 노출순서 : </td><td><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small>노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다</span></td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 class='dot-x'></td></tr>	  	
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 사용유무 : </td>
	    <td>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 class='dot-x'></td></tr>	 
	  </table>";
	  
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 그룹명을  입력해주세요
	</td>
</tr>
</table>
";



$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>	  
	  <tr>
	    <td align='left' colspan=7> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 10;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  그룹 목록</b></div>")."</td>
	  </tr>
	  <tr height=10><td colspan=6 ></td></tr>		  	  
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td style='width:150px;'> 그룹명</td>
	    <td style='width:50px;'> 그룹코드</td>
		<td style='width:50px;'> 부모그룹코드</td>
	    <td style='width:70px;'> 노출순서</td>
	    <td style='width:100px;'> 사용유무</td>
	    <td style='width:150px;'> 등록일자</td>
	    <td style='width:150px;'> 관리</td>
	  </tr>";

$sql = "select abg.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order from deepzoom_image_group abg where company_id ='".$admininfo["company_id"]."' order by  group_order asc , group_ix asc, vieworder asc ";
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
		    <td>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[group_depth])."></td>
						<td width='*' align='left'>".$db->dt[group_name]." ".$db->dt[group_order]."</td>
					</tr>
				</table>
			
			</td>
		    <td>".$db->dt[group_ix]."</td>	   
			<td>".$db->dt[parent_group_ix]."</td>	   
		    <td>".$db->dt[vieworder]."</td>	    
		    <td>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td>".$db->dt[regdate]."</td>
		    <td>
		    	<a href=\"javascript:updateGroupInfo('".$db->dt[group_ix]."','".$db->dt[group_name]."','".$db->dt[disp]."','".$db->dt[vieworder]."')\"><img src='../image/btc_modify.gif' border=0></a>  
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[group_ix]."')\"><img src='../image/btc_del.gif' border=0></a>
		    </td>
		  </tr>
		  <tr height=1><td colspan=6 class='dot-x'></td></tr>	  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 그룹가 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=6 class='dot-x'></td></tr>	  ";
}
$Contents02 .= "	  
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->	  
	  
	  </table>";

	  
$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";	  


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='group_form' action='image_group.act.php' method='post' onsubmit='return CheckValue(this)' target=act><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='bm_ix' type='hidden' value='$bm_ix'><input name='group_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >이미지 그룹 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>그룹명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		
	</table>
	";

	
	$help_text = HelpBox("이미지 그룹 관리", $help_text);				
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
 		alert('등록하시고자 하는 이미지 그룹명을 입력해주세요');
 		frm.group_name.focus();
 		return false;
 	}
 	
 }
 function updateGroupInfo(group_ix,group_name,disp, vieworder){
 	var frm = document.group_form;
 	
 	frm.act.value = 'update';
 	frm.group_ix.value = group_ix;
 	frm.group_name.value = group_name;
 	frm.vieworder.value = vieworder;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
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
	$P->strLeftMenu = deepzoom_menu();
	$P->Navigation = "HOME > 마케팅지원 > 이미지 그룹 관리";
	$P->NaviTitle = "게시판관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = deepzoom_menu();
	$P->Navigation = "HOME > 마케팅지원 > 이미지 그룹 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT bdiv.*
			FROM deepzoom_image_group bdiv 
			where group_depth = 1 and company_id ='".$admininfo["company_id"]."'
			group by group_ix ";
	
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


CREATE TABLE IF NOT EXISTS `deepzoom_image_group` (
  `group_ix` int(4) unsigned NOT NULL auto_increment,
  `parent_group_ix` int(4) unsigned default NULL,
  `group_name` varchar(20) default NULL,
  `group_depth` int(2) unsigned default '1',
  `disp` char(1) default '1',
  `vieworder` int(8) default '0',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`group_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;



*/
?>