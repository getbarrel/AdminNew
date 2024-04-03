<?
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include("../class/layout.class");
$install_path = "../../include/";
include("SOAP/Client.php");

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}

$db = new Database;
//phpinfo();
//print_r($db);
//print_r($admininfo);
$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");
$db->fetch();

$phone = explode("-",$db->dt[phone]);
$fax = explode("-",$db->dt[fax]);

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);

$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

$payment_historys = $soapclient->call("getServicePaymentHistory",$params = array("mall_domain"=> $_SERVER["HTTP_HOST"],"company_id"=> $admininfo[mall_domain_id], "mall_domain_key"=> $admininfo["mall_domain_key"]),	$options);
//print_r($payment_historys);
$payment_historys = (array)$payment_historys;
/*
$service_infos["CMS"] = (array)$service_status["CMS"];
$service_infos["BASIC_ADD"] = (array)$service_status["BASIC_ADD"];
$service_infos["ADD"] = (array)$service_status["ADD"];
$service_infos["APP"] = (array)$service_status["APP"];
*/
//print_r($service_infos["BASIC_ADD"]);

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='200px'>
<col width='*'>
<col width='*'>
	<tr>
		<td align='left' colspan='3' style='padding-bottom:0px;'> ".GetTitleNavigation("결제내역", "마이서비스 > 결제내역 ")."</td>
	</tr>
</table>";


