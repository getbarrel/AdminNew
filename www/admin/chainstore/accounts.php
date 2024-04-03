<?
include("../class/layout.class");

//print_r($admininfo);
$db1 = new Database;
$odb = new Database;


if ($vToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")-1));
	$eDate = date("Y/m/d", time()+84600*(31-date("d")));

	$startDate = date("Ymd", time()-84600*(date("d")-1));
	$endDate = date("Ymd", time()+84600*(31-date("d")));
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}



	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	if ($vFromYY != "")	$where .= "and a.ac_date between $startDate and $endDate ";

	if($status){
		$where .= " and a.status = '".$status."'";
	}

	if($mode != "excel"){
		$limit_str = "  LIMIT $start, $max ";
	}

	if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$company_id_str = " and c.company_id = '$company_id'";
			}

			if($admininfo[mem_type] == "MD"){
				$company_id_str .= " and c.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

			if($acc_view_type == "detail" || $acc_view_type == "report"){
				$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ORDER_DETAIL." od
								where a.company_id = c.company_id and a.ac_ix = od.ac_ix
								$where $company_id_str ";
				$db1->query($sql);
				$total = $db1->total;

				$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner , od.oid, od.pid, od.pname, od.option1, od.option_text, od.option_etc,
								od.pcnt, od.coprice, od.psprice, od.ptprice, od.ptprice*(100-od.commission)/100 as account_price, od.delivery_price,
								od.commission, od.surtax_yorn, o.bname, o.method, o.receipt_y, o.mem_group, o.use_reserve_price ,  o.use_cupon_price
								FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_COMMON_SELLER_DETAIL." csd , ".TBL_SHOP_ORDER_DETAIL." od,  ".TBL_SHOP_ORDER." o
								where a.company_id = c.company_id and c.company_id = csd.company_id and a.ac_ix = od.ac_ix and o.oid = od.oid $company_id_str $where  order by o.oid asc $limit_str ";
				//echo $sql;
				$db1->query($sql);
			}else{
				$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c where a.company_id = c.company_id $where $company_id_str ";
				$db1->query($sql);
				$total = $db1->total;

				$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner
								FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_COMMON_SELLER_DETAIL." csd
								where a.company_id = c.company_id and c.company_id = csd.company_id $company_id_str $where $limit_str ";
				//echo $sql;
				$db1->query($sql);
			}


	}else if($admininfo[admin_level] == 8){
		if($acc_view_type == "detail"  || $acc_view_type == "report"){
				$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ORDER_DETAIL." od
								where a.company_id = c.company_id and a.ac_ix = od.ac_ix
								$where $company_id_str ";
				$db1->query($sql);
				$total = $db1->total;

				$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner , od.oid,  od.pid,  od.pname, od.option1, od.option_text, od.option_etc,
								od.pcnt, od.coprice, od.psprice, od.ptprice, od.ptprice*(100-od.commission)/100 as account_price, od.delivery_price, od.commission, od.surtax_yorn,
								o.bname, o.method, o.receipt_y, o.mem_group, o.use_reserve_price ,  o.use_cupon_price
								FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_COMMON_SELLER_DETAIL." csd  , ".TBL_SHOP_ORDER_DETAIL." od,  ".TBL_SHOP_ORDER." o
								where a.company_id = c.company_id and c.company_id = csd.company_id and a.ac_ix = od.ac_ix and c.company_id = '".$admininfo[company_id]."' and o.oid = od.oid $where
								order by o.oid asc $limit_str ";
				$db1->query($sql);
		}else{
			$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c where  a.company_id = c.company_id and c.company_id = '".$admininfo[company_id]."' $where ";
			$db1->query($sql);
			$total = $db1->total;

			$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner
					FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_COMMON_SELLER_DETAIL." csd
					where a.company_id = c.company_id and c.company_id = csd.company_id and c.company_id = '".$admininfo[company_id]."' $where $limit_str ";
			$db1->query($sql);
		}
	}
//echo $sql;

