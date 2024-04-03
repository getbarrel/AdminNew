<?
include("../class/layout.class");

$db = new Database;

if($rt_ix){
	$db->query("SELECT * FROM bbs_response_templet where rt_ix ='$rt_ix' ");
	$db->fetch();
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2 > ".GetTitleNavigation("게시판 답변 템플릿", "게시판관리 > 게시판 답변 템플릿 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>게시판 답변 템플릿 등록</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' style='margin-top:3px;'>
	  <col width=25%>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용게시판  </td>
		<td class='input_box_item' colspan=3>
			
			<select name='templet_div' class='select_box'>
				<option value='' ".($db->dt[templet_div]=="" ? "select" : "").">전체사용</option>
				<option value='P_Q&A' ".($db->dt[templet_div]=="P_Q&A" ? "select" : "").">상품Q&A</option>";

				$board_infos = BoardSelect($db->dt[templet_div],"array");

				foreach($board_infos as $bi){
					$Contents01 .= "<option value='".$bi[board_ename]."' ".($db->dt[templet_div]==$bi[board_ename] ? "select" : "").">".$bi[board_name]."</option>";
				}

			$Contents01 .= "
			</select>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 템플릿명  </td>
		<td class='input_box_item' colspan=3><input type=text class='textbox' name='templet_name' value='".$db->dt[templet_name]."' validation=true title='템플릿명' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 템플릿 내용  </td>
		<td class='input_box_item' colspan=3 style='padding:5px;'><textarea type=text class='textbox' name='templet_text'  validation=true title='템플릿내용'  style='width:98%;height:50px;padding:5px;'>".$db->dt[templet_text]."</textarea></td>
	  </tr>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'> 노출순서  </td>
		<td class='input_box_item'><input type=text class='textbox' name='view_order' value='".$db->dt[view_order]."' style='width:80px;' validation=true title='노출순서'  > <span class=small></span></td>
	    <td class='input_box_title'> 사용유무  </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=7 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>답변템플릿 목록</b></div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box' style='margin-top:3px;'>
	  <tr height=25 bgcolor=#efefef style='font-weight:bold'>
	    <td style='width:5%;' align='center' class='s_td'> NO </td>
		<td style='width:10%;' align='center' class='m_td'> 사용게시판</td>
		<td style='width:*;' align='center' class='m_td'> 템플릿명</td>
	    <td style='width:10%;' align='center' class='m_td'> 노출순서</td>
	    <td style='width:10%;' align='center' class='m_td'> 사용유무</td>
	    <td style='width:18%;' align='center' class='m_td'> 등록일자</td>
	    <td style='width:18%;' align='center' class='e_td'> 관리</td>
	  </tr>";





$db->query("SELECT brt.*, case when brt.templet_div='' then '전체사용' when brt.templet_div ='P_Q&A' then '상품Q&A' else bmc.board_name end as templet_div_name FROM bbs_response_templet brt left join bbs_manage_config bmc on (brt.templet_div=bmc.board_ename)  order by templet_div asc,view_order asc   ");
		



//echo $sql;
//$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30>
			<td align='center' class='list_box_td list_bg_gray'>".($i+1)."</td>
			<td align='center' class='' style=''>".$db->dt[templet_div_name]."</td>
		    <td align=left class='list_box_td point' style='text-align:left;'>".$db->dt[templet_name]."</td>
			<td align='center' class='list_box_td list_bg_gray'>".$db->dt[view_order]."</td>
		    <td align='center' class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td align='center' class='list_box_td '>".$db->dt[regdate]."</td>
		    <td align='center' class='list_box_td list_bg_gray'>";
		    	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                    $Contents02.="
				    <a href=\"?rt_ix=".$db->dt[rt_ix]."\"><img src='../image/btc_modify.gif' border=0></a>";
                }else{
                    $Contents02.="
                    <a href=\"".$auth_update_msg."\"><img src='../image/btc_modify.gif' border=0></a>";
                }
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                    $Contents02.="
                    <a href=\"javascript:deleteBoardTemplet('delete','".$db->dt[rt_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
                }else{
                    $Contents02.="
                    <a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0></a>";
                }
                $Contents02.="
		    </td>
		  </tr>  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 답변템플릿가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:20px 0px;'><input type='image' src='../image/b_save.gif' border=0 style='ccursor:pointer;border:0px;' ></td></tr>
    </table>
    ";
}else{
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:20px 0px;'><a href=\"".$auth_write_msg."\"><img src='../image/b_save.gif' border=0 style='ccursor:pointer;border:0px;' ></a></td></tr>
    </table>
    ";
}


$Contents = "<table width='100%' border=0 cellpadding=0 cellspacing=0 >";
$Contents = $Contents."<form name='templet_form' action='board_response_templet.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='".$act."'><input name='rt_ix' type='hidden' value='$rt_ix'>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
//$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>답변템플릿명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >게시물을 수정하시려면 게시판 타이틀을 클릭하시면 됩니다</td></tr>
	</table>
	";


	$help_text = HelpBox("게시판 답변템플릿관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function UpdateBoardTemplet(rt_ix,templet_name,view_order,disp){
 	var frm = document.templet_form;

 	frm.act.value = 'update';
 	frm.rt_ix.value = rt_ix;
 	frm.templet_name.value = templet_name;
	frm.view_order.value = view_order;

 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function deleteBoardTemplet(act, rt_ix){
 	if(confirm('해당 게시판 답변 템플릿  정보를 정말로 삭제하시겠습니까?')){
 		/*
		var frm = document.templet_form;
 		frm.act.value = act;
 		frm.rt_ix.value = rt_ix;
 		frm.submit();
		*/
		window.frames['act'].location.href= 'board_response_templet.act.php?act='+act+'&rt_ix='+rt_ix;
 	}
}

 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	if($page_type == "cscenter"){
		$P->strLeftMenu = cscenter_menu();
		$P->Navigation = "고객센타 > 게시판 답변 템플릿";
	}else{
		$P->strLeftMenu = bbsmanage_menu();
		$P->Navigation = "HOME > 게시판관리 > 게시판 답변 템플릿";
	}
	$P->NaviTitle = "게시판관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	if($page_type == "cscenter"){
		$P->strLeftMenu = cscenter_menu();
		$P->Navigation = "고객센타 > 게시판 답변 템플릿";
	}else{
		$P->strLeftMenu = bbsmanage_menu();
		$P->Navigation = "HOME > 게시판관리 > 게시판 답변 템플릿";
	}
	$P->title = "게시판 답변 템플릿";	
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE IF NOT EXISTS `bbs_response_templet` (
  `rt_ix` int(4) unsigned NOT NULL auto_increment COMMENT '상태 고유번호',
  `templet_name` varchar(20) default NULL COMMENT '템플릿명',
  `templet_text` mediumtext default NULL COMMENT '템플릿내용',
  `view_order` int(4) NOT NULL default '1' COMMENT '노출순서',
  `disp` char(1) default '1' COMMENT '사용 유무 (0:사용 안함, 1:사용함)',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY  (`rt_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='게시판 답변 템플릿정보 '  ;


*/
?>