<?
include("../class/layout.class");
require_once("./GetErrString.php"); // 에러메세지 처리파일


		$client = new nusoap_client("https://testws.baroservice.com:8010/ti.asmx?WSDL", true);

		$client->xml_encoding = "UTF-8";
		$client->soap_defencoding = "UTF-8";
		$client->decode_utf8 = false;

$db = new Database;
$mdb = new Database;
$db2 = new Database;
$help_text = "	-  세금계산서발행 목록입니다. <br>
		";
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	$birDate = $birYY.$birMM.$birDD;
}
$Script = "
<script type='text/javascript'>
function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');
}

function init_date(FromDate,ToDate) {
	var frm = document.searchmember;


	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}


	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}

}



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}


	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
}



function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	init_date(FromDate,ToDate);

}

function clearAll(frm){
		for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.oid.length;i++){
				frm.oid[i].checked = true;
		}
}

function fixAll(frm){

	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}
</script>
";
$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));
$mstring ="

		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("세금계산서 발행내역", "주문관리 > 세금계산서 발행내역")."</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' style='width:100%;' align=left>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' valign=top>
										<TABLE height=0 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
											<TR>
												<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
													<table border=0 cellpadding=0 cellspacing=0 width='100%'>
														<tr height=1><td colspan=4 class='dot-x'></td></tr>
														<tr height=27>
														  <th bgcolor='#efefef' align=center><label for='regdate'>요청일자</label></th>
														  <td align=left style='padding-left:5px;'>
															<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
																<tr>
																	<TD width=190 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
																	<TD width=20 align=center> ~ </TD>
																	<TD width=190 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
																	<TD>
																		<a href=\"javascript:select_date('$today','$today',1);\"><img src='../image/b_btn_s_today.gif'></a>
																		<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../image/b_btn_s_yesterday.gif'></a>
																		<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../image/b_btn_s_1week01.gif'></a>
																		<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../image/b_btn_s_15day01.gif'></a>
																		<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../image/b_btn_s_1month01.gif'></a>
																		<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../image/b_btn_s_2month01.gif'></a>
																		<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../image/b_btn_s_3month01.gif'></a>
																	</TD>
																</tr>
															</table>
														  </td>
														</tr>
														<tr height=1><td colspan=4 class='dot-x'></td></tr>
														<tr>
															<th bgcolor='#efefef' width='150'>발급유무</th>
															<td >
																<table>
																	<tr>
																		<td>
																			<input type='radio' name='tax_yn' id='tax_yn_' value='' ".($tax_yn == "" ? "checked":"")."><label for='tax_yn_'>전체</label>
																			<input type='radio' name='tax_yn' id='tax_yn_c' value='C' ".($tax_yn == "C" ? "checked":"")."><label for='tax_yn_c'>발급완료 </label>
																			<input type='radio' name='tax_yn' id='tax_yn_y'  value='Y' ".($tax_yn == "Y" ? "checked":"")."><label for='tax_yn_y'>발급대기</label>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr height=1><td colspan=4 class='dot-x'></td></tr>
														<tr>
															<th bgcolor='#efefef' width='150'>조건검색</th>
															<td >
																<table>
																	<tr>
																		<td>
																			<select name='search_type'>
																				<option value='name_comname'>주문자명+사업자명</option>
																				<option value='oid'>주문번호</option>
																				<option value='com_number'>사업자번호</option>
																			</select> <input type='text' name='search_text' size=20>
																		</td>
																		<td>
																		<!--사업자 번호 검색시 예시 : 111-11-11111-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</TD>
											</TR>

										</TABLE>
									</td>
									<th class='box_06'></th>
								</tr>
								<tr>
									<th class='box_07'></th>
									<td class='box_08'></td>
									<th class='box_09'></th>
								</tr>
							</table>
						</td>
					</tr>
					<tr >
						<td colspan=3 align=center  style='padding:10 0 0 0'>
							<input type='image' src='../image/bt_search.gif' border=0>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<form name='taxbill_form' method='post' action='taxbill.act.php'>
		<input type='hidden' id='oid'>
		<input type='hidden' name='act' value='select_sendtonts'>
		<tr>
			<td>
			".PrintTaxList()."
			</td>
		</tr>
		</form>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("세금계산서 관리", $help_text)."</td></tr>";