if($mode == "excel"){
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=account_complete_list_".date("Y-m-d").".xls" );
	header( "Content-Description: Generated Data" );


	if($db1->total){
		//echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t정산일자\t판매건수\t배송비\t판매총액(할인가기준)\t수수료\t정산금액\t정산상태\n";
		if($acc_view_type == "detail"){
			$mstring = "NO\t업체명\t정산일\t주문번호\t제품명\t옵션\t비고\t수량\t단가\t판매액\t수수료율(%)\t수수료\t면세여부\t정산금액\t정산금액(공급가)\t정산금액(부가세)\t배송비정산금액\t정산상태\n";
		}else if($acc_view_type == "report"){
			$mstring = "<table border=1 style='font-size:12px;'>";
			$mstring .= "<tr align=center>
									<td rowspan=2>NO</td>
									<td rowspan=2>업체명</td>
									<td rowspan=2>정산일</td>
									<td rowspan=2>주문번호</td>
									<td rowspan=2>제품명</td>
									<td rowspan=2>옵션</td>
									<td rowspan=2>비고</td>
									<td rowspan=2>수량</td>
									<td colspan=4>상품매출</td>
									<td colspan=3>배송비정산금액</td>
									<td colspan=3>합계</td>
									<td colspan=3>매출원가</td>
									<td rowspan=2>수수료율(%)</td>
									<td rowspan=2>수수료</td>
									<td rowspan=2>쿠폰용금액</td>
									<td rowspan=2>마일리지 사용금액</td>
									<td rowspan=2>면세여부</td>
									<td rowspan=2>정산상태</td>
									<td rowspan=2>증빙</td>
									<td rowspan=2>결제방법</td>
									<td rowspan=2>회원명</td>
									<td rowspan=2>회원등급</td>
									</tr>";
			$mstring .= "<tr>
									<td>단가</td>
									<td>공급가</td>
									<td>부가세</td>
									<td>합계</td>
									<td>공급가</td>
									<td>부가세</td>
									<td>합계</td>
									<td>공급가</td>
									<td>부가세</td>
									<td>합계</td>
									<td>공급가</td>
									<td>부가세</td>
									<td>합계</td>

									</tr>";
		}else{
			$mstring = "NO\t업체명\t정산일\t은행명\t계좌번호\t입금자명\t정산일자\t판매건수\t판매총액(할인가기준)\t수수료\t정산금액\t배송비정산금액\t정산상태\n";
		}
		for ($i = 0; $i < $db1->total; $i++){
			$db1->fetch($i);

			if ($db1->dt[method] == ORDER_METHOD_CARD)
			{
				if($db1->dt[bank] == ""){
					$method = "카드결제";
				}else{
					$method = $db1->dt[bank];
				}
			}elseif($db1->dt[method] == ORDER_METHOD_BANK){
				$method = "계좌입금";
			}elseif($db1->dt[method] == ORDER_METHOD_PHONE){
				$method = "전화결제";
			}elseif($db1->dt[method] == ORDER_METHOD_AFTER){
				$method = "후불결제";
			}elseif($db1->dt[method] == ORDER_METHOD_VBANK){
				$method = "가상계좌";
			}elseif($db1->dt[method] == ORDER_METHOD_ICHE){
				$method = "실시간계좌이체";
			}elseif($db1->dt[method] == ORDER_METHOD_ASCROW){
				$method = "가상계좌[에스크로]";
			}

			if($db1->dt[receipt_y] == "Y"){
				$receipt_str = "발행";
			}else{
				$receipt_str = "미발행";
			}

			if($acc_view_type == "detail"){
				$mstring .= ($i+1)."\t";
				$mstring .= $db1->dt[com_name]."\t";
				$mstring .= $db1->dt[ac_date]."\t".$db1->dt[oid]."\t";
				$mstring .= strip_tags($db1->dt[pname])."\t";
				$mstring .= strip_tags($db1->dt[option_text])."\t";
				$mstring .= $db1->dt[option_etc1]."\t";
				$mstring .= $db1->dt[pcnt]."\t";
				$mstring .= $db1->dt[psprice]."\t";
				$mstring .= ($db1->dt[ptprice])."\t";
				$mstring .= ($db1->dt[commission])."\t";
				$mstring .= ($db1->dt[ptprice]-$db1->dt[account_price])."\t";
				$mstring .= ($db1->dt[surtax_yorn] == "Y" ? "면세":"과세")."\t";
				$mstring .= $db1->dt[account_price]."\t";
				$mstring .= ($db1->dt[surtax_yorn] == "Y" ? $db1->dt[account_price]:round($db1->dt[account_price]/1.1))."\t";
				$mstring .= ($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[account_price]-round($db1->dt[account_price]/1.1))."\t";
				$mstring .= $db1->dt[delivery_price]."\t";
				$mstring .= ($db1->dt[status] == "AR" ? "정산대기":"정산완료")."\n";
			}else if($acc_view_type == "report"){
				$mstring .= "<tr><td>".($i+1)."</td>";
				$mstring .= "<td>".$db1->dt[com_name]."</td>";
				$mstring .= "<td>".$db1->dt[ac_date]."</td>";
				$mstring .= "<td>".$db1->dt[oid]."</td>";
				$mstring .= "<td>".strip_tags($db1->dt[pname])."</td>";
				$mstring .= "<td>".strip_tags($db1->dt[option_text])."</td>";
				$mstring .= "<td>".$db1->dt[option_etc1]."</td>";
				$mstring .= "<td>".$db1->dt[pcnt]."</td>";
				$mstring .= "<td>".$db1->dt[psprice]."</td>";

				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? $db1->dt[psprice]:round($db1->dt[psprice]/1.1))."</td>";
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[psprice]-round($db1->dt[psprice]/1.1))."</td>";
				$mstring .= "<td>".($db1->dt[ptprice])."</td>";

				$mstring .= "<td>".(round($db1->dt[delivery_price]/1.1))."</td>";
				$mstring .= "<td>".($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))."</td>";
				$mstring .= "<td>".$db1->dt[delivery_price]."</td>";

				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? $db1->dt[ptprice]:round($db1->dt[ptprice]/1.1))+(round($db1->dt[delivery_price]/1.1)))."</td>";
				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[ptprice]-round($db1->dt[ptprice]/1.1))+($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1)))."</td>";
				$mstring .= "<td>".intval($db1->dt[ptprice]+$db1->dt[delivery_price])."</td>";

				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? $db1->dt[account_price]:round($db1->dt[account_price]/1.1))+(round($db1->dt[delivery_price]/1.1)))."</td>";
				$mstring .= "<td>".(($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[account_price]-round($db1->dt[account_price]/1.1))+($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1)))."</td>";
				$mstring .= "<td>".intval($db1->dt[account_price]+$db1->dt[delivery_price])."</td>";

				$mstring .= "<td>".($db1->dt[commission])."</td>";
				$mstring .= "<td>".($db1->dt[ptprice]-$db1->dt[account_price])."</td>";

				$mstring .= "<td>".($boid != $db1->dt[oid] ? $db1->dt[use_cupon_price]:"0")."</td>";
				$mstring .= "<td>".($boid != $db1->dt[oid] ? $db1->dt[use_reserve_price]:"0")."</td>";
				$mstring .= "<td>".($db1->dt[surtax_yorn] == "Y" ? "면세":"과세")."</td>";
				$mstring .= "<td>".($db1->dt[status] == "AR" ? "정산대기":"정산완료")."</td>";
				$mstring .= "<td>".$receipt_str."</td>";
				$mstring .= "<td>".$method."</td>";
				$mstring .= "<td>".$db1->dt[bname]."</td>";
				$mstring .= "<td>".$db1->dt[mem_group]."</td>";
				$mstring .= "</tr>";
			}else{
				$mstring .= ($i+1)."\t";
				$mstring .= $db1->dt[com_name]."\t";
				$mstring .= $db1->dt[ac_date]."\t";
				$mstring .= $db1->dt[bank_name]."\t";
				$mstring .= $db1->dt[bank_number]."\t";
				$mstring .= $db1->dt[bank_owner]."\t";
				$mstring .= $db1->dt[ac_date]."\t";
				$mstring .= $db1->dt[ac_cnt]."\t";
				$mstring .= $db1->dt[sell_total_price]."\t";
				$mstring .= ($db1->dt[sell_total_price]-$db1->dt[ac_price])."\t";
				$mstring .= ($db1->dt[ac_price])."\t";
				$mstring .= $db1->dt[shipping_fee]."\t";
				$mstring .= ($db1->dt[taxbill_yn] != "Y" ? "정산완료":"정산승인대기")."\n";
			}

			$boid = $db1->dt[oid];
		}

	}

	if($acc_view_type == "report"){
		$mstring .= "</table>";
	}

	echo iconv("utf-8","CP949",$mstring);
	exit;
}

