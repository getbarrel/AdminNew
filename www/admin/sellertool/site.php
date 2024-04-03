<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("제휴사 관리", "제휴사연동 > 제휴사 관리 ")."</td>
	  </tr>";
if(false){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	 ".buyingservice_site_tab("site")."
	    </td>
	  </tr>";
}
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35'>
	  <col width='15%'>
	  <col width='35%'>";
if(false){
$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b> 제휴사연동 구분 : </b></td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='depth' value='1' id='depth_1' onclick=\"document.getElementById('parent_si_ix').disabled=true;\" checked><label for='depth_1'>1차 구분 </label>
	    	<input type=radio name='depth' value='2' id='depth_2' onclick=\"document.getElementById('parent_si_ix').disabled=false;\" ><label for='depth_2'>2차 구분 </label>
	    	".getFirstDIV()." <span class='small'><!--2차 제휴사 관리 등록하기 위해서는 반드시 1차제휴사 관리를 선택하셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
	    </td>
	  </tr>";
}
$Contents01 .= "
	  <tr bgcolor=#ffffff > 
	   <td class='input_box_title'> <b>국내외 구분 : </b></td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='site_div' id='site_div_1' value='1' checked><label for='site_div_1'>국내 제휴사</label>
	    	<input type=radio name='site_div' id='site_div_2' value='2' ><label for='site_div_2'>해외 제휴사</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>제휴사명 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='site_name' id='site_name' value='".$db->dt[site_name]."' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>제휴사 코드 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='site_code' id='site_code' value='".$db->dt[site_code]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>제휴사 도메인 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='site_domain' id='site_domain' value='".$db->dt[site_domain]."' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>API Key : </b></td>
	    <td class='input_box_item'>
			<input type=text class='textbox' name='api_key' value='".$db->dt[api_key]."' style='width:230px;'>
		</td>
	  </tr>
	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서 : </b></td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
		
		<td class='input_box_title'> <b> API ticket key</b> </td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox' name='api_ticket' value='".$db->dt[api_ticket]."' style='width:230px;'>
	    </td>
	  </tr> 
		<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용자 아이디 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='site_id' value='".$db->dt[site_id]."' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>비밀번호 :</b> </td>
		<td class='input_box_item'>
			<input type=password class='textbox' name='site_pw' value='".$db->dt[site_pw]."' style='width:230px;'>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>API연동유무 : </b></td>
		<td class='input_box_item'>
			<input type=radio name='api_yn' id='api_yn_1' value='Y' ".CompareReturnValue("Y",$db->dt[api_yn],"checked")."><label for='api_yn_1'>사용</label>
	    	<input type=radio name='api_yn' id='api_yn_0' value='N' ".CompareReturnValue("N",$db->dt[api_yn],"checked")."checked><label for='api_yn_0'>사용하지않음</label>
		</td>
	 
	   <td class='input_box_title'> <b>제휴사 사용유무 : </b></td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용 맵핑 구분 : </b></td>
		<td class='input_box_item' colspan='3'>
			<input type=checkbox name='use_mapping_div[]' id='use_mapping_div_i' value='I' ".CompareReturnValue("I",$db->dt[use_mapping_div],"checked")."><label for='use_mapping_div_i'>품목분류(표준카테고리)</label>
	    	<input type=checkbox name='use_mapping_div[]' id='use_mapping_div_b' value='B' ".CompareReturnValue("B",$db->dt[use_mapping_div],"checked")."><label for='use_mapping_div_b'>브랜드</label>
			<input type=checkbox name='use_mapping_div[]' id='use_mapping_div_c' value='C' ".CompareReturnValue("C",$db->dt[use_mapping_div],"checked")."><label for='use_mapping_div_c'>제조사</label>
			<input type=checkbox name='use_mapping_div[]' id='use_mapping_div_n' value='N' ".CompareReturnValue("N",$db->dt[use_mapping_div],"checked")."><label for='use_mapping_div_n'>제조국</label>
		</td>
	  </tr>
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 제휴사 관리명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  제휴사 관리 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width='6%'>
		<col width='10%'>
        <col width=10%>
        <col width=10%>
        <col width=6%>
        <col width=8%>
        <col width=*>
        <col width=12%>
        <col width=10%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 구분</td>
		<td class='m_td'> 제휴사명</td>
		<td class='m_td'> 제휴사 코드</td>
		<td class='m_td'> 사용자 아이디</td>
		
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
        <td class='m_td'> API Key</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";

$sql = "select cr.*  from sellertool_site_info cr  order by vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then si_ix  else parent_si_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.si_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by si_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td list_bg_gray'>".($db->dt[site_div] == "1" ?  "국내":"해외")."</td>
			<td class='list_box_td point' style='padding-left:20px;' nowrap>
				".$db->dt[site_name]."
			</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[site_code]."</td>
		    <td class='list_box_td'>".$db->dt[site_id]."</td>
			
			
			<td class='list_box_td'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    ";
			if($db->dt[site_code] == 'interpark_api'){
			$Contents02 .= "
			<td class='list_box_td' style='padding:0px 5px;'><input type='button' value='관리' onClick=\"PopSWindow('sellertool_api_key.php?si_ix=".$db->dt[si_ix]."&mmode=pop',900,710,'sellertool_api_key')\" /></td>";
			}else{
			$Contents02 .= "
			<td class='list_box_td' style='padding:0px 5px;'>".$db->dt[api_key]."</td>";
			}
			$Contents02 .= "
            <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td' style='padding:0px 5px;' nowrap>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02 .= "    
		    	<a href=\"javascript:updateSiteInfo('".$db->dt[si_ix]."','".$db->dt[site_div]."','".$db->dt[site_name]."','".$db->dt[site_code]."','".$db->dt[site_id]."','".$db->dt[site_pw]."','".$db->dt[site_domain]."','".$db->dt[api_key]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."','".$db->dt[is_basic]."','".$db->dt[api_yn]."', '".$db->dt[api_ticket]."', '".$db->dt[use_mapping_div]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{   
                $Contents02 .= "    
		    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }
