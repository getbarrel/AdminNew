<?
include("../class/layout.class");
include_once("service.lib.php");

$db = new Database;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("서비스코드 관리", "서비스코드관리 > 서비스코드 관리 ")."</td>
	  </tr>
	 
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>서비스코드 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	  <col width = 20% >
	  <col width = * >
	  <tr bgcolor=#ffffff >
	    <td class='search_box_title' >  서비스코드 타입 </td>
	    <td class='search_box_item' colspan=3>
	    	<input type=radio name='depth' value='1' id='depth_1' onclick=\"document.getElementById('parent_service_ix').disabled=true;\" checked><label for='depth_1'>1차서비스코드</label>
	    	<input type=radio name='depth' value='2' id='depth_2' onclick=\"document.getElementById('parent_service_ix').disabled=false;\" ><label for='depth_2'>2차서비스코드</label>
	    	".getFirstDIV()." <span class='small'><!--2차 서비스코드 등록하기 위해서는 반드시 1차서비스코드를 선택하셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
			".getSellerList()."
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='search_box_title'> 서비스명 : </td>
		<td class='search_box_item'><input type=text class='textbox' name='service_name' value='".$db->dt[service_name]."' style='width:230px;'> <span class=small></span></td>
		<td class='search_box_title'> 서비스코드 : </td>
		<td class='search_box_item'><input type=text class='textbox' name='service_code' value='".$db->dt[service_code]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='search_box_title'> 노출순서 : </td>
		<td class='search_box_item'>
			<input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'>
			<span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
		</td>
		<td class='search_box_title'> 사용유무 : </td>
	    <td class='search_box_item'>
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
		  <u>$board_name </u> 에 이용할 서비스코드명을  입력해주세요
	</td>
</tr>
</table>
";*/

/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  서비스코드 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>서비스코드 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
		<col width='*'>
	    <col width=250>
	    <col width=100>
	    <col width=180>
	    <col width=150>
	  <tr height=28 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 서비스명</td>
		 <td class='s_td'> 서비스코드</td>
	    <td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

$sql = "select cr.* , case when depth = 1 then service_ix  else parent_service_ix end as group_order from service_division cr  order by  group_order asc ,depth asc , vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then service_ix  else parent_service_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.service_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by service_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[service_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[service_code]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td'>
		    	<a href=\"javascript:updateServiceInfo('".$db->dt[service_ix]."','".$db->dt[service_name]."','".$db->dt[service_code]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[service_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=100>
		    <td align=center colspan=6>등록된 서비스코드이 없습니다. </td>
		  </tr> ";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='service_frm' action='service_div.act.php' method='post' onsubmit='return CheckValue(this)' target=act>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='insert'>
<input name='bm_ix' type='hidden' value='$bm_ix'>
<input name='service_ix' type='hidden' value=''>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >서비스코드관리 서비스코드 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>서비스코드명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("서비스코드 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function CheckValue(frm){
 	if(frm.depth[1].checked){
 		if(frm.parent_service_ix.value == ''){
	 		alert('2차 서비스코드을 등록하기 위해서는 1차서비스코드을 반드시 선택하셔야 합니다.');
	 		return false;
 		}
 	}

 	if(frm.service_name.value.length < 1){
 		alert('등록하시고자 하는 서비스코드관리 서비스코드명을 입력해주세요');
 		frm.service_name.focus();
 		return false;
 	}

 }
 function updateServiceInfo(service_ix,service_name,service_code, depth, vieworder,group_order,disp){
 	var frm = document.service_frm;

 	frm.act.value = 'update';
 	frm.service_ix.value = service_ix;
 	frm.service_name.value = service_name;
	frm.service_code.value = service_code;
 	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}

 	if(depth == '1'){
 		frm.depth[0].checked = true;
		document.getElementById('parent_service_ix').disabled=true;
		document.getElementById('parent_service_ix').options[0].selected='selected';
 	}else{
 		frm.depth[1].checked = true;
		document.getElementById('parent_service_ix').disabled=false;
		document.getElementById('parent_service_ix').value=group_order;
		if(group_order=='7') {
			frm.seller_list.disabled=false;
			var op_cnt=frm.seller_list.options.length;
			for(var i=0;i<op_cnt;i++) {
				if(frm.seller_list.options[i].value==service_code) {
					frm.seller_list.options[i].selected='selected';
					return;
				}
			}
			frm.seller_list.options[0].selected='selected';
		}
 	}

}

 function deleteGroupInfo(act, service_ix){
 	if(confirm('해당서비스코드  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.service_frm;
 		frm.act.value = act;
 		frm.service_ix.value = service_ix;
 		frm.submit();
 	}
}

function ch_second_code(sel) {
	var b_sel=document.getElementById('seller_list');
	if(sel.value=='7') {
		b_sel.disabled=false;
	} else {
		//b_sel.disabled=true;
		b_sel.options[0].selected='selected';
		sel_seller(b_sel);
	}
}

function sel_seller(sel) {
	var fm=document.service_frm;
	var s_name=fm.service_name;
	var s_code=fm.service_code;
	var sel_index=sel.selectedIndex;
	if(sel_index>0) {
		s_name.value=sel.options[sel_index].getAttribute('com_name');
		s_code.value=sel.options[sel_index].value;
	} else {
		s_name.value='';
		s_code.value='';
	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = service_menu();
	$P->Navigation = "서비스관리 > 서비스코드관리 > 서비스코드 관리";
	$P->title = "서비스코드 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = service_menu();
	$P->Navigation = "서비스관리 > 서비스코드관리 > 서비스코드 관리";
	$P->title = "서비스코드 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getSellerList() {
	global $db;

	$sql = "SELECT ccd.company_id, ccd.com_name
			FROM common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd  
			where  com_type IN ('S','A') and csd.company_id = csdv.company_id
			and csd.company_id = ccd.company_id 
			group by ccd.company_id order by csd.regdate desc ";
	$db->query($sql);
	//echo $sql;
	$sel_text="<select name='seller_list' id='seller_list' onChange='sel_seller(this)' >";
	if($db->total) {
		$sel_text.="<option value=''>브랜드선택</option>";
		$fetch=$db->fetchall();
		$fetch_cnt=count($fetch);
		for($i=0;$i<$fetch_cnt;$i++) {
			$sel_text.="<option value='".$fetch[$i]["company_id"]."' com_name='".$fetch[$i]["com_name"]."'>".$fetch[$i]["com_name"]."</option>";
		}
		
	} else {
		
	}
	$sel_text.="</select>";
	return $sel_text;
}

/*

CREATE TABLE IF NOT EXISTS `service_division` (
  `service_ix` int(4) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `parent_service_ix` int(4) unsigned default NULL COMMENT '상위카테고리인덱스값',
  `service_name` varchar(20) default NULL COMMENT '서비스명',
  `service_code` varchar(20) default NULL COMMENT '서비스코드',
  `depth` int(2) unsigned default '1' COMMENT '카테고리depth',
  `disp` char(1) default '1' COMMENT '사용여부',
  `vieworder` int(8) default '0' COMMENT '노출순서',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`service_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='서비스 코드정보'  ;
*/
?>