$Script ="
<script language='javascript'>
function init_date(FromDate,ToDate) {
	var frm = document.search_frm;


	for(i=0; i<frm.vFromYY.length; i++) {
		if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
			frm.vFromYY.options[i].selected=true
	}
	for(i=0; i<frm.vFromMM.length; i++) {
		if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
			frm.vFromMM.options[i].selected=true
	}
	for(i=0; i<frm.vFromDD.length; i++) {
		if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
			frm.vFromDD.options[i].selected=true
	}


	for(i=0; i<frm.vToYY.length; i++) {
		if(frm.vToYY.options[i].value == ToDate.substring(0,4))
			frm.vToYY.options[i].selected=true
	}
	for(i=0; i<frm.vToMM.length; i++) {
		if(frm.vToMM.options[i].value == ToDate.substring(5,7))
			frm.vToMM.options[i].selected=true
	}
	for(i=0; i<frm.vToDD.length; i++) {
		if(frm.vToDD.options[i].value == ToDate.substring(8,10))
			frm.vToDD.options[i].selected=true
	}



}


function onLoad(FromDate, ToDate) {
	var frm = document.search_frm;


	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

	init_date(FromDate,ToDate);

	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;

}

function ChangeUsableAcDate(frm){
	if(frm.ac_date.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}
}


