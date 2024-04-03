<?
include("../class/layout.class");

$db = new Database;

$db->query("SELECT * FROM ".TBL_BBS_MANAGE_CONFIG." where bm_ix ='$bm_ix' ");
$db->fetch();
$board_name = $db->dt[board_name];
$board_ename = $db->dt[board_ename];

if($board_ename == 'qna' || $board_ename == 'qna_global' ){
    $add_info_area = "
    <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 안내문구 : </td>
		<td class='input_box_item'>
            <textarea type=text class='textbox' name='div_info_text' style='width:330px;margin:5px 0;'> ".$db->dt[div_info_text]." </textarea>
            <span class='small'>해당 안내문구는 1:1 문의 분류 선택 시 기본 설정 문구로 사용됩니다.</span>
		</td>
	  </tr>
    ";
}
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	  <tr >
		<td align='left' colspan=2 style='padding-bottom:10px;'> ".GetTitleNavigation("게시판 분류 관리", "게시판관리 > 게시판 분류 관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box' style='margin-top:3px;'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title' width='10%'>  분류타입 : </td>
	    <td class='input_box_item' width='*'>
	    	<input type=radio name='div_depth' value='1' id='div_depth_1' onclick=\"document.getElementById('parent_div_ix').disabled=true;\" checked><label for='div_depth_1'>1차분류</label>
	    	<input type=radio name='div_depth' value='2' id='div_depth_2' onclick=\"document.getElementById('parent_div_ix').disabled=false;\" ><label for='div_depth_2'>2차분류</label>
	    	".getFirstDIV($bm_ix)." <span class='small'>2차 분류 등록하기 위해서는 반드시 1차분류를 선택하셔야 합니다.</span>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 분류명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='div_name' value='".$db->dt[div_name]."' style='width:230px;'> <span class=small></span></td>
	  </tr>
	  ".$add_info_area."
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
		  <u>$board_name </u> 에 이용할 분류명을  입력해주세요
	</td>
</tr>
</table>
";



$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	  <tr>
	    <td align='left' colspan=7> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  분류 목록</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box' style='margin-top:3px;'>
	  <tr height=25 bgcolor=#efefef style='font-weight:bold'>
	    <td style='width:13%;' class='s_td'> NO </td>
		<td style='width:*;' class='m_td'> 분류명</td>
	    <td style='width:10%;' class='m_td'> 노출순서</td>
		<td style='width:10%;' class='m_td'> 등록게시물수</td>
	    <td style='width:10%;' class='m_td'> 사용유무</td>
	    <td style='width:18%;' class='m_td'> 등록일자</td>
	    <td style='width:18%;' class='e_td'> 관리</td>
	  </tr>";

if($board_ename!=""){
	if($db->dbms_type == "oracle"){
		$sql = 	"SELECT bdiv.div_ix,bdiv.parent_div_ix, bdiv.div_name, bdiv.div_depth, bdiv.view_order, bdiv.disp, bdiv.regdate, sum(case when bbs_div is NULL then 0 else 1 end) as  div_bbs_cnt ,
			sum(case when div_depth=1 then view_order else (SELECT view_order FROM ".TBL_BBS_MANAGE_DIV." WHERE div_ix=bdiv.parent_div_ix) end) as  view_order2
			FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.div_ix = bbs.bbs_div where bm_ix = '$bm_ix'
			group by bdiv.div_ix,bdiv.parent_div_ix, bdiv.div_name, bdiv.div_depth, bdiv.view_order, bdiv.disp, bdiv.regdate
			order by view_order2 asc, bdiv.div_depth asc, bdiv.view_order asc";//리스팅 순서를 정렬함 kbk 12/06/19
	}else{
		$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  div_bbs_cnt , case when div_depth = 1 then div_ix  else parent_div_ix end as div_order,
			case when div_depth=1 then view_order else (SELECT view_order FROM ".TBL_BBS_MANAGE_DIV." WHERE div_ix=bdiv.parent_div_ix) end as view_order2
			FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.div_ix = bbs.bbs_div where bm_ix = '$bm_ix'
			group by div_ix
			order by view_order2 asc, div_depth asc, view_order asc";//리스팅 순서를 정렬함 kbk 12/06/19
	}
}else{
	$sql = 	"SELECT bdiv.*, '0' as  div_bbs_cnt , case when div_depth = 1 then div_ix  else parent_div_ix end as div_order,
			case when div_depth=1 then view_order else (SELECT view_order FROM ".TBL_BBS_MANAGE_DIV." WHERE div_ix=bdiv.parent_div_ix) end as view_order2
			FROM ".TBL_BBS_MANAGE_DIV." bdiv where bm_ix = '$bm_ix'
			group by div_ix
			order by view_order2 asc, div_depth asc, view_order asc";
}

