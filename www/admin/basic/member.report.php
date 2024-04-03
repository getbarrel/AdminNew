<?
include("../class/layout.class");
include("./company.lib.php");
//auth(9);

$db = new Database;
$mdb = new Database;

$page_type = "member_report";

include "member_query.php";


if($info_type == "member_list"){
	$title_add = "전체";
}else if($info_type == "member_resign"){
	$title_add = "퇴사";
}else if($info_type == "member_lump"){
	$title_add = "전체";
}else{
	$title_add = "전체";
}


$Contents = "
<div id='printArea'>
<table width='100%' border='0' align='center' >
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("전체사원리스트", "기초정보관리 > 본사관리 ")."</td>
  </tr>
  		<tr>
				<td align='left' colspan=2 style='padding-bottom:15px;vertical-align:bottom;'>
					<table width='500px' border='0' cellspacing='0' cellpadding='0' >
					<tr>
						<td width='10%' height='31' valign='middle' style='color:#000000;border-bottom:3px solid #c5c5c5;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-right:20px;' nowrap>
							<img src='../v3/images/common/arrow_icon02.gif' align=absmiddle> 실시간 사원리스트 ( ".$title_add." )
						</td>
						<td width='90%' align='right' valign='middle' style='border-bottom:3px solid #c5c5c5;'>
							&nbsp;$navigation
						</td>
					</tr>
					<tr height=30><td colspan=2>현황일자 : ".date("Y.m.d / H:i:s")."</td></tr>
					</table>
				</td>
				<td align='right' colspan=2 style='padding-bottom:15px;width:330px;'>
					<table cellpadding=0 cellspacing=0 border=0 width=330 align=right  style='border-collapse:separate; border-spacing:1px; background:#c5c5c5;border:1px ;'>
							<col width='33%'>
							<col width='33%'>
							<col width='33%'>						
							<tr height='30'>		
								<td style='padding:0px 5px;height:1px;background:#ffffff repeat-x bottom;height:1px; text-align:center; background-color:#efefef;'><b> </b></td>		
								<td style='padding:0px 5px;height:1px;background:#ffffff repeat-x bottom;height:1px; text-align:center; background-color:#efefef;' ><b> </b></td>		
								<td style='padding:0px 5px;height:1px;background:#ffffff repeat-x bottom;height:1px; text-align:center; background-color:#efefef;'><b> </b></td>						
							</tr>
							<tr height='60'>		
								<td style='padding:0px 5px;height:1px;background:#ffffff repeat-x bottom;height:1px; text-align:center;'> </td>		
								<td style='padding:0px 5px;height:1px;background:#ffffff repeat-x bottom;height:1px; text-align:center;'> </td>		
								<td style='padding:0px 5px;height:1px;background:#ffffff repeat-x bottom;height:1px; text-align:center;'> </td>
							</tr>
					</table>
				</td>
			</tr>
</table><br>
</form>
";

$Contents .= "
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'  style='border-collapse:separate; border-spacing:1px; background:#c5c5c5;border:1px ;'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' style='color:#000000;background-color:#f2f2f2; font-weight:bold; text-align:center;' rowspan='2'><font color='#000000'><b>순번</b></font></td>
    <td width='5%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;' rowspan='2'><font color='#000000'><b>사원코드</b></font></td>
    <td width='6%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;' rowspan='2'><font color='#000000'><b>입사일<br>(근무년수)</b></font></td>
    <td width='5%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;' rowspan='2'><font color='#000000'><b>이름</b></font></td>

    <td width='8%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;' rowspan='2'><font color='#000000'><b>근무사업장</b></font></td>
    <td width='10%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;' colspan='4'><font color='#000000'><b>부서및직책</b></font></td>

	<td width='6%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;' rowspan='2'><font color='#000000'><b>연락처</b></font></td>
	<td width='6%' align='center' style=' color:#000000;background-color:#f2f2f2; font-weight:bold;text-align:center;' rowspan='2'><font color='#000000'><b>이메일</b></font></td>
  </tr>";

$Contents .= "
	<tr height='28' bgcolor='#ffffff'>