if(!$db->dt[is_basic]){
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){        
        $Contents02 .= "
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[si_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
     
    }else{
        $Contents02 .= "
	    		<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
    }
}
$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=9>등록된 제휴사연동 제휴사연동 정보가 없습니다. </td>
		  </tr>";
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


$Contents = "<form name='site_form' action='site.act.php' method='post' onsubmit='return CheckValue(this)' target=act>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='insert'>
<input name='bm_ix' type='hidden' value='$bm_ix'>
<input name='si_ix' type='hidden' value=''>";
$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01."<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>".$ContentsDesc01."</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$ButtonString."</td></tr>";

$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$Contents02."<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >연동하고자 하는 제휴사의 API key 를 발급받아 등록하여 사용하실수 있습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기본 제휴사는 제휴사명, 제휴사 코드 도메인등을 수정하실 수 없습니다.</td></tr>
	</table>
	";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("제휴사 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function CheckValue(frm){
 	if(frm.depth[1].checked){
 		if(frm.parent_si_ix.value == ''){
	 		alert(language_data['site.php']['A'][language]);
			//'2차 제휴사 관리을 등록하기 위해서는 1차제휴사 관리을 반드시 선택하셔야 합니다.'
	 		return false;
 		}
 	}

 	if(frm.site_name.value.length < 1){
 		alert(language_data['site.php']['B'][language]);
		//'등록하시고자 하는 제휴사연동 제휴사 관리명을 입력해주세요'
 		frm.site_name.focus();
 		return false;
 	}

 }
 function updateSiteInfo(si_ix, site_div, site_name,site_code,site_id,site_pw,site_domain,api_key,depth, vieworder,group_order,disp, is_basic,api_yn, api_ticket, use_mapping_div){
 	var frm = document.site_form;

 	frm.act.value = 'update';
 	frm.si_ix.value = si_ix;
 	frm.site_name.value = site_name;
	frm.site_code.value = site_code;
	frm.site_id.value = site_id;
	frm.site_pw.value = site_pw;
	frm.site_domain.value = site_domain;
    frm.api_key.value = api_key;
 	frm.vieworder.value = vieworder;
 	frm.api_ticket.value = api_ticket;
	if(site_div=='1') {
		frm.site_div[0].checked = true;
	} else {
		frm.site_div[1].checked = true;
	}

	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

	if(api_yn == 'Y'){
		frm.api_yn[0].checked = true;
	}else{
		frm.api_yn[1].checked = true;
	}
	if(is_basic == '1') {
		
		$('#site_name').attr('readonly',true);
		$('#site_code').attr('readonly',true);
		$('#site_id').attr('readonly',true);
		$('#site_domain').attr('readonly',true);

		$('#site_name').css('background-color','#efefef');
		$('#site_code').css('background-color','#efefef');
		$('#site_id').css('background-color','#efefef');
		$('#site_domain').css('background-color','#efefef');
	} else {
		$('#site_name').attr('readonly',false);
		$('#site_code').attr('readonly',false);
		$('#site_id').attr('readonly',false);
		$('#site_domain').attr('readonly',false);

		$('#site_name').css('background-color','#ffffff');
		$('#site_code').css('background-color','#ffffff');
		$('#site_id').css('background-color','#ffffff');
		$('#site_domain').css('background-color','#ffffff');
	}

	if( use_mapping_div.indexOf('|I|') > -1 ){
		$('#use_mapping_div_i').attr('checked',true);
	}else{
		$('#use_mapping_div_i').attr('checked',false);
	}

	if( use_mapping_div.indexOf('|B|') > -1 ){
		$('#use_mapping_div_b').attr('checked',true);
	}else{
		$('#use_mapping_div_b').attr('checked',false);
	}

	if( use_mapping_div.indexOf('|C|') > -1 ){
		$('#use_mapping_div_c').attr('checked',true);
	}else{
		$('#use_mapping_div_c').attr('checked',false);
	}

	if( use_mapping_div.indexOf('|N|') > -1 ){
		$('#use_mapping_div_n').attr('checked',true);
	}else{
		$('#use_mapping_div_n').attr('checked',false);
	}

}

 function deleteGroupInfo(act, si_ix){
 	if(confirm('해당제휴사 관리 정보를 정말로 삭제하시겠습니까?')){//language_data['site.php']['C'][language]
 		var frm = document.site_form;
 		frm.act.value = act;
 		frm.si_ix.value = si_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "상품관리 > 제휴사연동 > 제휴사 관리";
	$P->title = "제휴사 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "상품관리 > 제휴사연동 > 제휴사 관리";
	$P->title = "제휴사 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE IF NOT EXISTS `sellertool_site_info` (
  `si_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `site_name` varchar(20) DEFAULT NULL COMMENT '제휴사연동명',
  `site_code` varchar(20) DEFAULT NULL COMMENT '제휴사연동 코드',
  `site_domain` varchar(255) DEFAULT NULL COMMENT '제휴사연동 도메인',
  `site_id` varchar(50) DEFAULT NULL COMMENT '제휴사연동 아이디',
  `site_pw` varchar(255) DEFAULT NULL COMMENT '제휴사연동 비밀번호',  
  `disp` char(1) DEFAULT '1' COMMENT '사용여부',
  `vieworder` int(8) DEFAULT '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`si_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='제휴사 정보'

*/
?>