$mstring .="</table>";

$Contents = $mstring;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "HOME > 주문관리 > 세금계산서 발행내역";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintTaxList(){
	global $db, $mdb, $db2,$admininfo,$page,$nset,$tax_yn,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$orderby,$ordertype,$search_type,$search_text,$client;

	if ($orderby != "" || $ordertype != ""){
		$orderby_str = " order by $orderby $ordertype ";
	}else{
		$orderby_str = " order by regdate desc ";
	}

	if($tax_yn == "Y"){
		$where1 .= " and taxsheet_yn = 'Y' ";
	}else if($tax_yn == "C"){
		$where1 .= " and taxsheet_yn = 'C' ";
	}

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

	if($startDate != "" && $endDate != ""){
		$where1 .= " and  MID(replace(o.date,'-',''),1,8) between  $startDate and $endDate ";
	}
	if($search_text != ""){
		if($search_type == "name_comname"){
			$where1 .= " and (name LIKE '%$search_text%' or m.com_name LIKE '%$search_text%') ";
		}else{
			$where1 .= " and $search_type LIKE '%$search_text%' ";
		}
	}
	$sql = "select *
		from shop_taxbill t
		where tax_no <> ''
		$where1
		";
	//echo $sql;
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% bgcolor=silver>";
	$mString = $mString."<tr height=25><td colspan=5 bgcolor=#ffffff align=left>".$total." 건</td><td bgcolor=#ffffff align=left><a href='./taxissue_write.php'><img src='../images/btn/btn_billwrite.gif'></a></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
							<td class='s_td' width='30' align=center><input type='checkbox' name='all_fix' id='all_fix' value='Y' onclick=\"fixAll(document.taxbill_form)\"></td>
							<td class=m_td width='80'><a href='?orderby=name&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'><b>주문자명(ID)</b></a></td>
							<td class=m_td width='120'><a href='?orderby=oid&ordertype=".($ordertype == "" || $ordertype == "asc" ? "desc":"asc")."&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&auth=$auth'><b>주문번호</b></a></td>
							<td class=m_td width='150'>입금여부</td>
							<td class=m_td width='*'>계산서상태</td>
							<td class=e_td width='80'>관리</td>
						</tr>
						";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=50><td colspan=9 align=center><!--세금계산서 내역이 존재 하지 않습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</td></tr>";

	}else{

		$sql = "select *
		from shop_taxbill t
		where tax_no <> ''
		$where1
		";

		//echo $sql;
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$params = array(
				CERTKEY  		=> "41002A15-CBDA-4C0B-96F1-B81B819BB119",
				CorpNum			=> "2141009837",
				MgtKey			=> $db->dt[tax_no],
			);
			//print_r($params);


			// Soap 문장 전송
			$BarobillState = "";
			$NTSSendState = "";
			$result = $client->call('GetTaxInvoiceState', $params, '', '', false, true);

			$GetTaxInvoiceStateResult = $result['GetTaxInvoiceStateResult'];
			$BarobillState = $GetTaxInvoiceStateResult['BarobillState'];
			$NTSSendState = $GetTaxInvoiceStateResult['NTSSendState'];

			$update = "";
			$updateYN = "N";
			if($db->dt[tax_issue_states] != $BarobillState) { $updateYN = "Y"; }
			if($db->dt[tax_sendnts] != $NTSSendState) { $updateYN = "Y"; }

			$states1 = substr($BarobillState, 0,1);
			$states2 = substr($BarobillState, 1,2);
			$states3 = substr($BarobillState, 3,1);
			if($updateYN == "Y") $db->query("update shop_taxbill set tax_issue_states = '".$BarobillState."', tax_sendnts = '".$NTSSendState."',tax_senddate = NOW() where tax_no = '".$db->dt[tax_no]."' ");

			//echo $states1."<br>";
			//echo $states2."<br>";
			//echo $states3."<br>";
			if(!$db->dt[tax_orderno]) $db->dt[tax_orderno] = "직접입력";
			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td><input type='checkbox' name='tax_no[]' id='oid' value='".$db->dt[tax_no]."'></td>
			<td bgcolor='#efefef'>".$db->dt[tax_name]."</td>
			<td align=left style='padding-left:20px;'>".$db->dt[tax_orderno]."</td>
			<td width='150' align='center'>
			".bankstates($db->dt[tax_deposit_states])."
			(".$db->dt[tax_deposit_date].")
			</td>
			<td align='left'>
			".docstates($states1)." 문서 ".complstates($states2)." ".complestates($states3)."
			</td>
			<td >
			";
			if($NTSSendState == "4") {
				$mString .= "<img src='../images/btn/btn_comple.gif'>";
			} else if($NTSSendState == "1") {
				$mString .= "<a href='taxbill.act.php?MgtKey=".$db->dt[tax_no]."&oid=".$db->dt[tax_orderno]."&act=sendtonts&s=a'><img src='../images/btn/btn_ntsok.gif'></a><br>";
				$mString .= "<a href='taxbill.act.php?MgtKey=".$db->dt[tax_no]."&oid=".$db->dt[tax_orderno]."&act=sendtonts&s=f'><img src='../images/btn/btn_ntsoki.gif'></a>";
			} else {
				$mString .= ntsstates($db->dt[tax_sendnts])."<br>";
			}
				$mString .= "<br><a href='taxbill.act.php?MgtKey=".$db->dt[tax_no]."&oid=".$db->dt[tax_orderno]."&act=printpop&s=a' target='act'><img src='../images/btn/btn_print.gif'></a><a href='taxbill.act.php?MgtKey=".$db->dt[tax_no]."&oid=".$db->dt[tax_orderno]."&act=printpop&s=f' target='act'><img src='../images/btn/btn_view.gif'></a>";
			$mString = $mString."</td>
			</tr>
			<tr height=1><td colspan=6 class='dot-x'></td></tr>
			";

		}

		$mString .= "<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max&tax_yn=$tax_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&orderby=$orderby&ordertype=$ordertype&search_type=$search_type&search_text=$search_text","")."</td>
					<td colspan=3 align=right>  선택한 계산서를 국세청으로 <input type=image src='../images/btn/btn_allok.gif' align=absmiddle>";

					$mString .= "</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}