<!--
		<td width='5%' align='center' class=m_td ><font color='#000000'><b>1DEPTH</b></font></td>
		<td width='5%' align='center' class=m_td><font color='#000000'><b>2DEPTH</b></font></td>
		<td width='5%' align='center' class=m_td><font color='#000000'><b>3DEPTH</b></font></td>
		<td width='5%' align='center' class=m_td><font color='#000000'><b>4DEPTH</b></font></td>-->

		<td width='5%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;'><font color='#000000'><b>부서그룹</b></font></td>
		<td width='5%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;'><font color='#000000'><b>부서</b></font></td>
		<td width='5%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;'><font color='#000000'><b>직위</b></font></td>
		<td width='5%' align='center' style=' color:#000000;background-color:#f2f2f2;  font-weight:bold; text-align:center;'><font color='#000000'><b>직책</b></font></td>
		
	</tr>
";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/
		
        if($db->dt[is_id_auth] != "Y"){
            $is_id_auth = "미인증";
        }else{
            $is_id_auth = "";
        }

        switch($db->dt[authorized]){

        case "Y":
            $authorized = "승인";
            break;
        case "N":
            $authorized = "승인대기";
            break;
        case "X":
            $authorized = "승인거부";
            break;
        default:
            $authorized = "알수없음";
            break;
        }

        switch($db->dt[mem_type]){

        case "M":
            $mem_type = "일반";
            break;
        case "C":
            $mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
            break;
        case "F":
            $mem_type = "외국인";
            break;
        case "S":
            $mem_type = "셀러";
            break;
        case "A":
            $mem_type = "관리자";
            break;
        case "MD":
            $mem_type = "MD";
            break;
        default:
            $mem_type = "일반";
            break;
        }

		
        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".$no."</td>
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' style='padding:0px 5px;'>".$db->dt[mem_code]."</td>
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' nowrap>".$db->dt[join_date]."</td>
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' ><a href='javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')' style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
			
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' nowrap>".$db->dt[com_name]."</td>
            <!--<td class='list_box_td' >".getCompanyname($db->dt[relation_code],9)."</td>
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".getCompanyname($db->dt[relation_code],13)."</td>
            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".getCompanyname($db->dt[relation_code],17)."</td>-->

            <td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' nowrap>".getGroupname('group',$db->dt[com_group])."</td>
			<td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".getGroupname('department',$db->dt[department])."</td>
			<td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".getGroupname('position',$db->dt[position])."</td>
			<td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".getGroupname('duty',$db->dt[duty])."</td>
			<td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".$db->dt[pcs]."</td>
			<td style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center' >".$db->dt[mail]."</td>

            <!--<td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>-->
			 </tr>
      ";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50 >
    <td colspan='11' align='center' style='padding:0px 5px;height:1px;background:#ffffff  repeat-x bottom;height:1px; text-align:center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}

$Contents .= "

</form>
</div>
</table>
<br><br><br>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
<tr>
	<td align='center'>
		<input type='button' name='print_page' value='인쇄하기' onclick='printDiv();'></td></tr>
</table>
";


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>

</form>";

$Script .="
<script language='javascript'>
var tempHtmlContent; 
var initBody ;

function beforePrint() {
	initBody = document.body.innerHTML;
}

function afterPrint() {
	document.body.innerHTML = initBody;
}
function printDiv (){
	window.onbeforeprint = beforeDivs; 
	window.onafterprint = afterDivs; 
	window.print(); 
}
function beforeDivs(){
	if(document.all) { 
		var rng = document.body.createTextRange( ); 
		if (rng!=null) { 
			//alert(rng.htmlText); 
			tempHtmlContent = rng.htmlText; 
			rng.pasteHTML('<table border=0 align=center><tr><td align=center>'+ document.all('printArea').innerHTML + '</td></tr></table>'); 
		}
	}
}
function afterDivs () { 
	if (document.all) { 
		var rng = document.body.createTextRange( ); 
		if (rng!=null) { 
			rng.pasteHTML(tempHtmlContent); 
		}
	}
}

</script>";

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
$P->title = "전체사원리스트";
$P->strContents = $Contents;
$P->OnloadFunction = "";
echo $P->PrintLayOut();

?>