function clearAll(frm){
		for(i=0;i < frm.ac_ix.length;i++){
				frm.ac_ix[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.ac_ix.length;i++){
				frm.ac_ix[i].checked = true;
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

function accounts_init(frm){
	var check_bool = false;
	frm.act.value = 'initialize';

	for(i=0;i < frm.ac_ix.length;i++){
			if(frm.ac_ix[i].checked){
				check_bool = true;
			}
	}

	if(check_bool){
		frm.submit();
	}else{
		alert(language_data['accounts.php']['A'][language]);//체크박스를 하나 이상 선택 하셔야 합니다.
	}
}

function Account(ac_ix, company_id){
	var frm = document.account_frm;

	if(confirm(language_data['accounts.php']['B'][language])){//해당 정산내용을 정산확인 처리 하시겠습니까?
		frm.company_id.value = company_id;
		frm.ac_ix.value = ac_ix;
		frm.submit();
	}
}

</script>
";
$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("정산완료내역", "정산관리 > 정산완료내역")."</td>
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
	                                <td class='box_02' onclick=\"document.location.href='?acc_view_type='\">업체별 정산 요약</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                        <table id='tab_02' ".($acc_view_type == "detail" ? "class='on'":"").">
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='?acc_view_type=detail'\">업체별 정산 상세</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
													<table id='tab_02' ".($acc_view_type == "report" ? "class='on'":"").">
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='?acc_view_type=report'\">업체별 정산 보고서</td>
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
		<td align='left' colspan=4 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정보검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	</table>
	<form name='search_frm' method='get' action=''><input type=hidden name='acc_view_type' value='$acc_view_type'>

	<table width='100%' class='search_table_box'>
	<col width=15%>
	<col width=35%>
	<col width=15%>
	<col width=35%>
	<tr height=30>
		<td class='search_box_title'> 정산일  <input type='checkbox' name='ac_date' id='ac_date' value=1 onclick='ChangeUsableAcDate(document.search_frm);' ".CompareReturnValue("1",$ac_date,"checked")." /></td>
		<td class='search_box_item' colspan=3>
			<table border=0 cellpadding=0 cellspacing=0>
				<TD nowrap>
				<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
				<TD style='padding:0 10px;' align=left>~</TD>
				<TD  nowrap>
				<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월
				<SELECT name=vToDD></SELECT> 일</TD>
			</table>
		</td>
	</tr>";
if($admininfo[admin_level] == 9){
$Contents .= "
	<tr height=30>
		<td class='search_box_title'> 업체검색  </td>
		<td class='search_box_item' colspan=3>".CompanyList2($company_id,"")."<!--업체명 <input type='text' name='com_name' value=''>--></td>
	</tr>";
}
$Contents .= "
	<tr height=30>
		<td class='search_box_title'> 정산상태</td>
		<td class='search_box_item' colspan=3>
		<input type='radio' name='status'  id='status' value='' ".ReturnStringAfterCompare($status, "", " checked")."><label for='status'>전체</label>
		<input type='radio' name='status'  id='status_ac' value='AC' ".ReturnStringAfterCompare($status, "AC", " checked")."><label for='status_ac'>정산완료</label>
		<input type='radio' name='status'  id='status_ar' value='AR' ".ReturnStringAfterCompare($status, "AR", " checked")."><label for='status_ar'>정산승인대기</label>
		</td>
	</tr>
	<!--tr bgcolor=#ffffff height=30>
		<td><img src='../image/ico_dot.gif' align=absmiddle>정산수행</td>
		<td colspan=3>
			<table cellpadding=3 cellspacing=0>
				<tr>
					<td><input type=radio name='account_auto' value='1' id='account_auto_1'  ".CompareReturnValue("1",$db->dt[account_auto],"checked")."><label for='account_auto_1'>자동수행</label>
	    			<input type=radio name='account_auto' value='0' id='account_auto_2'  ".CompareReturnValue("0",$db->dt[account_auto],"checked")."><label for='account_auto_2'>수동수행</label></td>
				</tr>
				<tr>
					<td><span class=small><b>외부 호스팅</b>을 받으시는 고객님의 경우 <b>자동수행</b>으로 정산을 원하시는 경우  URL(<u>http://".$HTTP_HOST."/clone/account.php</u>) 을 <b>clone</b> 에 등록해주시기 바랍니다</span></td>
	    		</tr>
			</table>
		</td>
	</tr>
	<tr height=1><td colspan=4 class='dot-x'></td></tr-->
</table>
<table width='100%' >
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td>
	</tr>
	</form>
</table>";

$Contents .= "
<form name=listform method=post action='accounts.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='page' value='$page'>
<input type=hidden  id='ac_ix' value=''>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >";

$Contents .= "
	<tr>
		<td align='right' colspan=15>
		<!--a href='?mode=excel&acc_view_type=".$acc_view_type."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a-->
		<a href='accounts_excel2003.php?mode=excel&acc_view_type=".$acc_view_type."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>
		</td>
	</tr>
</table>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='30' align='center'>
		";
if($acc_view_type == "detail" || $acc_view_type == "report"){
$Contents .= "
		<td width='12%' align='center' class='s_td'><font color='#000000'><b>업체명</b></font></td>
		<td width='8%' align='center' class='m_td'><font color='#000000'><b>정산일</b></font></td>
		<!--td width='10%' class='m_td'><b>상품코드</b></td-->
		<td width='*' class='m_td'><b>주문번호/제품정보</b></td>
		<!--td width='15%' class='m_td'><b>옵션</b></td-->
		<td width='5%' class='m_td' nowrap><b>비고</b></td>
		<td width='5%' class='m_td' nowrap><b>수량</b></td>
		<td width='8%'  class='m_td' nowrap><b>단가</b></td>
		<td width='7%' class='m_td' nowrap><b>판매액</b></td>
		<td width='5%' class='m_td' nowrap><b>수수료율</b></td>
		<td width='7%' class='m_td ' nowrap><b>수수료</b></td>
		<td width='8%' class='m_td ' nowrap><b>정산금액</b></td>
		<td width='2%' align='center' class='e_td' nowrap><font color='#000000' ><b>배송비 정산금액</b></font></td>";
}else{
$Contents .= "
		<td width='5%' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
		<td width='15%' align='center' class='m_td'><font color='#000000'><b>업체명</b></font></td>
		<td width='10%' align='center' class='m_td'><font color='#000000'><b>정산일</b></font></td>
		<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>판매건수</b></font></td>
		<!--td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>선불배송비</b></font></td-->
		<td width='15%' align='center' class='m_td'><font color='#000000'><b>판매총액</b></font></td>
		<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>수수료</b></font></td>
		<td width='10%' align='center' class='m_td'><font color='#000000'><b>정산금액</b></font></td>
		<td width='13%' align='center' class='m_td'><font color='#000000' class=small><b>배송비정산금액</b></font></td>
		<td width='15%' align='center' class='m_td' nowrap><font color='#000000'><b>정산상태</b></font></td>
		<td width='25%' align='center' class='e_td' nowrap><font color='#000000' class=small><b>정상확인/세금계산서</b></font></td>
	</tr>";
}

if($db1->total){
	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);


$Contents .= "
  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" align='center'>
    ";

if($acc_view_type == "detail" || $acc_view_type == "report"){
$Contents .= "
		<td class='list_box_td list_bg_gray' nowrap><a href='accounts_detail.php?ac_ix=".$db1->dt[ac_ix]."'>".$db1->dt[com_name]."</a></td>
		<td class='list_box_td point' nowrap>".substr($db1->dt[ac_date],0,4)."-".substr($db1->dt[ac_date],4,2)."-".substr($db1->dt[ac_date],6,2)."</td>
		
		<td class='list_box_td '>
		<table>
			<tr>
				<td>
				<div style='border:1px solid silver;padding:5px;width:60px;text-align:center;'><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], "c")."'  width=50><!--img src=\"".$admin_config[mall_data_root]."/images/product/c_".$db1->dt[pid].".gif\" width=50 height=50--></div>
				</td>
				<td>
				<div align='left' style='padding:5px 0 5px 10px;line-height:120%'>
					<b class='blue'>".$db1->dt[oid]."</b><br>
					<a href=\"javascript:PoPWindow('/shop/goods_view.php?id=".$db1->dt[pid]."','1000','700','preview')\">".$db1->dt[pname]."</a>
					".($db1->dt[option_text] ? "<br><b>옵션</b>:".$db1->dt[option_text]:"")."
				</div>
				</td>
			</tr>
		</table>
		</td>
		<td class='list_box_td list_bg_gray'>".$db1->dt[option_etc1]."</td>
		<td class='list_box_td '>".$db1->dt[pcnt]." 개</td>
		<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
		<td class='list_box_td str'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
		<td class='list_box_td list_bg_gray'>".number_format($db1->dt[commission])." %</td>
		<td class='list_box_td '>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[ptprice]-$db1->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
		<td class='list_box_td list_bg_gray point'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
		<td class='list_box_td '>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[delivery_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";

}else{
$Contents .= "
		<td class='list_box_td list_bg_gray'><input type=checkbox name='ac_ix[]' id='ac_ix' value='".$db1->dt[ac_ix]."'></td>
    <td class='list_box_td ' nowrap><a href='accounts_detail.php?ac_ix=".$db1->dt[ac_ix]."'>".$db1->dt[com_name]."</a></td>
    <td class='list_box_td list_bg_gray' nowrap>".substr($db1->dt[ac_date],0,4)."-".substr($db1->dt[ac_date],4,2)."-".substr($db1->dt[ac_date],6,2)."</td>
    <td class='list_box_td ' nowrap>".$db1->dt[ac_cnt]."</td>
    <!--td class='list_box_td list_bg_gray'>".number_format($db1->dt[shipping_fee])." </td-->
    <td class='list_box_td list_bg_gray point'style='padding-left:10px' bgcolor=#efefef nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[sell_total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
    <td class='list_box_td 'style='padding-left:10px' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[sell_total_price]-$db1->dt[ac_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
    <td class='list_box_td list_bg_gray point' nowrap>
    	".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[ac_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
    	<!--".number_format($db1->dt[ac_price]+$db1->dt[shipping_fee])." 원-->
    </td>
    <td class='list_box_td'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[shipping_fee])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
    if($db1->dt[status] == "AR"){
		$Contents .= "<td class='list_box_td list_bg_gray'>정산승인대기</td>";
		}else if($db1->dt[status] == "AC"){
		$Contents .= "<td class='list_box_td list_bg_gray'>정산완료</td>";
		}


	$Contents .= "<td class='list_box_td 'style='padding:5px ;' nowrap>
							".($admininfo[admin_level] == 9 ? "<img src='../images/".$admininfo["language"]."/btn_account_confirm.gif' align=absmiddle onClick=\"Account('".$db1->dt[ac_ix]."','".$db1->dt[company_id]."')\" style='cursor:pointer;margin-bottom:3px;'>":"")."
								<img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('accounts_taxbill.php?company_id=".$db1->dt[company_id]."&ac_ix=".$db1->dt[ac_ix]."',680,450,'sendsms')\" style='cursor:pointer;margin-bottom:3px;'>
								<a href='#' style='line-height:20px;margin-bottom:3px;' onclick=\"PROC.location.href='proc.send_tax.php?company_id=".$db1->dt[company_id]."&ac_ix=".$db1->dt[ac_ix]."'\">
									<img src='../images/".$admininfo["language"]."/btn_taxbill_out.gif' align=absmiddle>
								</a>
							</td>";
}
  $Contents .= "</tr>";
	}
}else{
		if($acc_view_type == "detail"){
			$Contents .= "<tr height=50><td colspan=13 align=center>조회된 결과가 없습니다.</td></tr>
							";
		}else if($acc_view_type == "report"){
			$Contents .= "<tr height=50><td colspan=13 align=center>조회된 결과가 없습니다.</td></tr>
							";
		}else{
			$Contents .= "<tr height=50><td colspan=10 align=center>조회된 결과가 없습니다.</td></tr>
							";
		}
	}
