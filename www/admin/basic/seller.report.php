<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "거래처 리스트";
}else{
	$menu_name = "거래처 리스트";
}

$info_type = "seller_list";


include "seller_query.php";

$Contents = "
<div id='printArea'>
<table width='100%' border='0' align='center' >
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("거래처 리스트", "기초정보관리 > 거래처 리스트 ")."</td>
  </tr>
  		<tr>
				<td align='left' colspan=2 style='padding-bottom:15px;vertical-align:bottom;'>
					<table width='500px' border='0' cellspacing='0' cellpadding='0' >
					<tr>
						<td width='10%' height='31' valign='middle' style='color:#000000;border-bottom:3px solid #c5c5c5;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-right:20px;' nowrap>
							<img src='../v3/images/common/arrow_icon02.gif' align=absmiddle> 실시간 거래처 리스트 ( ".$title_add." )
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
<div id='printArea'>
<form name='list_frm'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class='m_td' ><font color='#000000'><b>순번</b></font></td>
    <td width='3%' align='center' class='m_td' ><font color='#000000'><b>등급</b></font></td>
    <td width='5%' align='center' class='m_td' ><font color='#000000'><b>거래시작일</b></font></td>
    <td width='5%' align='center' class=m_td ><font color='#000000'><b>업체코드</b></font></td>
    <td width='4%' align='center' class=m_td ><font color='#000000'><b>거래처유형</b></font></td>
    <td width='7%' align='center' class=m_td ><font color='#000000'><b>사업자명</b></font></td>
	<td width='5%' align='center' class=m_td ><font color='#000000'><b>사업자유형<br></b></font></td>
	<td width='5%' align='center' class=m_td ><font color='#000000'><b>국내외구분</b></font></td>
    <td width='6%' align='center' class=m_td ><font color='#000000'><b>대표전화</b></font></td>
	 <td width='6%' align='center' class=m_td ><font color='#000000'><b>대표이메일</b></font></td>
	  <td width='6%' align='center' class=m_td ><font color='#000000'><b>담당자<br>전화번호</b></font></td>
	   <td width='6%' align='center' class=m_td ><font color='#000000'><b>담당자<br>핸드폰번호</b></font></td>
	 <td width='6%' align='center' class=m_td ><font color='#000000'><b>여신한도</b></font></td>
	 <td width='6%' align='center' class=m_td><font color='#000000'><b>보증금</b></font></td>
  </tr>";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}else{
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}

		$mdb->fetch(0);
		$gp_name = $mdb->dt[gp_name];

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

        switch($db->dt[com_div]){
        case "P":
            $com_div = "개인(일반사업자)";
            break;
        case "R":
            $com_div = "법인";
            break;
        case "S":
            $com_div = "간이과세자";
            break;
        case "E":
            $com_div = "면세과세자";
            break;
        case "I":
            $com_div = "수출입업자";
            break;
        }

		$seller_array = unserialize($db->dt[seller_type]);

		switch($seller_array[sales_vendor]){
			case "1":
				$seller_type = "매출";
				break;
		}
		switch($seller_array[supply_vendor]){
			case "2":
				$seller_type .= " / 매입";
				break;
		}

		if($db->dt[seller_date]){
			$seller_array = explode (" ",$db->dt[seller_date]);
			$seller_date = $seller_array[0];
		}else{
			$seller_date = "-";
		}

        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;'><span title='".$db->dt[seller_level]."'>".$db->dt[seller_level]."</span></td>
            <td class='list_box_td' nowrap>".$seller_date."</td>
            <td class='list_box_td' >".$db->dt[company_code]."</td>
            <td class='list_box_td point' nowrap>".$seller_type."</td>
            <td class='list_box_td' >".$db->dt[com_name]."</a></td>
            <td class='list_box_td' >".$com_div."</font></td>
            <td class='list_box_td' >".$db->dt[nationality]."</td>
			<td class='list_box_td' >".$db->dt[com_phone]."</a></td>
            <td class='list_box_td' >".$db->dt[com_email]."</font></td>
            <td class='list_box_td' >".$db->dt[customer_phone]."</td>
			<td class='list_box_td' >".$db->dt[customer_mobile]."</td>
			<td class='list_box_td' >".number_format($db->dt[loan_price])." 원</td>
            <td class='list_box_td ctr point' >".number_format($db->dt[deposit_price])." 원</a></td>
            </tr>";
	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='16' align='center'>등록된 데이타가 없습니다.</td>
  </tr>";
}
$Contents .= "
</table>
</form>
</div>
<br><br><br>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
<tr>
	<td align='center'>
		<input type='button' name='print_page' value='인쇄하기' onclick='printDiv();'></td></tr>
</table>

";

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
function printDiv () { 
    window.onbeforeprint = beforeDivs; 
    window.onafterprint = afterDivs; 
    window.print();
} 

function beforeDivs () { 
   if (document.all) { 
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


</script>
";

$P = new ManagePopLayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->Navigation = "기초정보관리 > 거래처 관리 > $menu_name";
$P->title = "거래처 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>



