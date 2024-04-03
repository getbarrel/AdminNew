<?
include("../class/layout.class");
include("category.lib.php");





$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("아이콘등록관리", "디자인관리 > 아이콘등록관리 ")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left'>
	 <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>아이콘추가하기</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='input_table_box' style='margin-top:3px;'>
	  <tr>
	    <td class='input_box_title' style='width:150px;'> <b>아이콘명 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
			<input type='text' class=textbox name='icon_name' style='width:200px' id='icon_name' value='' validation='true' title='아이콘명'>
	     </td>
	  </tr>
	  <tr >
	    <td class='input_box_title'> <b>아이콘파일 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=file class='textbox' name='icon_file' value='' style='width:230px;' validation=true title='아이콘파일'> </td>
	  </tr>
	  <tr  >
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	</table>";





$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;' ><img src='../image/title_head.gif' align=absmiddle> <b>아이콘목록</b></div>")."</td>
	  </tr>
	</table>
	<div style='width:100%;height:350px;'>
	<table width='100%' cellpadding=0 cellspacing=0 align='left' class='list_table_box' style='margin-top:3px;'>
	  <tr height=27 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td' style='width:20%; padding:0; text-align:center;' class='s_td'> 아이콘명</td>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='m_td'> 아이콘 이미지 파일</td>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='m_td'> 사용유무</td>
	    <td class='m_td' style='width:20%; padding:0; text-align:center;' class='m_td'> 등록일자</td>
	    <td class='e_td' style='width:20%; padding:0; text-align:center;' class='e_td'> 관리</td>
	  </tr>";
$max = 9; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}
$db = new Database;


$db->query("SELECT * FROM shop_icon ");
$total = $db->total;
$str_page_bar = page_bar($total, $page,$max, "&max=$max&mmode=$mmode","");

if($total){
	$db->query("SELECT * FROM shop_icon order by regdate desc limit $start , $max ");
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[icon_name]."</td>
		    <td class='list_box_td'><img src='".$admin_config[mall_data_root]."/images/icon/".$db->dt[idx].".gif' align='absmiddle' style='vertical-align:middle'></td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td'>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
			";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "<a href=\"javascript:updateIconInfo('".$db->dt[idx]."','".$db->dt[icon_name]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle></a> ";
			}else{
				$Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .= "<a href=\"javascript:deleteIconInfo('delete','".$db->dt[idx]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a> ";
			}else{
				$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
			$Contents02 .= "


		    </td>
		  </tr>	  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=5>등록된 아이콘이 없습니다. </td>
		  </tr>	  ";
}
$Contents02 .= "
	  <!--tr height=1><td colspan=5><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->
	  </table>
	  <div style='width:100%;text-align:center;padding-top:10px;clear:both;'>".$str_page_bar."</div>
	  </div>";







if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
	</table>
	";
}else{
	$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff ><td colspan=4 align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>
	";

}



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='bank_form' action='product_icon.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act' enctype='multipart/form-data'><input name='act' type='hidden' value='insert'><input name='idx' type='hidden' value=''>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>
 function updateIconInfo(idx,icon_name,disp){
 	var frm = document.bank_form;

 	frm.act.value = 'update';
 	frm.idx.value = idx;
 	frm.icon_name.value = icon_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	frm.icon_file.setAttribute('validation','false'); //수정인 경우 무조건 이미지 파일을 업로드하라고 나오는 것을 방지 kbk

}

 function deleteIconInfo(act, idx){
 	if(confirm('해당계좌 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.bank_form;
 		frm.act.value = act;
 		frm.idx.value = idx;
 		frm.submit();
 	}
}

 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "디자인관리 > 기타 디자인관리 > 아이콘관리";
	$P->NaviTitle = "아이콘관리";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
	$P = new LayOut();
	$P->addScript = $Script;
	//$P->strLeftMenu = design_menu();
	$P->strLeftMenu = design_menu("/admin",$category_str);
	$P->Navigation = "디자인관리 > 기타 디자인관리 > 아이콘관리";
	$P->title = "아이콘관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

create table ".TBL_SHOP_BANKINFO." (
bank_ix int(4) unsigned not null auto_increment  ,
bank_name varchar(20) null default null,
bank_number varchar(20) null default null,
bank_owner varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(bank_ix));
*/
?>