/*
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<tr>
		<td align='left' colspan=3 style='padding:3px 0px;'>
		".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>".$service_infos["CMS"]["SERVICE_NAME"]."</b></div>")."
		</td>
	</tr>
</table>";
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='list_table_box'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
	<tr bgcolor=#ffffff  height=30 align=center>
		<td class='s_td'>서비스구분</td>
		<td class='m_td'>신청일</td>
		<td class='m_td'>만료일</td>
		<td class='m_td'>남은기간</td>
		<td class='e_td'>관리</td>
	</tr>";

for($i=0; $i < count($payment_history);$i++){
	//$service_info[solution_div] ==
	if($admininfo[mall_type] == "F"){
		if($service_info[solution_div] == "SOHO"){
	$Contents01 .= "
		<tr bgcolor=#ffffff height=30>
			<td class='list_box_td point''> ".$service_info[sp_name]." </td>
			<td class='list_box_td list_bg_gray'' >
				".$service_info[sm_sdate]."
			</td>
			<td class='list_box_td ' >
				".$service_info[sm_edate]."
			</td>
			<td class='list_box_td list_bg_gray' >
				-
			</td>
			<td class='list_box_td' >";
				if($payment_history[si_status] == 'SR'){
					$Contents01 .= "<a href=\"javascript:alert('서비스 신청중입니다. 입금이 확인되면 바로 바로 사용 하실수 있습니다.[ 몰스토리 고객센타 : 1600-2028 ]로 문의 주시기 바랍니다.');\" ><b>서비스 신청중</b></a>";
				}else if($payment_history[service_type] == 9){
					$Contents01 .= "<a href='".$payment_history[service_apply_url]."' target=_blank >서비스 신청 바로가기</a>";
				}else if($payment_history[service_type] == 8 || $payment_history[pid] == ""){
					$Contents01 .= "<a href='".$payment_history[service_apply_url]."' >서비스 준비중</a>";

				}else{
					$Contents01 .= "<a href='#' onClick=\"PopSWindow('service_apply.php?si_ix=".$payment_history[si_ix]."&service_div=".$payment_history[service_div]."&solution_div=BIZ',700,400,'service_apply')\" >비즈형 전환</a>";
				}

	$Contents01 .= "
			</td>
		</tr>";
		}
	}else if($admininfo[mall_type] == "B"){
		if($payment_history[solution_div] == "BIZ"){
			$Contents01 .= "
			<tr bgcolor=#ffffff height=30>
				<td class='list_box_td point''> ".$payment_history[sp_name]." </td>
				<td class='list_box_td list_bg_gray'' >
					".$payment_history[sm_sdate]."
				</td>
				<td class='list_box_td ' >
					".$payment_history[sm_edate]."
				</td>
				<td class='list_box_td list_bg_gray' >
					-
				</td>
				<td class='list_box_td' >";
					if($payment_history[si_status] == 'SR'){
						$Contents01 .= "<a href=\"javascript:alert('서비스 신청중입니다. 입금이 확인되면 바로 바로 사용 하실수 있습니다.[ 몰스토리 고객센타 : 1600-2028 ]로 문의 주시기 바랍니다.');\" ><b>서비스 신청중</b></a>";
					}else if($payment_history[service_type] == 9){
						$Contents01 .= "<a href='".$payment_history[service_apply_url]."' target=_blank >서비스 신청 바로가기</a>";
					}else if($payment_history[service_type] == 8 || $payment_history[pid] == ""){
						$Contents01 .= "<a href='".$payment_history[service_apply_url]."' >서비스 준비중</a>";
					}else{
						$Contents01 .= "<a href='#' onClick=\"PopSWindow('service_apply.php?si_ix=".$payment_history[si_ix]."&service_div=".$payment_history[service_div]."&solution_div=".$payment_history[solution_div]."',700,400,'service_apply')\" >연장 신청</a>";
					}

		$Contents01 .= "
				</td>
			</tr>";
		}
	}else if($admininfo[mall_type] == "O"){
		if($payment_history[solution_div] == "OPEN"){
			$Contents01 .= "
			<tr bgcolor=#ffffff height=30>
				<td class='list_box_td point''> ".$payment_history[sp_name]." </td>
				<td class='list_box_td list_bg_gray'' >
					".$payment_history[sm_sdate]."
				</td>
				<td class='list_box_td ' >
					".$payment_history[sm_edate]."
				</td>
				<td class='list_box_td list_bg_gray' >
					-
				</td>
				<td class='list_box_td' >";
					if($payment_history[si_status] == 'SR'){
						$Contents01 .= "<a href=\"javascript:alert('서비스 신청중입니다. 입금이 확인되면 바로 바로 사용 하실수 있습니다.[ 몰스토리 고객센타 : 1600-2028 ]로 문의 주시기 바랍니다.');\" ><b>서비스 신청중</b></a>";
					}else if($payment_history[service_type] == 9){
						$Contents01 .= "<a href='".$payment_history[service_apply_url]."' target=_blank >서비스 신청 바로가기</a>";
					}else if($payment_history[service_type] == 8 || $payment_history[pid] == ""){
						$Contents01 .= "<a href='".$payment_history[service_apply_url]."' >서비스 준비중</a>";
					}else{
						$Contents01 .= "<a href='#' onClick=\"PopSWindow('service_apply.php?si_ix=".$payment_history[si_ix]."&service_div=".$payment_history[service_div]."&solution_div=".$payment_history[solution_div]."',700,400,'service_apply')\" >연장 신청</a>";
					}

		$Contents01 .= "
				</td>
			</tr>";
		}
	}
}
$Contents01 .= "
</table>";

*/

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<tr>
		<td align='left' colspan=3 style='padding:3px 0px;'>
		".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>결제내역</b></div>")."
		</td>
	</tr>
</table>";
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='list_table_box'>
<col width='20%'>
<col width='15%'>
<col width='15%'>
<col width='10%'>
<col width='10%'>
<col width='10%'>
<col width='10%'>
	<tr bgcolor=#ffffff  height=30 align=center>
		<td class='s_td'>서비스신청번호</td>
		<td class='m_td'>신청일</td>
		<td class='m_td'>서비스구분</td>
		<td class='m_td'>결제방법</td>
		<td class='m_td'>가격</td>
		<td class='m_td'>기간</td>
		<td class='m_td'>신청타입</td>
	</tr>";


for($i=0; $i < count($payment_historys[data]);$i++){
	//print_r($payment_history);
	$payment_history = (array)$payment_historys[data][$i];
	$Contents01 .= "
		<tr bgcolor=#ffffff height=30>
			<td class='list_box_td point''> ".$payment_history[oid]." </td>
			<td class='list_box_td list_bg_gray'' >
				".$payment_history[date]."
			</td>
			<td class='list_box_td ' >
				".$payment_history[service_div_text]." - ".$payment_history[solution_div]."
			</td>
			<td class='list_box_td list_bg_gray' >
				".$payment_history[method_text]."
			</td>
			<td class='list_box_td number' style=''>
				".number_format($payment_history[payment_price],0)."
			</td>
			<td class='list_box_td list_bg_gray' >
				".$payment_history[priod_txt]."
			</td>
			<td class='list_box_td' >
				".$payment_history[apply_type_text]."
			</td>
		</tr>";
}
$Contents01 .= "
</table>";






