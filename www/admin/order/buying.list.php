<?
include("../class/layout.class");

if ($vFromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}

$db1 = new Database;
$odb = new Database;
$ddb = new Database;
//$title_str = getOrderStatus($type);
if(!$title_str){
	$title_str  = "사입목록";
}

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-84600*date("w"));
$lastday = date("Ymd", time()+84600*(6-date("w")));



$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
$where = "WHERE od.status not in ('SR','IR','CA') and od.bc_ix != '' AND od.product_type NOT IN (".implode(',',$sns_product_type).")  ";

if ($bc_ix != "")		$where .= "and od.bc_ix = '$bc_ix' ";
if ($bname != "")	$where .= "and bname = '$bname' ";
if ($rname != "")	$where .= "and rname = '$rname' ";
if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";
if($date_type){
	if ($vFromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if($search_type && $search_text){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}

if(is_array($type)){
	for($i=0;$i < count($type);$i++){


		if($type[$i]){
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ", '".$type[$i]."' ";
			}
		}
	}

	if($type_str != ""){
		$where .= "and od.status in ($type_str) ";
	}
}else{
	if($type){
		$where .= "and od.status = '$type' ";
	}

}


$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "매출관리 > $title_str ")."</td>
	</tr>
	<!--tr>
		<td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> $title_str </b></div>")."</td>
	</tr-->
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' valign=top>
			<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05' style='padding:1px;'>
									<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 0 0;'>
										<table cellpadding=0 cellspacing=0 width='100%' border='0'>
											<tr>
												<th width=120 bgcolor='#efefef' align='center'>처리상태 : </th>
												<td width='*' style='padding:5px 5px 5px 5px;' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<TR>
														<TD width='20%'><input type='checkbox' name='type[]' id='type_ir' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_ir'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
														<TD width='20%'><input type='checkbox' name='type[]' id='type_ic' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_ic'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
														<TD width='20%'><input type='checkbox' name='type[]' id='type_oc' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_oc'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
														<TD width='20%'><input type='checkbox' name='type[]' id='type_or' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_or'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
														<TD width='20%'><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
													</TR>
													<TR>

														<TD><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_ei' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_ei'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_ed' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_ec' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>


													</TR>
													<TR>
														<TD><input type='checkbox' name='type[]' id='type_r1' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_r2' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_ca' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_ca'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_xx' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_xx'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>


													</TR>
												</TABLE>
												</td>
											</tr>
											 <tr height=1><td colspan=4 class='dot-x'></td></tr>

											<tr height=30>
												<th bgcolor='#efefef' style='padding-left:7px;' align='center'>

												<select name='date_type'>
												<option value='o.date' ".CompareReturnValue('o.date',$date_type,' selected').">주문일자</option>
												<option value='o.bank_input_date' ".CompareReturnValue('o.bank_input_date',$date_type,' selected').">입금일자</option>

												</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td align=left  style='padding-left:5px;'>
													<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
														<tr>
															<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
															<TD style='padding:0 5px;' align=center> ~ </TD>
															<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
														
													

															<td style='padding-left:5px;' colspan=2>";

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

							$Contents .= "
															<a href=\"javascript:init_date('$today','$today');\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
															<a href=\"javascript:init_date('$vyesterday','$vyesterday');\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
															<a href=\"javascript:init_date('$voneweekago','$today');\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
															<a href=\"javascript:init_date('$v15ago','$today');\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
															<a href=\"javascript:init_date('$vonemonthago','$today');\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
															<a href=\"javascript:init_date('$v2monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
															<a href=\"javascript:init_date('$v3monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr height=1><td colspan=4 class='dot-x'></td></tr>
											<tr height=30>
												<th bgcolor='#efefef'  align='center'>검색항목 : </th>
												<td style='padding-left:5px;'>
													<table cellpadding='0' cellspacing='0' border='0' width='100%'>
													<tr>
														<td width='10%'>
														<select name='search_type' style='font-size:11px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
															<option value='bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
															<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
															<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
															<option value='deliverycode' ".CompareReturnValue('deliverycode',$search_type,' selected').">송장번호</option>
														</select>
														</td>
														<td width='*' style='padding:0px 0px 0px 5px'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style='height:18px;'></td>
													</tr>
													</table>
												</td>
											</tr>
											<tr height=1><td colspan=4 class='dot-x'></td></tr>
											<tr height=30>
												<th bgcolor='#efefef' align='center'>업체명 : </th>
												<td  style='padding-left:5px;'>".CompanyList($company_id,"","")."</td>
											</tr>
											<tr>
												<td></td>
												<td></td>
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
			</table>
		</td>
	</tr>
	<tr>
		<td  align=center style='padding-top:10px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr></form>
