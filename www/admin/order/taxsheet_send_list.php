<?
include("../class/layout.class");
include("barobill.lib.php");

$db = new Database;
$mdb = new Database;
$db2 = new Database;
$tdb = new Database;

//echo md5('830316'.'000');
/*
$help_text = "	-  세금계산서발행 목록입니다. <br>
	체크박스를 체크후 일괄발송을 누르시면 발행이 됩니다.
		";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

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

function act_delete(oid,company_id,surtax_yorn){
	if(confirm('해당 세금계산서를 삭제하시겠습니까?')){
		window.frames['act'].location.href='taxbill2.act.php?act=delete&oid='+oid+'&company_id='+company_id+'&surtax_yorn='+surtax_yorn;
	}
}

function act_seller_delete(ac_ix){
	if(confirm('해당 세금계산서를 삭제하시겠습니까?')){
		window.frames['act'].location.href='taxbill2.act.php?act=seller_delete&ac_ix='+ac_ix;
	}
}

function act_ready_modify(oid,today){

	sc_damdang = encodeURIComponent($('[name=tax_charge_name['+oid+']]').val());
	sc_mail = $('[name=tax_charge_email['+oid+']]').val();
	taxt_regdate = $('[name=taxt_regdate_y['+oid+']]').val()+'-'+$('[name=taxt_regdate_m['+oid+']]').val()+'-'+$('[name=taxt_regdate_d['+oid+']]').val()
	purpose_states = $('[name=tax_purpose_states['+oid+']]').val();
	deposit_states = $('[name=tax_deposit_states['+oid+']]').val();
	bank_date = $('[name=bank_date['+oid+']]').val();


	if(today <  $('[name=taxt_regdate_y['+oid+']]').val()+$('[name=taxt_regdate_m['+oid+']]').val()+$('[name=taxt_regdate_d['+oid+']]').val()){
		alert('금일 이후 날짜로는 전자세금계산서를 발행할 수 없습니다.');
		return false;
	}

	if(confirm(oid +' 주문의 세금계산서 정보를 바꾸시겠습니까?')){
		window.frames['act'].location.href='taxbill2.act.php?act=ready_modify&sc_damdang='+sc_damdang+'&sc_mail='+sc_mail+'&taxt_regdate='+taxt_regdate+'&purpose_states='+purpose_states+'&deposit_states='+deposit_states+'&bank_date='+bank_date+'&oid='+oid;
	}
}

function act_seller_ready_modify(ac_ix,today){

	sc_damdang = encodeURIComponent($('[name=tax_charge_name['+ac_ix+']]').val());
	sc_mail = $('[name=tax_charge_email['+ac_ix+']]').val();
	taxt_regdate = $('[name=taxt_regdate_y['+ac_ix+']]').val()+'-'+$('[name=taxt_regdate_m['+ac_ix+']]').val()+'-'+$('[name=taxt_regdate_d['+ac_ix+']]').val()

	if(today <  $('[name=taxt_regdate_y['+ac_ix+']]').val()+$('[name=taxt_regdate_m['+ac_ix+']]').val()+$('[name=taxt_regdate_d['+ac_ix+']]').val()){
		alert('금일 이후 날짜로는 전자세금계산서를 발행할 수 없습니다.');
		return false;
	}

	if(confirm('해당 세금계산서 정보를 바꾸시겠습니까?')){
		window.frames['act'].location.href='taxbill2.act.php?act=seller_ready_modify&sc_damdang='+sc_damdang+'&sc_mail='+sc_mail+'&taxt_regdate='+taxt_regdate+'&ac_ix='+ac_ix;
	}
}

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

	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;

}

function ChangeUsableDate(frm){
	if(frm.date.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
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

		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-bottom:100px;'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("세금계산서 발행내역", "증빙서 > 세금계산서 발행내역")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding:10px 0px 15px 0px;'>
				<div class='tab'>
					<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_01'  ".($acc_view_type == "" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?acc_view_type='\">고객</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<table id='tab_02' ".($acc_view_type == "seller" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?acc_view_type=seller'\">입점업체</td>
										<th class='box_03'></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' valign=top style='padding:2px;'>
										<TABLE height=0 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
											<TR>
												<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
													<table border=0 cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
													<col width = 15% >
													<col width = * >
														<tr height=30>
														  <th class='search_box_title' ><label for='date'>요청일자</label><input type='checkbox' name='date' id='date' value='1' onclick='ChangeUsableDate(document.searchmember);' ".CompareReturnValue("1",$date,"checked")." /></th>
														  <td class='search_box_item' >
															<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
																<tr>
																	<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
																	<TD style='padding:0 5px;' align=center> ~ </TD>
																	<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>

																	<TD style='padding-left:10px;'>
																		<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
																		<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
																		<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
																		<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
																		<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
																		<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
																		<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
																	</TD>
																</tr>
															</table>
														  </td>
														</tr>
														<tr height=30>
															<th class='search_box_title' >발급유무</th>
															<td class='search_box_item' >
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
														<tr height=30>
															<th class='search_box_title' >조건검색</th>
															<td class='search_box_item' >
																<table>
																	<tr>
																		<td>
																			<select name='search_type'>
																				<option value='name_comname'>주문자명+사업자명</option>
																				<option value='oid'>주문번호</option>
																				<option value='com_number'>사업자번호</option>
																			</select>
																		<input type='text' class='textbox' name='search_text' size=20>
																		</td>
																		<td>
																		사업자 번호 검색시 예시 : 214-10-12345
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
						<td colspan=3 align=center  style='padding:20px 0'>
							<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<!--tr>
			<td align=right><a href='taxsheet_list.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
		</tr-->
		<form name='taxbill_form' method='post' action='taxbill.act.php' target='act'>
		<input type='hidden' id='oid'>
		<input type='hidden' name='act' value='select_update'>
		<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
		<tr>
			<td>
			".($acc_view_type == "seller" ? PrintSellerTaxList() : PrintTaxList() )."
			</td>
		</tr>
		</form>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("세금계산서 관리", $help_text)."</td></tr>";
$mstring .="</table>";

$Contents = $mstring;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();ChangeUsableDate(document.searchmember);";
$P->Navigation = "증빙서 > 세금계산서 발행내역";
$P->title = "세금계산서 발행내역";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintTaxList(){
	global $db,$mdb,$db2,$tdb,$admininfo,$page,$nset,$tax_yn,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,		$orderby,$ordertype,$search_type,$search_text,$sns_product_type,$client,$method,$surtax_yorn,$status,$deposit_states,$methodstr,$baroInfo,$datecheck;


	if ($orderby != "" || $ordertype != ""){
		$orderby_str = " order by $orderby $ordertype ";
	}else{
		$orderby_str = " order by regdate desc ";
	}
	$where1=" AND od.product_type NOT IN ('".implode("','",$sns_product_type)."')";
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
			$where1 .= " and (AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."')  LIKE '%$search_text%' or ccd.com_name LIKE '%$search_text%') ";
		}else{
			$where1 .= " and $search_type LIKE '%$search_text%' ";
		}
	}


	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	// 검색시 회원명과 , 회사이름 검색필드가 없어서 아래 형태로 쿼리 변경 2012.01.13
	if($db->dbms_type == "oracle"){
		$sql = "select AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
		where (taxsheet_yn in ('Y') and tax_states = 'Y') and o.oid = od.oid
		and od.ptprice > '0' and od.status <> 'SR'
		and cu.code = cmd.code and cu.company_id = ccd.company_id
		and o.uid_ = cmd.code $where1";
	}else{
		$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
		where (taxsheet_yn in ('Y') and tax_states = 'Y') and o.oid = od.oid
		and od.ptprice > 0 and od.status <> 'SR'
		and cu.code = cmd.code and cu.company_id = ccd.company_id
		and o.uid = cmd.code $where1";
	}
	//echo $sql;
	$mdb->query($sql);
	$total_od_ix = $mdb->total;
	if($db->dbms_type == "oracle"){
		$sql = "select distinct(o.oid),AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
		where (taxsheet_yn in ('Y') and tax_states = 'Y') and o.oid = od.oid
		and od.ptprice > '0' and od.status <> 'SR'
		and cu.code = cmd.code and cu.company_id = ccd.company_id
		and o.uid_ = cmd.code $where1 ";
	}else{
		$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
		where (taxsheet_yn in ('Y') and tax_states = 'Y') and o.oid = od.oid
		and od.ptprice > 0 and od.status <> 'SR'
		and cu.code = cmd.code and cu.company_id = ccd.company_id
		and o.uid = cmd.code $where1";
	}
	//echo $sql;
	$mdb->query($sql);
	$total = $mdb->total;
	if($db->dbms_type == "oracle"){
		$sql = "select distinct (o.oid), AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name ,	id, cmd.code
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
		where (taxsheet_yn in ('Y') and tax_states = 'Y') and o.oid = od.oid
		and od.ptprice > '0' and od.status <> 'SR'
		and cu.code = cmd.code and cu.company_id = ccd.company_id
		and o.uid_ = cmd.code  $where1 limit $start, $max";
	}else{
		$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name ,
		id, cmd.code,o.*
		from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_COMMON_COMPANY_DETAIL." ccd , ".TBL_SHOP_ORDER_DETAIL." od
		where (taxsheet_yn in ('Y') and tax_states = 'Y') and o.oid = od.oid
		and od.ptprice > 0 and od.status <> 'SR'
		and cu.code = cmd.code and cu.company_id = ccd.company_id
		and o.uid = cmd.code  $where1 group by o.oid limit $start, $max";
	}
	//echo $sql;
	$mdb->query($sql);


$mString = "<ul class='total_cnt_area' >
					<li class='front'>전체건수 : ".$total_od_ix." 건</li>
					<li class='back'></li>
				  </ul>";
	$mString .= "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>";

	$mString .= "<tr align=center bgcolor=#efefef height=25>
							<td class='s_td' width='30' align=center><input type='checkbox' name='all_fix' id='all_fix' value='Y' onclick=\"fixAll(document.taxbill_form)\"></td>
							<td class=m_td width='80'><b>주문자명<br/>(ID)</b></td>
							<td class=m_td width='130'>주문번호<br/>(구매형태)</td>
							<td class=m_td width='*'><b>주문상품명</b></td>
							<td class=m_td width='50'>과세</td>
							<td class=m_td width='140'>입점업체<br/>(사업자등록번호)</td>
							<td class=m_td width='80'>금액</td>
							<td class=m_td width='30'>주<br />문<br />상<br />품</td>
							<td class=m_td width='30'>결<br />제<br />완<br />료</td>
							<td class=m_td width='30'>상<br />품<br />준<br />비</td>
							<td class=m_td width='30'>배<br />송<br />중</td>
							<td class=m_td width='30'>배<br />송<br />완<br />료</td>
							<td class=m_td width='30'>취<br />소<br />/<br />반<br />품</td>
							<td class=m_td width='150'>담당자<br />/이메일<br />/발행일</td>
							<td class=m_td width='80'>구분<br />/입금여부<br />/입금날짜</td>
							<td class=m_td width='60' >E-mail 열람</td>
							<td class=e_td width='60' >국세청 전송상태</td>
						</tr>
						";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=90><td colspan=17 align=center>세금계산서 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
	}else{

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if ($mdb->dt[method] == ORDER_METHOD_CARD)
			{
				if($ddb->dt[bank] == ""){
					$method = "카드결제";
				}else{
					$method = $db1->dt[bank];
				}
			}elseif($mdb->dt[method] == ORDER_METHOD_BANK){
				$method = "계좌입금";
			}elseif($mdb->dt[method] == ORDER_METHOD_PHONE){
				$method = "전화결제";
			}elseif($mdb->dt[method] == ORDER_METHOD_AFTER){
				$method = "후불결제";
			}elseif($mdb->dt[method] == ORDER_METHOD_VBANK){
				$method = "가상계좌";
			}elseif($mdb->dt[method] == ORDER_METHOD_ICHE){
				$method = "실시간계좌이체";
			}elseif($mdb->dt[method] == ORDER_METHOD_ASCROW){
				$method = "가상계좌[에스크로]";
			}elseif($mdb->dt[method] == ORDER_METHOD_NOPAY){
				$method = "무료결제";
			}

			$sql = "SELECT od.pname,od.surtax_yorn, company_name,com_number,cd.company_id,od.tax_num,od.tax_issue_states,od.tax_opened,od.tax_type,od.NTSSendState,
					(select case when od.surtax_yorn != 'Y' then delivery_price else 0 end from shop_order_delivery ode where od.oid=ode.oid and cd.company_id=ode.company_id) as delivery_price,
				count(od.od_ix) as od_cnt,
				sum(od.ptprice) as total_ptprice,
				sum(od.pcnt) as order_detail_cnt,
				sum(case when od.status in ('CC','RC','DC','FC','EC','AR','AC','AA') then 1 else 0 end) as complet_cnt,
				sum(case when od.status in ('IC') then 1 else 0 end) as pay_complet_cnt,
				sum(case when od.status not in ('IR','IC','DR') then 0 else 1 end) as delivery_ing,
				sum(case when od.status in ('DR') then 1 else 0 end) as ready_cnt,
				sum(case when od.status in ('DI') then 1 else 0 end) as delivery_cnt,
				sum(case when od.status in ('DC','AR','AC') then 1 else 0 end) as delivery_complet_cnt,
				sum(case when od.status in ('CC','RC','FC','CA') then 1 else 0 end) as cancel_cnt,
				sum(case when od.status in ('IR','IC','DR','DI','EA','EI','ED','FA','RA','RI','RD','CA') then 1 else 0 end) as ing_cnt
			FROM `shop_order_detail`od left join common_company_detail cd using(company_id)
			WHERE oid = '".$mdb->dt[oid]."' and od.tax_states ='Y'
			group by company_id,surtax_yorn order by company_id,surtax_yorn
			";
			$db2->query($sql);

			for($j=0;$j < $db2->total;$j++){
				$db2->fetch($j);

				$mString .= "<tr height=30 bgcolor=#ffffff align=center>
				<td><input type='checkbox' name='oid[]' id='oid' value='".$mdb->dt[oid]."'></td>";


				if($j ==0){
					$mString .="<td bgcolor='#efefef' rowspan='".$db2->total."'><a href=\"javascript:PopSWindow('../member/member_view.php?code=".$mdb->dt[code]."',950,500,'member_info')\">".$mdb->dt[name]."<br>(".$mdb->dt[id].")</a></td>
					<td  align='center' rowspan='".$db2->total."'>".$mdb->dt[oid]."<br/>(".$method.")</td>";
				}

				if($od_cnt > 1){
					$mString .="<td align=left style='padding-left:20px;'>".cut_str($db2->dt[pname],25)." 외 ".$od_cnt."</td>";
				}else{
					$mString .="<td align=left style='padding-left:20px;'>".cut_str($db2->dt[pname],30)."</td>";
				}

				if($db2->dt[surtax_yorn] == "Y") $surtax_yorn = "비과세";
				else $surtax_yorn = "과세";
				$mString .= "<td align='center'>".$surtax_yorn."</td>

				<td bgcolor='#efefef'>".$db2->dt[company_name]."<br />(".$db2->dt[com_number].")</td>
				<td bgcolor='#ffffff'>".number_format($db2->dt[total_ptprice]+$db2->dt[delivery_price])."원</td>

				<td align=center>".$db2->dt[order_detail_cnt]."</td>
				<td align=center style='color:red;'>".$db2->dt[pay_complet_cnt]."</td>
				<td align=center style='color:blue;'>".($db2->dt[ready_cnt])."</td>
				<td align=center>".$db2->dt[delivery_cnt]."</td>
				<td align=center >".$db2->dt[delivery_complet_cnt]."</td>
				<td align=center >".$db2->dt[cancel_cnt]."</td>";

				if($j ==0){
					$mString .= "<td align='center' bgcolor='#efefef' rowspan='".$db2->total."'>
						<input type='text' name='tax_charge_name[".$mdb->dt[oid]."]' class='textbox' value='".$mdb->dt[sc_damdang]."' style='width:138px;margin:2px 0px;'>
						<input type='text' name='tax_charge_email[".$mdb->dt[oid]."]' class='textbox' value='".$mdb->dt[sc_mail]."' style='width:138px;margin:2px 0px;'>
						<select name='taxt_regdate_y[".$mdb->dt[oid]."]' style='width:60px;'>";
					if(!$mdb->dt[taxt_regdate]){
						$toyear = date("Y");
						$tomonth = date("m");
						$today = date("d");
						$endyear = $toyear - 3;
					}else{
						$toyear = substr($mdb->dt[taxt_regdate],0,4);
						$tomonth = substr($mdb->dt[taxt_regdate],5,2);
						$today = substr($mdb->dt[taxt_regdate],8,2);
						$endyear = $toyear - 3;
					}
					//echo $tomonth;
					for($b=$toyear; $b>$endyear; $b--){
						if($b == $toyear) $checked = " selected ";
						else $checked = "";
						$mString .= "<option value='$b'$checked>$b</option>\n";
					}
					$mString .= "</select>
					<select name='taxt_regdate_m[".$mdb->dt[oid]."]' style='width:40px;'>
					";
					for($c=1; $c<=12; $c++){
						if($c == $tomonth) $checked = " selected ";
						else $checked = "";
						if($c < 10) $c = "0".$c;
						$mString .= "<option value='$c'$checked>$c</option>\n";
					}
					$mString .= "</select>
					<select name='taxt_regdate_d[".$mdb->dt[oid]."]' style='width:40px;'>
					";
					for($d=1; $d<=31; $d++){
						if($d == $today) $checked = " selected ";
						else $checked = "";
						if($d < 10) $d = "0".$d;
						$mString .= "<option value='$d'$checked>$d</option>\n";
					}
					$mString .= "</select>
					</td>
					<td align='center' rowspan='".$db2->total."'>
						<select name='tax_purpose_states[".$mdb->dt[oid]."]' style='width:100px;'>
							<option style='color:blue;' value='1' ".($mdb->dt[purpose_states]=='1' ? 'selected':'')." >영수 </option>
							<option value='2' ".($mdb->dt[purpose_states]=='2' ? 'selected':'')." >청구</option>
						</select>
						<select name='tax_deposit_states[".$mdb->dt[oid]."]' style='width:100px;'>
							<option value='Y' ".($mdb->dt[deposit_states]=='Y' ? 'selected':'').">입금 </option>
							<option value='N' ".($mdb->dt[deposit_states]=='N' ? 'selected':'').">미입금 </option>
						</select>
						<input type='text' class='textbox' name='bank_date[".$mdb->dt[oid]."]' value='".$mdb->dt[bank_date]."' style='width:90px;margin:2px 0px;'>
					</td>";
				}

				if($db2->dt[tax_type]==1){
					$db2->dt[tax_type]='정발행';
				}elseif($db2->dt[tax_type]==2){
					$db2->dt[tax_type]='역발행';
				}else{
					$db2->dt[tax_type]='위수탁';
				}


				$update_ ='';
				$IsOpened = IsOpened($db2->dt[tax_num]);

				if($IsOpened == $db2->dt[tax_opened]){
					$tax_opened = $db2->dt[tax_opened];
				}else{
					$tax_opened = $IsOpened;
					$update_ ='Y';
				}

				switch($tax_opened){
					case 0;
						$tax_opened_str ="<span style='color:#C0C0C0;'>미열람</span>";
					break;
					case 1;
						$tax_opened_str ='열람';
					break;
					default :
						$tax_opened = $db2->dt[tax_opened];
					break;
				}

				$BarobillState = BarobillState($db2->dt[tax_num]);

				if($BarobillState == $db2->dt[tax_issue_states]){
					$tax_issue_states = $db2->dt[tax_issue_states];
				}else{
					$tax_issue_states = $BarobillState;
					$update_ ='Y';
				}

				switch($tax_issue_states){
					case 1;
						$tax_issue_states_str ="";
						break;
					case 3;
					$tax_issue_states_str ='<br/>(거부)';
					break;
					case 4;
						$tax_issue_states_str ='<br/>(취소)';
						break;
					default :
						$tax_issue_states = $db2->dt[tax_issue_states];
						break;
				}

				$NTSSendState = NTSSendState($db2->dt[tax_num]);

				if($NTSSendState == $db2->dt[NTSSendState]){
					$NTSSendState_ = $db2->dt[NTSSendState];
				}else{
					$NTSSendState_ = $NTSSendState;
					$update_ ='Y';
				}

				switch($NTSSendState_){
					case 1;
						$NTSSendState_str ="전송전";
					break;
					case 2;
						$NTSSendState_str ='전송대기';
					break;
					case 3;
						$NTSSendState_str ="전송중";
					break;
					case 4;
						$NTSSendState_str ='전송완료';
					break;
					case 5;
						$NTSSendState_str ="전송실패";
					break;
					default :
						$NTSSendState_ = $db2->dt[NTSSendState];
						break;
				}

				if($update_ =='Y'){
					$tdb->query("update shop_order_detail set  tax_issue_states='".$tax_issue_states."', tax_opened='".$tax_opened."' , NTSSendState='".$NTSSendState_."'   where oid = '".$mdb->dt[oid]."' and company_id='".$db2->dt[company_id]."' and surtax_yorn = '".$db2->dt[surtax_yorn]."' ");
				}

				$mString .= "
				<td  bgcolor='#ffffff' rowspan='".$db2->total."'>
					".$tax_opened_str."".$tax_issue_states_str."
				</td>
				<td bgcolor='#efefef'>
					".$NTSSendState_str."
					<br />".$db2->dt[tax_type]."
					<br />".GetTaxinvoiceMailURL($db2->dt[tax_num])."<img src='../images/".$admininfo["language"]."/btn_detail_view.gif' onclick=\"\" style='cursor:pointer; vertical-align:middle;'></a>
				</td>
			</tr>";
			}

		}

	$mString .= "</table>";

	$mString .= "<ul class='paging_area' >
						<li class='front'><!--input type=image src='../images/".$admininfo["language"]."/btn_allok.gif' align=absmiddle--></li>
						<li class='back' style='width:100%;float:none;'>".page_bar($total, $page, $max,  "&max=$max&receipt_yn=$receipt_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&orderby=$orderby&ordertype=$ordertype&search_type=$search_type&search_text=$search_text","")."</li>
					  </ul>";

	}


	return $mString;
}





function PrintSellerTaxList(){
	global $db, $mdb, $db2,$tdb,$admininfo,$page,$nset,$tax_yn,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$orderby,$ordertype,$search_type,$search_text,$sns_product_type;

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
			$where1 .= " and (AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."')  LIKE '%$search_text%' or ccd.com_name LIKE '%$search_text%') ";
		}else{
			$where1 .= " and $search_type LIKE '%$search_text%' ";
		}
	}

	$sql = "select ac_ix
		from shop_accounts a left join common_company_detail cd using (company_id)
		where taxsheet_yn = 'Y' and taxbill_yn ='Y'  $where1";

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

$mString = "<ul class='total_cnt_area' >
					<li class='front'>전체건수 : ".$total." 건</li>
					<li class='back'></li>
				  </ul>";
	$mString .= "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>";

	$mString .= "<tr align=center bgcolor=#efefef height=25>
							<td class='s_td' width='30' align=center><input type='checkbox' name='all_fix' id='all_fix' value='Y' onclick=\"fixAll(document.taxbill_form)\"></td>
							<td class=m_td width='100'><b>업체명</b></td>
							<td class=m_td width='80'><b>정산일</b></td>
							<td class=m_td width='100'>품목</td>
							<td class=m_td width='150'>공급사<br />(사업자 번호)</td>
							<td class=m_td width='150'>공급받는자<br />(사업자 번호)</td>
							<td class=m_td width='100'>금액</td>
							<td class=m_td width='150'>담당자/이메일/발행일</td>
							<td class=m_td width='100'>E-mail 열람</td>
							<td class=e_td width='100'>국세청 전송상태</td>
						</tr>
						";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=90><td colspan=10 align=center>세금계산서 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
	}else{


		$sql = "select * from common_company_detail where com_type = 'A' ";
		$db->query($sql);
		$basic_shop_info = $db->fetch(0);

		$sql = "select *
			from shop_accounts a left join common_company_detail cd using (company_id)
			where taxsheet_yn = 'Y' and taxbill_yn ='Y'  $where1";

		//echo $sql;
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td><input type='checkbox' name='ac_ix[]' id='ac_ix' value='".$db->dt[ac_ix]."'></td>
			<td bgcolor='#efefef'><!--a href=\"javascript:PopSWindow('../member/member_view.php?code=".$db->dt[company_id]."',950,500,'member_info')\"-->".$db->dt[com_name]."<!--/a--></td>
			<td align=center'>".substr($db->dt[ac_date],0,4).".".substr($db->dt[ac_date],4,2).".".substr($db->dt[ac_date],6,2)."</td>
			<td bgcolor='#efefef' align='center'>".$db->dt[month]."월 수수료 정산</td>
			<td bgcolor='#ffffff'>".$basic_shop_info[com_name]."<br />(".$basic_shop_info[com_number].")</td>
			<td bgcolor='#ffffff'>".$db->dt[com_name]."<br />(".$db->dt[com_number].")</td>
			<td bgcolor='#efefef'>".number_format($db->dt[sell_total_price]-$db->dt[ac_price])."</td>
			<td  width='150' align='center' bgcolor='#ffffff'>
				<input type='text' name='tax_charge_name[".$db->dt[ac_ix]."]' class='textbox' value='".$db->dt[sc_damdang]."' style='width:138px;margin:2px 0px;'>
				<input type='text' name='tax_charge_email[".$db->dt[ac_ix]."]' class='textbox' value='".$db->dt[sc_mail]."' style='width:138px;margin:2px 0px;'>
				<select name='taxt_regdate_y[".$db->dt[ac_ix]."]' style='width:60px;'>
			";
			if(!$db->dt[taxt_regdate]){
				$toyear = date("Y");
				$tomonth = date("m");
				$today = date("d");
				$endyear = $toyear - 3;
			}else{
				$toyear = substr($db->dt[taxt_regdate],0,4);
				$tomonth = substr($db->dt[taxt_regdate],5,2);
				$today = substr($db->dt[taxt_regdate],8,2);
				$endyear = $toyear - 3;
			}
			//echo $tomonth;
			for($b=$toyear; $b>$endyear; $b--){
				if($b == $toyear) $checked = " selected ";
				else $checked = "";
				$mString .= "<option value='$b'$checked>$b</option>\n";
			}
			$mString = $mString."</select>
			<select name='taxt_regdate_m[".$db->dt[ac_ix]."]' style='width:40px;'>
			";
			for($c=1; $c<=12; $c++){
				if($c == $tomonth) $checked = " selected ";
				else $checked = "";
				if($c < 10) $c = "0".$c;
				$mString .= "<option value='$c'$checked>$c</option>\n";
			}
			$mString = $mString."</select>
			<select name='taxt_regdate_d[".$db->dt[ac_ix]."]' style='width:40px;'>
			";
			for($d=1; $d<=31; $d++){
				if($d == $today) $checked = " selected ";
				else $checked = "";
				if($d < 10) $d = "0".$d;
				$mString .= "<option value='$d'$checked>$d</option>\n";
			}
			$mString = $mString."</select>
			</td>";

			if($db->dt[tax_type]==1){
				$db->dt[tax_type]='정발행';
			}elseif($db->dt[tax_type]==2){
				$db->dt[tax_type]='역발행';
			}else{
				$db->dt[tax_type]='위수탁';
			}


			$update_ ='';
			$IsOpened = IsOpened($db->dt[tax_num]);

			if($IsOpened == $db->dt[tax_opened]){
				$tax_opened = $db->dt[tax_opened];
			}else{
				$tax_opened = $IsOpened;
				$update_ ='Y';
			}

			switch($tax_opened){
				case 0;
					$tax_opened_str ="<span style='color:#C0C0C0;'>미열람</span>";
				break;
				case 1;
					$tax_opened_str ='열람';
				break;
				default :
				$tax_opened = $db->dt[tax_opened];
				break;
			}

			$BarobillState = BarobillState($db->dt[tax_num]);

			if($BarobillState == $db->dt[tax_issue_states]){
				$tax_issue_states = $db->dt[tax_issue_states];
			}else{
				$tax_issue_states = $BarobillState;
				$update_ ='Y';
			}

			switch($tax_issue_states){
				case 1;
					$tax_issue_states_str ="";
				break;
				case 3;
					$tax_issue_states_str ='<br/>(거부)';
				break;
				case 4;
					$tax_issue_states_str ='<br/>(취소)';
				break;
				default :
				$tax_issue_states = $db->dt[tax_issue_states];
				break;
			}

			$NTSSendState = NTSSendState($db->dt[tax_num]);

			if($NTSSendState == $db->dt[NTSSendState]){
				$NTSSendState_ = $db->dt[NTSSendState];
			}else{
				$NTSSendState_ = $NTSSendState;
				$update_ ='Y';
			}

			switch($NTSSendState_){
				case 1;
					$NTSSendState_str ="전송전";
				break;
				case 2;
					$NTSSendState_str ='전송대기';
				break;
				case 3;
					$NTSSendState_str ="전송중";
				break;
				case 4;
					$NTSSendState_str ='전송완료';
				break;
				case 5;
					$NTSSendState_str ="전송실패";
				break;
				default :
				$NTSSendState_ = $db->dt[NTSSendState];
				break;
			}

			if($update_ =='Y'){
				$tdb->query("update shop_accounts set tax_issue_states='".$tax_issue_states."', tax_opened='".$tax_opened."' , NTSSendState='".$NTSSendState_."'   where ac_ix= '".$db->dt[ac_ix]."' ");
			}

$mString .= "
			<td bgcolor='#ffffff'>
				".$tax_opened_str."".$tax_issue_states_str."
			</td>
			<td bgcolor='#efefef'>
				".$NTSSendState_str."
				<br />".$db->dt[tax_type]."
				<br />".GetTaxinvoiceMailURL($db->dt[tax_num])."<img src='../images/".$admininfo["language"]."/btn_detail_view.gif' onclick=\"\" style='cursor:pointer; vertical-align:middle;'></a>
			</td>
			</tr>
			<tr height=1><td colspan=10 class='dot-x'></td></tr>
			";

		}

	$mString .= "</table>";
	$mString .= "<ul class='paging_area' >
						<li class='front'><!--input type=image src='../images/".$admininfo["language"]."/btn_allok.gif' align=absmiddle--></li>
						<li class='back' style='width:100%;float:none;'>".page_bar($total, $page, $max,  "&max=$max&receipt_yn=$receipt_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&orderby=$orderby&ordertype=$ordertype&search_type=$search_type&search_text=$search_text","")."</li>
					  </ul>";

	}


	return $mString;
}
?>
