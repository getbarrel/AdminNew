<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database;

if($div_ix){
	$db->query("SELECT * FROM ".TBL_SNS_FREEPRODUCT_DIV." where div_ix = '$div_ix' ");
	$db->fetch();

	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("무료쿠폰 분류 관리", "소셜커머스 > 무표쿠폰 분류 관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
	 <col width='20%' />
	 <col width='80%' />
	  <tr>
	    <td class='input_box_title'><b>분류명 </b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='div_name' value='".$db->dt[div_name]."' style='width:230px;'> <span class=small></span>
	  </tr>
	  <tr>
	    <td class='input_box_title'><b>사용유무 </b></td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' ".(($db->dt[disp] == "" || $db->dt[disp] == 1) ? "checked":"" )."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ".(($db->dt[disp] == 0) ? "checked":"" )."><label for='disp_0'>사용하지않음</label>
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
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='margin-bottom:3px;'>
	  <tr>
	    <td align='left' colspan=5> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  분류 목록</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
		<col style='width:50px;'>
		<col style='width:150px;'>
		<col style='width:150px;'>
		<col style='width:150px;'>
		<col style='width:150px;'>

	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td''> NO</td>
		<td class='m_td'> 분류명</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";





$sql = 	"SELECT * FROM ".TBL_SNS_FREEPRODUCT_DIV;


//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
	$Contents02 .= "
		  <tr height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".($i+1)."</td>
			<td class='list_box_td'><span style='width:".(30*$db->dt[div_depth])."px;'></span>".$db->dt[div_name]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td'>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>";
		    	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents02 .= "<a href=\"?div_ix=".$db->dt[div_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				}else{
					$Contents02 .= "<a href=\"".$auth_update_msg."\" ><img src='../images/".$admininfo["language"]."/btc_modify.gif'></a> ";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents02 .= "<a href=\"javascript:deleteBankInfo('delete','".$db->dt[div_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a> ";
				}else{
					$Contents02 .= "<a href=\"".$auth_delete_msg."\" ><img src='../images/".$admininfo["language"]."/btc_del.gif'></a>";
				}
	$Contents02 .= "
		    </td>
		  </tr>
			";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=5>등록된 분류가 없습니다. </td>
		  </tr>
			  ";
}
$Contents02 .= "
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
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='free_goods_category.act.php' method='post'><input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>
						<input name='div_ix' type='hidden' value='$div_ix'>";
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
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>분류명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >분류의 노출을 원하지 않으시면 사용 안함으로 설정 하시면 됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 분류는 프로모션 상품 관리에서 또한 노출 되지 않는다.</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

	$help_text = HelpBox("SNS 무료쿠폰 분류 관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
function deleteBankInfo(act, div_ix){
 	if(confirm(language_data['sns_free_goods_category.php']['A'][language])){
	//'해당카테고리  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.div_form;
 		frm.act.value = act;
 		frm.div_ix.value = div_ix;
 		frm.submit();
 	}
}
 </script>
 ";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sns_menu();
	$P->Navigation = "소셜커머스 > 무료쿠폰 > 무료쿠폰 분류관리";
	$P->title = "무료쿠폰 분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

function getFirstDIV($bm_ix, $selected=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_promotion_div
			where disp=1 ";

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