<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

if($div_ix){
	$mdb->query("SELECT * FROM shop_display_brand_div where div_ix = '$div_ix' ");
	$mdb->fetch();

	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("브랜드 전시분류관리", "전시관리 > 브랜드 전시관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width=20%>
	  <col width=*>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'>
			 <b>분류명 <img src='".$required3_path."'></b>
	    </td>
		<td class='input_box_item'>
			<input type=text class='textbox2' name='div_name' value='".$mdb->dt[div_name]."' style='width:230px;' validation=true title='분류명'> <span class=small></span>
		</td>
	  </tr>";
if(false){
$Contents01 .= "
	  <tr bgcolor=#ffffff height=25>
	    <td class='input_box_title'> <b>카테고리 <img src='".$required3_path."'></b> </td>
		<!--td class='input_box_item'>".getCategoryList3("카테고리", "cid", "title='카테고리' validation=true ", 0, $mdb->dt[cid])." <span class=small></span></td-->
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $mdb->dt[cid])."</td>
					<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $mdb->dt[cid])."</td>
					<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $mdb->dt[cid])."</td>
					<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $mdb->dt[cid])."</td>
				</tr>
			</table>
		</td>
	  </tr>";
}
$Contents01 .= "
	  <tr bgcolor=#ffffff height=25>
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' ".(($mdb->dt[disp] == "" || $mdb->dt[disp] == 1) ? "checked":"" )." id='disp_1'><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ".(($mdb->dt[disp] == 0) ? "checked":"" )." id='disp_0'><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u>프로모션에 이용할 분류명을  입력해주세요
	</td>
</tr>
</table>
";



$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  분류 목록</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	    <col style='width:20%;'>
	    <col style='width:40%;'>
		<col style='width:10%;'>
	    <col style='width:15%;'>
	    <col style='width:15%;'>
	  <tr height=30 align=center>
	    <td class='s_td'> 분류명</td>
		<td class='m_td'> 카테고리명</td>
		<td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";




/*
$sql = 	"SELECT cmd.*, ci.cname
			FROM shop_display_brand_div cmd , shop_category_info ci
			where  cmd.cid = ci.cid ";
*/

$sql = 	"SELECT *	FROM shop_display_brand_div order by cid";

//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr  height=30 >
		    <td class='list_box_td point'><span style='width:".(30*$db->dt[div_depth])."px;'></span>".$db->dt[div_name]."</td>
			<!--td class='list_box_td'>".$db->dt[cname]."</td-->
			<td class='list_box_td'>".getCategoryPathByAdmin($db->dt[cid], $db->dt[depth])."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td'>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>
		    	<!--a href=\"javascript:updateBankInfo('".$db->dt[div_ix]."','".$db->dt[div_name]."','".$db->dt[div_bbs_cnt]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
		    	<a href=\"?div_ix=".$db->dt[div_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>

	    		<a href=\"javascript:deleteBankInfo('delete','".$db->dt[div_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		  </tr>  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 분류가 없습니다. </td>
		  </tr>";
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
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<form name='div_form' action='brand_display_div.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'><input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>
						<input type='hidden' name='cid2' value='".$mdb->dt[cid]."'>
						<input type='hidden' name='depth' value='".$mdb->dt[depth]."'>
						<input name='div_ix' type='hidden' value='$div_ix'>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table ></form>";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>분류명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >분류의 노출을 원하지 않으시면 사용안함으로 설정 하시면 됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 분류는 프로모션 상품 관리에서도 노출 되지 않습니다.</td></tr>
	</table>
	";


	$help_text = HelpBox("브랜드 전시분류관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

 function loadCategory(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = $('select[name='+sel.name+']').attr('depth');
	//alert('category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;

 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

 function deleteBankInfo(act, div_ix){
 	if(confirm(language_data['category_main_div.php']['A'][language])){//'해당카테고리  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.div_form;
 		frm.act.value = act;
 		frm.div_ix.value = div_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = display_menu();
	$P->Navigation = "전시관리 > 브랜드 전시관리 > 브랜드 전시분류관리";
	$P->NaviTitle = "브랜드 전시분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = display_menu();
	$P->Navigation = "전시관리 > 브랜드 전시관리 > 브랜드 전시분류관리";
	$P->title = "브랜드 전시분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
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