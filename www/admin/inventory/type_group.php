<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once("../inventory/inventory.lib.php");

$db = new Database;
$Script = "
<script type=\"text/javascript\">
<!--

	function CodeSubmit(frm,mode)
	{
		frm.mode.value = mode;
		if(CheckFormValue(frm)){
			frm.submit();
		}

	}

	function LoadingCode(dt_ix,type_div,type_code,type_name,disp)
	{
		
		document.forms['codeform'].dt_ix.value = dt_ix;
		document.forms['codeform'].type_code.value = type_code;
		document.forms['codeform'].type_name.value = type_name;
		document.forms['codeform'].mode.value = 'update';
		document.forms['codeform'].disp[disp].checked = true;
		
		/*
		for(i=0;i < document.forms['codeform'].type_div.length;i++){
			//alert(type_div.indexOf(document.forms['codeform'].type_div[i].value));
			if(type_div.indexOf(document.forms['codeform'].type_div[i].value) != -1){
				document.forms['codeform'].type_div[i].checked = true;
			}
		}
		*/

		$('.type_div').each(function(){
			if(type_div.indexOf($(this).val()) != -1){
				$(this).attr('checked',true);
			}else{
				$(this).attr('checked',false);
			}
		});

	}



	function code_del(dt_ix,type_name){
		if(confirm(type_name + '을(를) 삭제하시겠습니까?')){
			window.frames['act'].location.href='type_group.act.php?mode=delete&dt_ix='+dt_ix;
		}
	}


//-->
</script>
";


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr>
		<td colspan=2>
			".GetTitleNavigation($type_title." 목록", "재고관리 > 기초정보 관리 > ".$type_title." 추가하기" )."
		</td>
	  </tr>
	  <tr>
			<td align='left' colspan=2 style='padding-bottom:14px;'>
				<div class='tab'>
					<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_00'  ".(substr_count($_SERVER["REQUEST_URI"],"input_delivery_type.php") ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='input_delivery_type.php'\">입고유형 관리</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<table id='tab_01'  ".(substr_count($_SERVER["REQUEST_URI"],"output_delivery_type.php") ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='output_delivery_type.php'\">출고유형 관리</td>
										<th class='box_03'></th>
									</tr>
								</table>
							</td>
							<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
								$Contents01 .= "
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b>".$type_title." 목록</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15%'>
	  <col width='35%'>
		<tr bgcolor=#ffffff height=30>
			<td class='input_box_title'> <b>".$type_title." 분류 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
			".getTypeDiv($type,$type_div, "type_div", "", "checkbox")."
			</td>
			<td class='input_box_title'> <b>".$type_title."명 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
			<input type=text class='textbox point_color' name=type_name size=41 validation=true title='".$type_title."명' style='width:200px;'>
			</td>
		</tr>
		<tr bgcolor=#ffffff height=30>
			<td class='input_box_title'> <b>".$type_title."코드<img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
			<input type=text class='textbox point_color' name=type_code size=41 validation=true title='".$type_title."코드' style='width:200px;'>
			</td>	
			<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=true title='사용유무'><label for='disp_0'>미사용</label>
				<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=true title='사용유무' checked><label for='disp_1'>사용</label>
			</td>
		</tr>
	  </table>";

/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 자동차제조사명을  입력해주세요
	</td>
</tr>
</table>
";*/

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents02 = "
	<div style='width:100%;margin-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b>".$type_title." 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=10%>
		<col width='*'>
	    <col width=15%>
	    <col width=10%>
		<col width=10%>
		<col width=15%>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 번호</td>
		<td class='m_td'> ".$type_title." 분류</td>
		<td class='m_td'> ".$type_title." 코드</td>
	    <td class='m_td'> ".$type_title." 이름</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";

if($search_text == ""){
	$db->query("SELECT * FROM inventory_type where type='".$type."' order by regdate desc ");
}else{
	if($search_type == "type_name"){
		$db->query("SELECT * FROM inventory_type where type='".$type."' and $search_type LIKE '%$search_text%' order by regdate desc  ");
	}else{
		$db->query("SELECT * FROM inventory_type where type='".$type."' and $search_type = '$search_text' order by regdate desc ");
	}
}

$total = $db->total;
/*
if($search_text == ""){
	$db->query("SELECT * FROM inventory_type where type='".$type."' order by regdate desc  limit $start, $max");
}else{
	if($search_type == "type_name"){
		$db->query("SELECT * FROM inventory_type where type='".$type."' and $search_type LIKE '%$search_text%' order by regdate desc limit $start,$max");
	}else{
		$db->query("SELECT * FROM inventory_type where type='".$type."' and $search_type = '$search_text' order by regdate desc limit $start,$max");
	}
}
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

	$no = $total - ($page - 1) * $max - $i;

	if($db->dt[disp] == 1){
		$display_string = "사용";
	}else{
		$display_string = "사용안함";
	}

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>

			<td class='list_box_td list_bg_gray'>".$no."</td>
			<td class='list_box_td' style='padding-left:20px;'>".getTypeDiv($type,$db->dt[type_div], "type_div", "", "text")."</td>
			<td class='list_box_td point' style='padding-left:20px;'>".$db->dt[type_code]."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[type_name]."</td>
		    <td class='list_box_td '>".$display_string."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	if($db->dt[is_basic] !='1'){
		$Contents02 .= "
		    	<a href=\"javascript:LoadingCode('".$db->dt[dt_ix]."','".$db->dt[type_div]."','".$db->dt[type_code]."','".$db->dt[type_name]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 title='수정하기'></a>";
	}else{
		//$Contents02 .= "기본";
	}
}else{
	$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 title='수정하기'></a>";
}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
	if($db->dt[is_basic] !='1'){
		$Contents02 .= "
	    		<a href=\"javascript:code_del('".$db->dt[dt_ix]."','".$db->dt[type_name]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 title='삭제'></a>";
	}else{
		$Contents02 .= "";
	}
}else{
	//$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 title='삭제'></a>";
}
	$Contents02 .= "
		    </td>
		  </tr>";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 ".$type_title." 목록이 없습니다. </td>
		  </tr>";
}
$Contents02 .= "

	  </table>";


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
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

$Contents = "<form name='codeform' action='./type_group.act.php' method='post'  onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target=act><input type=hidden name=mode value=insert><input type=hidden name=dt_ix value=''><input type=hidden name=type value='".$type."'><input type='hidden' name='mmode' value='$mmode'>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table>";
$Contents = $Contents."</form>";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->Navigation = "재고관리 > 기초정보 관리 > ".$type_title." 목록";
	$P->title = $type_title." 목록";
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->Navigation = "재고관리 > 기초정보 관리 > ".$type_title." 목록";
	$P->title = $type_title." 목록";
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}

/*

CREATE TABLE IF NOT EXISTS `inventory_type` (
  `type_code` int(4) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `type` enum('I','O') NULL DEFAULT NULL COMMENT '구분 I:입고유형,O:출고유형',
  `type_name` varchar(100) DEFAULT NULL COMMENT '이름',
  `disp` char(1) DEFAULT '0' COMMENT '사용여부',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`type_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고관리 분류그룹' AUTO_INCREMENT=1 ;

*/

?>