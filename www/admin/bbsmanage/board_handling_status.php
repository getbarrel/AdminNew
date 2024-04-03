<?
include("../class/layout.class");

$db = new Database;

$db->query("SELECT * FROM ".TBL_BBS_MANAGE_CONFIG." where bm_ix ='$bm_ix' ");
$db->fetch();
$board_name = $db->dt[board_name];
$board_ename = $db->dt[board_ename];

$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2 > ".GetTitleNavigation("게시판 처리상태 관리", "게시판관리 > 게시판 처리상태 관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 처리상태 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' style='margin-top:3px;'>
	  <col width=25%>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 처리상태명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='status_name' value='".$db->dt[status_name]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 노출순서 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='view_order' value='".$db->dt[view_order]."' style='width:80px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 처리상태명을  입력해주세요
	</td>
</tr>
</table>
";



$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=7> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u>  처리상태 목록</b></div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box' style='margin-top:3px;'>
	  <tr height=25 bgcolor=#efefef style='font-weight:bold'>
	    <td style='width:5%;' align='center' class='s_td'> NO </td>
		<td style='width:*;' align='center' class='m_td'> 처리상태명</td>
	    <td style='width:10%;' align='center' class='m_td'> 노출순서</td>
		<td style='width:10%;' align='center' class='m_td'> 등록게시물수</td>
	    <td style='width:10%;' align='center' class='m_td'> 사용유무</td>
	    <td style='width:18%;' align='center' class='m_td'> 등록일자</td>
	    <td style='width:18%;' align='center' class='e_td'> 관리</td>
	  </tr>";



if($db->dbms_type == "oracle"){
	$sql = "SELECT bdiv.status_ix, bdiv.bm_ix, bdiv.status_name, bdiv.view_order, bdiv.disp, bdiv.regdate, sum(case when bbs_div is NULL then 0 else 1 end) as  status_bbs_cnt
			FROM ".TBL_BBS_MANAGE_STATUS." bdiv left join bbs_".$board_ename." bbs on bdiv.status_ix = bbs.bbs_div where bm_ix = '$bm_ix'
			group by bdiv.status_ix, bdiv.bm_ix, bdiv.status_name, bdiv.view_order, bdiv.disp, bdiv.regdate
			order by view_order asc ";
}else{
	$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  status_bbs_cnt
			FROM ".TBL_BBS_MANAGE_STATUS." bdiv left join bbs_".$board_ename." bbs on bdiv.status_ix = bbs.bbs_div where bm_ix = '$bm_ix'
			group by status_ix
			order by view_order asc ";
}



//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30>
			<td align='center' class='list_box_td list_bg_gray'>".($i+1)."</td>
		    <td align=left class='list_box_td point'>".$db->dt[status_name]."</td>
			<td align='center' class='list_box_td list_bg_gray'>".$db->dt[view_order]."</td>
		    <td align='center' class='list_box_td '>".$db->dt[status_bbs_cnt]."</td>
		    <td align='center' class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td align='center' class='list_box_td '>".$db->dt[regdate]."</td>
		    <td align='center' class='list_box_td list_bg_gray'>
		    	<a href=\"javascript:updateBBShandling_status('".$db->dt[status_ix]."','".$db->dt[status_name]."','".$db->dt[view_order]."','".$db->dt[disp]."')\"><img src='../image/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteBBShandling_status('delete','".$db->dt[status_ix]."')\"><img src='../image/btc_del.gif' border=0></a>
		    </td>
		  </tr>  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 처리상태가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=7 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

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
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='ccursor:pointer;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='status_form' action='board_handling_status.act.php' method='post' onsubmit='return validate(this)' target='act'><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='bm_ix' type='hidden' value='$bm_ix'><input name='status_ix' type='hidden' value=''>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >게시판 처리상태는 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>처리상태명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >게시물을 수정하시려면 게시판 타이틀을 클릭하시면 됩니다</td></tr>
	</table>
	";


	$help_text = HelpBox("게시판 처리상태관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function updateBBShandling_status(status_ix,status_name,view_order,disp){
 	var frm = document.status_form;

 	frm.act.value = 'update';
 	frm.status_ix.value = status_ix;
 	frm.status_name.value = status_name;
	frm.view_order.value = view_order;

 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function deleteBBShandling_status(act, status_ix){
 	if(confirm('처리상태 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.status_form;
 		frm.act.value = act;
 		frm.status_ix.value = status_ix;
 		frm.submit();
 	}
}

function validate(frm) {

	if(frm.status_name.value.length<1) {
		alert('처리상태명을 입력해주세요.');
		frm.status_name.focus();
		return false;
	}
	return true;
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "HOME > 게시판관리 > 게시판 처리상태 관리";
	$P->NaviTitle = "게시판관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "HOME > 게시판관리 > 게시판 처리상태 관리";
	$P->title = "게시판 처리상태 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getFirstDIV($bm_ix, $selected=""){
	$mdb = new Database;

	$sql = 	"SELECT bms.*
			FROM ".TBL_BBS_MANAGE_STATUS." bms
			where  bm_ix = '$bm_ix'
			group by status_ix ";

	$mdb->query($sql);

	$mstring = "<select name='parent_status_ix' id='parent_status_ix' disabled>";
	$mstring .= "<option value=''>1차 처리상태</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[status_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[status_ix]."' selected>".$mdb->dt[status_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[status_ix]."'>".$mdb->dt[status_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

/*

CREATE TABLE IF NOT EXISTS `bbs_manage_status` (
  `status_ix` int(4) unsigned NOT NULL auto_increment COMMENT '상태 고유번호',
  `bm_ix` int(4) unsigned NOT NULL default '0' COMMENT '게시판 고유번호',
  `status_name` varchar(20) default NULL COMMENT '상태명',
  `view_order` int(4) NOT NULL default '1',
  `disp` char(1) default '1' COMMENT '사용 유무 (0:사용 안함, 1:사용함)',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`status_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='게시판별 상태정보 '  ;


*/
?>