</table>
<form name=listform method=post action='orders.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='page' value='$page'>
<input type=hidden id='bc_ix' value='' >
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center'>
 <tr>";


	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
	}


	$sql = "SELECT count(*) as total
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where  "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo $sql;
	$db1->query($sql);

	$db1->fetch();
	$total = $db1->dt[total];

	$sql = "SELECT od.bc_ix
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					GROUP BY od.bc_ix ORDER BY date DESC LIMIT $start, $max";
	//echo $sql;
	$db1->query($sql);


 $Contents .= "<td colspan=3 align=left><b>전체 주문수 : $total 건</b></td><td colspan=9 align=right>
  	<a href=\"javascript:mybox.service('/admin/order/excel_out.php?".$QUERY_STRING."','10','500','900', 4, [], Prototype.emptyFunction, [], 'HOME > 주문관리 > 주문내역저장하기');\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";

if($admininfo[admin_level] == 9){

$Contents .= " <a href='orders.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";

}else if($admininfo[admin_level] == 8){
$Contents .= "<span style='color:red'>! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능</span> ";
$Contents .= "<a href='orders.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
//$Contents .= "<a href='orders.excel.hanjin.php?".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}
$Contents .= "
  </td>
  </tr>
	<tr height='25' >
		<td class='s_td' width='5%'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
		<td width='14%' align='center' class='m_td'><font color='#000000' class=small><b>사입처</b></font></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' class=small><b>층/호수</b></font></td>
		<td width='*' align='center' class='m_td' nowrap><font color='#000000' class=small><b>제품명</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>결제방법</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>상품금액</b></font></td>
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>수량</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>주문금액</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>배송비</b></font></td>
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>수수료</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>처리상태</b></font></td>
		<td width='7%' align='center' class='e_td' nowrap><font color='#000000' class=small><b>관리</b></font></td>
	</tr>

  ";



if($db1->total){
	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);

		if($admininfo[admin_level] == 9){
			$sql = "SELECT o.oid, od.bc_ix, od.pname, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, bname, mem_group,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.bc_ix = '".$db1->dt[bc_ix]."' and od.bc_ix != '' and od.status not in ('SR','IR','CA') AND od.product_type NOT IN (".implode(',',$sns_product_type).")
						ORDER BY company_id DESC ";


		}else if($admininfo[admin_level] == 8){
			$sql = "SELECT o.oid, od.bc_ix, od.pname, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, bname, mem_group,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,
						(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_totalprice,
						(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as company_total,
						(select delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.bc_ix = '".$db1->dt[bc_ix]."' and od.bc_ix != '' and od.company_id ='".$admininfo[company_id]."' and  od.status not in ('SR','IR','CA') AND od.product_type NOT IN (".implode(',',$sns_product_type).")
						ORDER BY company_id DESC ";


		}


		$ddb->query($sql);
		$od_count = $ddb->total;

		$status = getOrderStatus($db1->dt[status]);

		$psum = number_format($ddb->dt[total_price]);

	$bcompany_id = '';
	for($j=0;$j < $ddb->total;$j++){
		$ddb->fetch($j);

		if ($ddb->dt[method] == ORDER_METHOD_CARD)
		{
			if($ddb->dt[bank] == ""){
				$method = "카드결제";
			}else{
				$method = $db1->dt[bank];
			}
			$receipt_y = "카드결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_BANK){
			$method = "계좌입금";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_PHONE){
			$method = "전화결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_AFTER){
			$method = "후불결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_VBANK){
			$method = "가상계좌";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_ICHE){
			$method = "실시간계좌이체";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_ASCROW){
			$method = "가상계좌[에스크로]";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}

		if($ddb->dt[delivery_pay_type] == "1"){
			$delivery_pay_type = "선불";
		}elseif($ddb->dt[delivery_pay_type] == "2"){
			$delivery_pay_type = "착불";
		}else{
			$delivery_pay_type = "무료";
		}

		$one_status = getOrderStatus($ddb->dt[status],$ddb->dt[method])."<input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."' value='".$ddb->dt[status]."'>";

		if($ddb->dt[gift] != ""){
			$od_count_plus = 0;
		}else{
			$od_count_plus = 0;
		}

		//$Contents .= "<tr ".($ddb->dt[bc_ix] != $b_bc_ix  ? "style='background-color:#efefef'":"")." height=28 >";// kbk
		$Contents .= "<tr height=28 >";
		if($ddb->dt[bc_ix] != $b_bc_ix){
			$Contents .= "<td ".($ddb->dt[bc_ix] != $b_bc_ix ? "rowspan='".($od_count+$od_count_plus)."'":"")." class=dot-x nowrap align='center'><input type=checkbox name='bc_ix[]' id='bc_ix' value='".$ddb->dt[bc_ix]."' ".($ddb->dt[status] == "AC" ? "disabled":"")." ><input type=hidden name='bstatus[".$ddb->dt[bc_ix]."]' value='".$ddb->dt[status]."'><input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[bc_ix])."'></td>";
			//$Contents .= "<td ".($ddb->dt[bc_ix] != $b_bc_ix ? "rowspan='".($od_count)."'":"")." class=dot-x align=center></td>";
			$Contents .= "<td ".($ddb->dt[bc_ix] != $b_bc_ix ? "rowspan='".($od_count+$od_count_plus)."'":"")." class=dot-x style='line-height:140%' align=center>";

			$Contents .= "<a href=\"orders.read.php?bc_ix=".$ddb->dt[bc_ix]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[bc_ix]."</a><br></td>";
			$Contents .= "<td ".($ddb->dt[bc_ix] != $b_bc_ix ? "rowspan='".($od_count+$od_count_plus)."'":"")." style='line-height:140%' align=center class=dot-x> 2층 201호</td>";
		}
		$Contents .= "
						<td class=dot-x style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD><a href='../product/goods_input.php?id=".$ddb->dt[pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], "c")."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50></a></TD>
									<td width='5'></td>
									<TD class=small style='line-height:140%'>";

			$Contents .= cut_str($ddb->dt[pname],30)."
									</TD>
								</TR>
							</TABLE>
						</td>
						<td class=dot-x align='center'  nowrap>".$method."</td>
						<td class=dot-x align=center>".number_format($ddb->dt[psprice])."</td>
						<td class=dot-x align=center>".number_format($ddb->dt[pcnt])."</td>
						<td class=dot-x align=center>".number_format($ddb->dt[ptprice])."</td>";

					$Contents .="<td class=dot-x align=center >-</td>";


					$Contents .="	<td class=dot-x align=center>".number_format($ddb->dt[commission])."</td>
						<td class=dot-x align='center'>".$one_status."</td>";
		if($ddb->dt[bc_ix] != $b_bc_ix){
			$Contents .= "<td ".($ddb->dt[bc_ix] != $b_bc_ix ? "rowspan='".($od_count)."'":"")." class=dot-x align='center'  nowrap><!--img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('taxbill.php?uid=".$ddb->dt[uid]."&bc_ix=".$ddb->dt[bc_ix]."',680,800,'sendsms')\" style='cursor:hand;'-->";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<a href=\"orders.edit.php?bc_ix=".$ddb->dt[bc_ix]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .= "<br>".$delete;
			}

			$Contents .= "</td>";
		}
		$Contents .= "</tr>";

		if(($od_count-1) == $j && $admininfo[admin_level] == 9){
			$Contents .= "<tr >
							<td class=dot-x style='background-color:#efefef;height:30px;font-weight:bold;padding:0 0 0 10px' class=blue colspan=4>
							 <span class='small blue'>".getDeliveryPrice2($ddb->dt[bc_ix])."</span>
							</td>
							<td class=dot-x  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=4>
							<span>현금영수증 : ".$receipt_y."</span>
							</td>
							<td class=dot-x  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=4>
							<b style='color:red;'>결제금액 : ".number_format($db1->dt[payment_price])." 원</b>
							</td>
						</tr>";
		}

		$b_bc_ix = $ddb->dt[bc_ix];
		$bcompany_id = $ddb->dt[company_id];
	}
	//$Contents .= "<tr height=3><td colspan=10 bgcolor='#DDDDDD'></td></tr>";
	}
}else{
$Contents .= "<tr height=50><td colspan=12 align=center>조회된 결과가 없습니다.</td></tr>
			<tr height=1><td colspan=12 class='dot-x'></td></tr>";
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
  <tr height=40>
    <td colspan=7 align=left valign=middle style='font-weight:bold' nowrap>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

 $Contents .= "선택된 항목 ";
if($admininfo[admin_level] == 9){
$Contents .= "
    	<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
				<option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
				<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
				<!--option value='".ORDER_STATUS_ADVANCE_ING."' >".getOrderStatus(ORDER_STATUS_ADVANCE_ING)."</option-->
				<option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
				<!--option value='".ORDER_STATUS_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option-->
				<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' >".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
				<!--option value='".ORDER_STATUS_EXCHANGE_APPLY."' >".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
				<option value='".ORDER_STATUS_RETURN_APPLY."' >".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
				<option value='".ORDER_STATUS_RETURN_COMPLETE."' >".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</option-->
				<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
			</select>";
}else if($admininfo[admin_level] == 8){
$Contents .= "<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
				<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
				<!--option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option-->
			</select>";
}
$Contents .= "로 상태변경
	<div id='invoice' style='display:none'>
		".deliveryCompanyList($db3->dt[quick],"select")." <div id='deliverycode' style='display:inline'><input type='text' name='deliverycode'   size=15 value='".$db3->dt[invoice_no]."'> * 좌측에 송장번호를 입력해주세요 </div>
	</div>
	<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle>";
}
$Contents .= "

    </td>
  </tr>
  <tr height=40>
    <td colspan='12' align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문번호를 클릭하시면 주문에 대한 상세 정보를 보실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 변경하시려면 수정버튼을 누르세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 빠르게 변경하시려면 변경하시고자 하는 주문 선택후 아래 변경하고자 하는 상태를 선택하신후 수정버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>주문총액</b>은 <u>배송비 미포함 금액</u>입니다.</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("사입목록", $help_text);
$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='orders.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n";
$P->Navigation = "HOME > 주문관리 > 사입목록";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>