/*

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:10px;' >
<col width='200px'>
<col width='*'>
	<tr>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>기본 운영 서비스</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='list_table_box'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
	<tr bgcolor=#ffffff  height=30 align=center>
		<td class='s_td'>서비스구분</td>
		<td class='m_td'>사용가능사양</td>
		<td class='m_td'>사용내역</td>
		<td class='m_td'>만료일</td>
		<td class='e_td'>관리</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 도메인 </td>
		<td class='list_box_td list_bg_gray'' >
			2011-12-30
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			도메인 관리
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 신용카드(PG) </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 웹메일/웹하드 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 게시판(자료실) 용량 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 상품공유 기능(비즈) </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 온라인식별(실명인증) </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 전자세금계산서 서비스 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 무통장입금확인 서비스 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
</table>";


$Contents03 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='*' />
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:2px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>부가서비스</b> </div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='list_table_box'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
	<tr bgcolor=#ffffff  height=30 align=center>
		<td class='s_td'>서비스구분</td>
		<td class='m_td'>사용가능사양</td>
		<td class='m_td'>사용내역</td>
		<td class='m_td'>만료일</td>
		<td class='e_td'>관리</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> SMS 서비스 </td>
		<td class='list_box_td list_bg_gray'' >
			2011-12-30
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			<a href=''>서비스 신청</a>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 대량이메일 서비스 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 이미지호스팅 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 해외구매대행기능 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 국내구매대행기능 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 딥줌 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 인터넷팩스 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 세무시스템</td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
</table>";

$Contents06 = "
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' >
<col width='200px'>
<col width='250' />
<col width='*' />
	<tr>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>부가 Application</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;' class='list_table_box'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
<col width='20%'>
	<tr bgcolor=#ffffff  height=30 align=center>
		<td class='s_td'>소프트웨어이름</td>
		<td class='m_td'>사용가능사양</td>
		<td class='m_td'>사용내역</td>
		<td class='m_td'>만료일</td>
		<td class='e_td'>관리</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 업무관리 </td>
		<td class='list_box_td list_bg_gray'' >
			2011-12-30
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 웹메일 서비스 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> KMS </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> CMS </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 전자결제 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 메일링/SMS 서비스 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 재고관리 </td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
	<tr bgcolor=#ffffff height=30>
		<td class='list_box_td point''> 견적시스템</td>
		<td class='list_box_td list_bg_gray'' >
			미사용
		</td>
		<td class='list_box_td ' >
			-
		</td>
		<td class='list_box_td list_bg_gray' >
			-
		</td>
		<td class='list_box_td' >
			서비스 신청
		</td>
	</tr>
</table>
  ";
*/

$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='mallinfo.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
		<input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>
		<input name='mall_div' type='hidden' value='".$db->dt[mall_div]."'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";

$Contents = $Contents."<tr><td>".$Contents05."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents05_1."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents04."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents06."<br></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents07."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents08."<br></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents09."<br></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='basicinfo.js'></script>
<script language='javascript'>
function update_zipcode(){
	form = document.edit_form;
	form.action = './zip_act.php';
	form.act.value = 'zipcode';
	form.submit();
}
</script>
";

if($admininfo[mall_type] == "H"){
	$Contents = str_replace("쇼핑몰","사이트",$Contents);
}

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = myservice_menu();
$P->Navigation = "마이서비스 > 결제 내역";
$P->title = "결제 내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();




/*
create table admin_menus (
menu_code varchar(32) not null ,
menu_name varchar(255) null default null,
menu_path varchar(255) null default null,
auth_read enum('Y','N') null default 'Y',
auth_write enum('Y','N') null default 'Y',
shipping_company varchar(30) null default null,
primary key(menu_code));
*/
?>