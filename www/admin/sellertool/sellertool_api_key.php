<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;

$si_ix = $_GET['si_ix'];

$sql = "select * from sellertool_site_info where si_ix = '".$si_ix."'";
$db->query($sql);
$db->fetch();


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("API_KEY 관리", "제휴사연동 > API_KEY 관리 ")."</td>
	  </tr>";

$Contents01 .= "
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>API Key 추가</b></div>")."</td>
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
	    <td class='input_box_title'> <b>제휴사명 :</b> </td>
		<td class='input_box_item'>".$db->dt[site_name]."<span class=small></span></td>
	    <td class='input_box_title'> <b>제휴사 코드 :</b> </td>
		<td class='input_box_item'>".$db->dt[site_code]."<span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>Api 관리명 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='api_name' value='' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>Api 관리키 :</b> </td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='api_method' value='' style='width:230px;'>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>Api 1차키 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='api_key_1' value='' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>Api 2차키 :</b> </td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='api_key_2' value='' style='width:230px;'>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무 : </b></td>
	    <td class='input_box_item' colspan='3'>
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
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>API Key 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
        <col width='10%'>
        <col width=15%>
        <col width=30%>
        <col width=30%>
        <col width=8%>
        
        <col width=10%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> API 관리명</td>
		<td class='m_td'> API 관리키</td>
		<td class='m_td'> API 1차키</td>
		
	    <td class='m_td'> API 2차키</td>
	    <td class='m_td'> 사용유무</td>
	    
	    <td class='m_td'> 관리</td>
	  </tr>";

$sql = "select cr.*  from sellertool_site_detail_info cr  order by regdate desc ";
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
			<td class='list_box_td point' style='' nowrap>
				".$db->dt[api_name]."
			</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[api_method]."</td>
			
			
			<td class='list_box_td'>".$db->dt[api_key_1]."</td>
		  
		   
			<td class='list_box_td' style='padding:0px 5px;'>".$db->dt[api_key_2]."</td>
			 <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td' style='padding:0px 5px;' nowrap>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $Contents02 .= "    
		    	<a href=\"javascript:updateSiteInfo('".$db->dt[sid_ix]."','".$db->dt[api_name]."','".$db->dt[api_method]."','".$db->dt[api_key_1]."','".$db->dt[api_key_2]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{   
                $Contents02 .= "    
		    	<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }
if(!$db->dt[is_basic]){
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){        
        $Contents02 .= "
	    		<a href=\"javascript:deleteGroupInfo('detail_delete','".$db->dt[sid_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
     
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
		    <td align=center colspan=8>등록된 제휴사 API key 정보가 없습니다. </td>
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
<input name='act' type='hidden' value='detail_insert'>
<input name='si_ix' type='hidden' value='".$si_ix."'>
<input name='sid_ix' type='hidden' value='".$db->dt[sid_ix]."'>";

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
  	if(frm.api_name.value.length < 1){
 		alert('Api 관리 명칭을 입력해 주세요.');
 		frm.api_name.focus();
 		return false;
 	}

 }

 function updateSiteInfo(sid_ix,api_name,api_method,api_key_1,api_key_2,disp){
 	var frm = document.site_form;

 	frm.act.value = 'detail_update';
	frm.sid_ix.value = sid_ix;
 	frm.api_name.value = api_name;
	frm.api_method.value = api_method;
	frm.api_key_1.value = api_key_1;
	frm.api_key_2.value = api_key_2;

	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}
}

 function deleteGroupInfo(act, sid_ix){
 	if(confirm('해당제휴사 API key 정보를 정말로 삭제하시겠습니까?')){//language_data['site.php']['C'][language]
 		var frm = document.site_form;
 		frm.act.value = act;
 		frm.sid_ix.value = sid_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴사 관리 > API_KEY 관리";
	$P->title = "API_KEY 관리";
	$P->NaviTitle = "API KEY 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴사 관리 > API_KEY 관리";
	$P->title = "API_KEY 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


?>