function ntsstates($states) {
	switch ($states) {
	 case "1" :
			return "전송전";
		break;
	 case "2" :
			return "전송대기";
		break;
	 case "3" :
			return "전송중";
		break;
	 case "4" :
			return "전송완료";
		break;
	 case "5" :
			return "전송실패";
		break;
	 default :
			return "미처리";
		break;
	}
}
function docstates($states) {
	switch ($states) {
	 case "1" :
			return "임시저장";
		break;
	 case "2" :
			return "진행중";
		break;
	 case "3" :
			return "완료";
		break;
	 case "4" :
			return "거부됨";
		break;
	 case "5" :
			return "취소됨";
		break;
	 default :
			return "미처리";
		break;
	}
}

function complstates($states) {
	switch ($states) {
	 case "01" :
			return "정발행승인요청";
		break;
	 case "02" :
			return "역발행요청";
		break;
	 case "03" :
			return "취소요청(공급자)";
		break;
	 case "04" :
			return "취소요청(공급받는자)";
		break;
	 case "05" :
			return "내부발행요청";
		break;
	 case "06" :
			return "내부발행요청";
		break;
	 default :
			return "미처리";
		break;
	}
}

function complestates($states) {
	switch ($states) {
	 case "0" :
			return "미처리 중";
		break;
	 case "1" :
			return "승인";
		break;
	 case "2" :
			return "거부";
		break;
	 case "3" :
			return "자체취소";
		break;
	 case "4" :
			return "국세청전송을 위해 강제 승인 또는 취소됨";
		break;
	 default :
			return "미처리";
		break;
	}
}

function bankstates($states) {
	switch ($states) {
	 case "Y" :
			return "입금";
		break;
	 case "N" :
			return "미입금";
		break;
	 default :
			return "미처리";
		break;
	}
}

?>
