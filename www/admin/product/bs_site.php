<?
include("../class/layout.class");
include_once("buyingService.lib.php");

$db = new Database;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("구매대행 사이트 관리", "구매대행 > 구매대행 사이트 관리 ")."</td>
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
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>구매대행 사이트 추가</b></div>")."</td>
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
	    <td class='input_box_title'> <b> 사이트 구분 : </b></td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='depth' value='1' id='depth_1' onclick=\"document.getElementById('parent_bs_ix').disabled=true;\" checked><label for='depth_1'>1차 구분 </label>
	    	<input type=radio name='depth' value='2' id='depth_2' onclick=\"document.getElementById('parent_bs_ix').disabled=false;\" ><label for='depth_2'>2차 구분 </label>
	    	".getFirstDIV()." <span class='small'><!--2차 구매대행 사이트 관리 등록하기 위해서는 반드시 1차구매대행 사이트 관리를 선택하셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
	    </td>
	  </tr>";
}
$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사이트 명 :</b> </td>
		<td class='input_box_item'><input type=hidden name='depth' value='1' ><input type=text class='textbox' name='site_name' value='".$db->dt[site_name]."' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>사이트 코드 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='site_code' value='".$db->dt[site_code]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사이트 도메인 :</b> </td>
		<td class='input_box_item'><input type=hidden name='depth' value='1' ><input type=text class='textbox' name='site_domain' value='".$db->dt[site_domain]."' style='width:230px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>사이트 지역구분 :</b> </td>
		<td class='input_box_item'>
			<input type=radio name='region_div' id='region_div_korea' value='KOREA' > <label for=region_div_korea>KOREA</label>
			<input type=radio name='region_div' id='region_div_usa' value='USA' > <label for=region_div_usa>USA</label>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서 : </b></td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	 
	    <td class='input_box_title'> <b>사용유무 : </b></td>
	    <td class='input_box_item'>
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
		  <u>$board_name </u> 에 이용할 구매대행 사이트 관리명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  구매대행 사이트 관리 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>구매대행 사이트 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
	  <col width='*'>
	    <col width=15%>
		<col width=10%>
	    <col width=10%>
	    <col width=15%>
	    <col width=15%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 사이트명</td>
		<td class='m_td'> 사이트 코드</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='m_td'> 관리</td>
	  </tr>";

$sql = "select cr.* , case when depth = 1 then bs_ix  else parent_bs_ix end as group_order from shop_buyingservice_site cr  order by  group_order asc ,depth asc , vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then bs_ix  else parent_bs_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.bs_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by bs_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[site_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[site_code]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<a href=\"javascript:updateSiteInfo('".$db->dt[bs_ix]."','".$db->dt[site_name]."','".$db->dt[site_code]."','".$db->dt[site_domain]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[bs_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 구매대행 사이트 정보가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";



$Contents = "<form name='site_form' action='bs_site.act.php' method='post' onsubmit='return CheckValue(this)' target=act>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='insert'>
<input name='bm_ix' type='hidden' value='$bm_ix'>
<input name='bs_ix' type='hidden' value=''>";
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
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 구매대행 사이트 관리 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>구매대행 사이트 관리명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("구매대행 사이트 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function CheckValue(frm){
 	if(frm.depth[1].checked){
 		if(frm.parent_bs_ix.value == ''){
	 		alert(language_data['site.php']['A'][language]);
			//'2차 구매대행 사이트 관리을 등록하기 위해서는 1차구매대행 사이트 관리을 반드시 선택하셔야 합니다.'
	 		return false;
 		}
 	}

 	if(frm.site_name.value.length < 1){
 		alert(language_data['site.php']['B'][language]);
		//'등록하시고자 하는 구매대행 구매대행 사이트 관리명을 입력해주세요'
 		frm.site_name.focus();
 		return false;
 	}

 }
 function updateSiteInfo(bs_ix,site_name,site_code,site_domain,depth, vieworder,group_order,disp){
 	var frm = document.site_form;

 	frm.act.value = 'update';
 	frm.bs_ix.value = bs_ix;
 	frm.site_name.value = site_name;
	frm.site_code.value = site_code;
	frm.site_domain.value = site_domain;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

 	if(depth == '1'){
 		frm.depth[0].checked = true;
		//document.getElementById('parent_bs_ix').disabled=true;
		//document.getElementById('parent_bs_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		//document.getElementById('parent_bs_ix').disabled=false;
		//document.getElementById('parent_bs_ix').value=group_order;
 	}

}

 function deleteGroupInfo(act, bs_ix){
 	if(confirm(language_data['site.php']['C'][language])){//'해당구매대행 사이트 관리 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.site_form;
 		frm.act.value = act;
 		frm.bs_ix.value = bs_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 구매대행 > 구매대행 사이트 관리";
	$P->title = "구매대행 사이트 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 구매대행 > 구매대행 사이트 관리";
	$P->title = "구매대행 사이트 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `shop_address_group` (
  `bs_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_bs_ix` int(4) unsigned DEFAULT NULL,
  `site_name` varchar(20) DEFAULT NULL,
  `depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`bs_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>