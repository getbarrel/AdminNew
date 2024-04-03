<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] == ""){
	header("Location:/admin/");
}
if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$globalInfo = getGlobalInfo();

//print_r($globalInfo);

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2''> ".GetTitleNavigation("글로벌설정", "글로벌 > 글로벌설정")."</td>
	</tr>
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>글로벌설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>글로벌 사용유무 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='global_use' id='global_use_n' value='N' ".($globalInfo[global_use] =="N" ? "checked":"").">
		<label for='global_use_n'>사용안함</label>
		<input type='radio' name='global_use' id='global_use_y'  value='Y' ".($globalInfo[global_use] == "Y" ? "checked":"").">
		<label for='global_use_y'>사용</label> 
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>번역 요소별 설정 - 상품명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=radio name='global_pname_type' id='global_pname_type_d' value='D' ".($globalInfo[global_pname_type] == "D" ? "checked":"")." />
			<label for='global_pname_type_d'>직접입력</label>
			<input type=radio name='global_pname_type' id='global_pname_type_a' value='A' ".($globalInfo[global_pname_type] == "A" ? "checked":"")." />
			<label for='global_pname_type_a'>상품번역사전</label>
			<input type=radio name='global_pname_type' id='global_pname_type_c' value='C' ".($globalInfo[global_pname_type] == "C" ? "checked":"")." disabled/>
			<label for='global_pname_type_c'>카테고리명-상품코드(개발중)</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>번역 요소별 설정 - 옵션명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=radio name='global_oname_type' id='global_oname_type_d' value='D' ".($globalInfo[global_oname_type] == "D" ? "checked":"")." />
			<label for='global_oname_type_d'>직접입력</label>
			<input type=radio name='global_oname_type' id='global_oname_type_a' value='A' ".($globalInfo[global_oname_type] == "A" ? "checked":"")." />
			<label for='global_oname_type_a'>상품번역사전</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>번역 요소별 설정 - 가격 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=radio name='global_price_type' id='global_price_type_n' value='N' ".($globalInfo[global_price_type] == "N" ? "checked":"")." />
			<label for='global_price_type_n'>미사용</label>
			<input type=radio name='global_price_type' id='global_price_type_d' value='D' ".($globalInfo[global_price_type] == "D" ? "checked":"")." />
			<label for='global_price_type_d'>개별입력</label>
			<input type=radio name='global_price_type' id='global_price_type_e' value='E' ".($globalInfo[global_price_type] == "E" ? "checked":"")." disabled/>
			<label for='global_price_type_e'>환율(개발중)</label>
		</td>
	</tr>
	</table>
	<!--table width='100%' border='0'>
		<tr>
			<td align='left' style='line-height:120%;'>
				※ <span class='small'> 전체 인센티브 설정은 <b>회원이 리셀러 신청할때 일괄적</b>으로 적용됩니다.</span><br>
				※ <span class='small'> 인센티브 설정은 리셀러에게 인센티브를 어떤 정책으로 줄지 설정해주는 곳입니다.</span><br>
				※ <span class='small'> 신규 가입자 인센티브 & 매출액인센티브 사용 안할시 리셀러 화면에도 자동으로 노출하지 안습니다. </span>
			</td>
			
		</tr>
	</table-->
	";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  도메인, 도메인 아이디, 도메인 key 등은 몰스토리에서 발급해드리는 사항이므로 변경이 불가능합니다.<br>
		  <u>상업적인 목적으로 상점을 운영</u>하기 위해서는 정식 <b>도메인 key</b>를 발급 받아 사용하셔야만 상점을 정상적으로 운영하실수 있습니다.
	</td>
</tr>
</table>
";
*/

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='globalinfo.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'><!-- target='act'-->";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >신규가입자 인센티브 사용함 : 금액 입력시 즉시 반영 ( 한번 입력된 주민번호/ 1개월간 탈퇴후 가입불가 )</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >매출액의 VAT를 빼고 적용</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >인센티브 현금 지급은 익월의 지급일에 적용 ( 다음달에 지급 )</td></tr>
	<tr><td valign=top></td><td class='small' style='line-height:120%' >적립금 & 예치금은 지급일에 자동 지급 </td></tr>
</table>
";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

//$Contents .=  HelpBox("글로벌설정", $help_text);

//$Contents = "<div style=height:1000px;'></div>";


//$Script = "<script language='javascript' src='reseller_rule.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = global_menu();
$P->Navigation = "글로벌 > 글로벌설정";
$P->title = "글로벌설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>