//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr>
			<td class='list_box_td list_bg_gray'>".($i+1)."</td>
		    <td class='list_box_td' style='text-align:left; padding-left:10px;'><span style='margin-left:".(30*($db->dt[div_depth]-1))."px;'></span>".$db->dt[div_name]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[view_order]."</td>
		    <td class='list_box_td'>".$db->dt[div_bbs_cnt]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td'>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
		    	<a href=\"javascript:updateBBSCategory('".$db->dt[div_ix]."','".$db->dt[div_depth]."','".$db->dt[parent_div_ix]."','".$db->dt[div_name]."','".nl2br($db->dt[div_info_text])."','".$db->dt[view_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteBBSCategory('delete','".$db->dt[div_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 분류가 없습니다. </td>
		  </tr>";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=5 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->

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
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='ccursor:pointer;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='board_category.act.php' method='post' onsubmit='return validate(this)' target='act'><input name='mmode' type='hidden' value='$mmode'><input name='act' type='hidden' value='insert'><input name='bm_ix' type='hidden' value='$bm_ix'><input name='div_ix' type='hidden' value=''>";
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
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >게시판 분류는 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>분류명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >게시물을 수정하시려면 게시판 타이틀을 클릭하시면 됩니다</td></tr>
	</table>
	";


	$help_text = HelpBox("게시판 분류관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
 function updateBBSCategory(div_ix,div_depth,parent_div_ix,div_name,div_info_text,view_order,disp){
 	var frm = document.div_form;

 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	$('textarea[name=div_info_text]').text(div_info_text.replace(/<br\s?\/?>/g,\"\\n\"));
	frm.view_order.value = view_order;

 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

	if(div_depth == '1'){
 		frm.div_depth[0].checked = true;
		document.getElementById('parent_div_ix').disabled=true;

 	}else{
 		frm.div_depth[1].checked = true;
		document.getElementById('parent_div_ix').disabled=false;
 	}


	for(i=0;i<frm.parent_div_ix.length;i++){
		if(frm.parent_div_ix.options[i].value == parent_div_ix){
			frm.parent_div_ix.options[i].selected = 'true';
		}
	}

}

 function deleteBBSCategory(act, div_ix){
 	if(confirm('해당카테고리  정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.div_form;
 		frm.act.value = act;
 		frm.div_ix.value = div_ix;
 		frm.submit();
 	}
}

function validate(frm) {
	if(frm.div_depth[1].checked) {
		if(frm.parent_div_ix.value=='') {
			alert('2차 분류 등록하기 위해서는 반드시 1차분류를 선택하셔야 합니다.');
			return false;
		}
	}
	if(frm.div_name.value.length<1) {
		alert('분류명을 입력해주세요.');
		frm.div_name.focus();
		return false;
	}
	return true;
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	//$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판목록 > 분류관리설정";
	$P->title = "분류관리설정";
	$P->NaviTitle = "분류관리설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판목록 > 분류관리설정";
	$P->title = "분류관리설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getFirstDIV($bm_ix, $selected=""){
	$mdb = new Database;

	if($mdb->dbms_type == "oracle"){
		$sql = 	"SELECT bdiv.div_ix, bdiv.div_name
				FROM ".TBL_BBS_MANAGE_DIV." bdiv
				where div_depth = 1 and bm_ix = '$bm_ix'
				group by bdiv.div_ix, bdiv.div_name";
	}else{
		$sql = 	"SELECT bdiv.*
				FROM ".TBL_BBS_MANAGE_DIV." bdiv
				where div_depth = 1 and bm_ix = '$bm_ix'
				group by div_ix ";
	}
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