$Contents .= "
	</table>
	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan=6 align=left valign=middle style='font-weight:bold' nowrap>";

if($admininfo[admin_id] == "forbiz"){
//$Contents .= "    <a onclick='accounts_init(document.listform)'>선택항목 정산 초기화</a>";
}
$Contents .= "</td>


    <td colspan='4' align='right' ></td>
  </tr>
  <tr><td colspan=10 align=center>&nbsp;".page_bar($total, $page, $max,"&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&company_id=$company_id&status=$status&acc_view_type=$acc_view_type","")."&nbsp;</td></tr>
</table>
</form>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>정산금액</b>은 업체에 지불해야 하는 정산 금액을 말합니다 , <b>수수료</b>는 입점업체들이 귀사에 지불하는 수수료를 의미 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세금계산서 출력전 내역을 확인하실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산일을 클릭하시면 정산 완료에 대한 상세 내역을 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세금계산서 보기를 클릭하시면 세금계산서를 볼수 있으며 출력후엔 다시보기가 불가능합니다. 재출력을 원하시면 더줌으로 연락주시기 바랍니다.</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= HelpBox("정산관리", $help_text);
$Contents .= "<form name=account_frm method=post action='accounts.act.php' onsubmit=\"alert(language_data['accounts.php']['C'][language]);return false;\" target='act'><!--일괄정산 준비중입니다-->
			<input type='hidden' name='act' value='account_confirm'>
			<input type=hidden name='ac_ix' value=''>
			<input type=hidden name='company_id' value=''>
		</form>";


$P = new LayOut();
$P->strLeftMenu = chainstore_menu();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeUsableAcDate(document.search_frm);";
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$P->Navigation = "정산관리 > 정산완료내역";
$P->title = "정산완료내역";
$P->strContents = $Contents;


echo $P->PrintLayOut();


function SelectQuickLink($QuickCode, $deliverycode){
	$divname = array ("#",
	"http://www.ilogen.com/customer/reserve_03-1_ok.asp?f_slipno=",
	"http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo=",
	"http://samsunghth.com/homepage/searchTraceGoods/SearchTraceDtdShtno.jhtml?dtdShtno=",
	"#",
	"#",
	"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=",
	"http://www.kgbls.co.kr/tracing.asp?number=",
	"http://www.yellowcap.co.kr/branch/chase/listbody.html?a_gb=branch&a_cd=5&a_item=0&f_slipno=",
	"#");


	return "<a href='".$divname[$QuickCode]."$deliverycode' target=_blank>$deliverycode</a>";

}

?>
<iframe name="PROC" width="100%" height="100"></iframe>