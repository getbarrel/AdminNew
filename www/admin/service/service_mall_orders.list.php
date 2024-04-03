<?
include_once("../class/layout.class");
include_once("service.lib.php");
//print_r($admin_config);
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
	$title_str  = "주문리스트";
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
$where = "WHERE od.status <> 'SR' AND od.status!='' ";

if ($oid != "")		$where .= "and od.oid = '$oid' ";
if ($mall_name != "")	$where .= "and mall_name = '$mall_name' ";
if ($rname != "")	$where .= "and rname = '$rname' ";
if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";
if($date_type){
	if ($vFromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if($search_type && $search_text){
		if($search_type == "combi_name"){
			$where .= "and (mall_name LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}
//print_r($type);
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

if(is_array($method)){
	for($i=0;$i < count($method);$i++){
		if($method[$i] != ""){
			if($method_str == ""){
				$method_str .= "'".$method[$i]."'";
			}else{
				$method_str .= ",'".$method[$i]."' ";
			}
		}
	}

	if($method_str != ""){
		$where .= "and o.method in ($method_str) ";
	}
}else{
	if($method){
		$where .= "and o.method = '$method' ";
	}
}

if(is_array($payment_agent_type)){
	for($i=0;$i < count($payment_agent_type);$i++){
		if($payment_agent_type[$i] != ""){
			if($payment_agent_type_str == ""){
				$payment_agent_type_str .= "'".$payment_agent_type[$i]."'";
			}else{
				$payment_agent_type_str .= ", '".$payment_agent_type[$i]."' ";
			}
		}
	}

	if($payment_agent_type_str != ""){
		$where .= "and o.payment_agent_type in ($payment_agent_type_str) ";
	}
}else{
	if($payment_agent_type){
		$where .= "and o.payment_agent_type = '$payment_agent_type' ";
	}
}

$Contents = "


<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td colspan=2>
			".OrderSummary($pre_type, $title_str)."
		</td>
	</tr>
	<tr>";
$Contents .= "
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=160 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 3px 0;height:120px;'>
										<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>";
if(!$pre_type){
$Contents .= "

											<tr>
												<th class='search_box_title' >처리상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=25>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_READY."'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_WAREHOUSING_STANDYBY."' value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSING_STANDYBY,$type,' checked')." ><label for='type_".ORDER_STATUS_WAREHOUSING_STANDYBY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_ING."'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
													</TR>
													<TR height=25>



														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_SOLDOUT_CANCEL."' value='".ORDER_STATUS_SOLDOUT_CANCEL."' ".CompareReturnValue(ORDER_STATUS_SOLDOUT_CANCEL,$type,' checked')." ><label for='type_".ORDER_STATUS_SOLDOUT_CANCEL."'>".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</label></TD>

													</TR>
													<TR height=25>
														<TD>
															<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_APPLY."' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." >
															<label for='type_".ORDER_STATUS_RETURN_APPLY."'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label>
														</TD>
														<TD>
															<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' ".CompareReturnValue(ORDER_STATUS_RETURN_ING,$type,' checked')." >
															<label for='type_".ORDER_STATUS_RETURN_ING."'>".getOrderStatus(ORDER_STATUS_RETURN_ING)."</label></TD>
														<TD >
															<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_DELIVERY."' value='".ORDER_STATUS_RETURN_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_RETURN_DELIVERY,$type,' checked')." >
															<label for='type_".ORDER_STATUS_RETURN_DELIVERY."'>".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)."</label></TD>
														<TD>
															<input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." >
															<label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_REFUND_APPLY."' value='".ORDER_STATUS_REFUND_APPLY."' ".CompareReturnValue(ORDER_STATUS_REFUND_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_REFUND_APPLY."'>".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_REFUND_COMPLETE."' value='".ORDER_STATUS_REFUND_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_REFUND_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_REFUND_COMPLETE."'>".getOrderStatus(ORDER_STATUS_REFUND_COMPLETE)."</label></TD>

													</TR>
													<TR height=25>
														<TD>
															<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." >
															<label for='type_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label>
														</TD>
														<TD>
															<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." >
															<label for='type_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
														<TD >
															<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_DELIVERY."' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." >
															<label for='type_".ORDER_STATUS_EXCHANGE_DELIVERY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
														<TD>
															<input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." >
															<label for='type_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>
														<td></td>
													</TR>
												</TABLE>
												</td>
											</tr>";
}else if($pre_type == "EA"){
$Contents .= "
											<tr>
												<th class='search_box_title' >처리상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>

													<TR height=30>
														<TD><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_ei' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_ei'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_r1' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_r2' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>
														<td></td>

													</TR>
												</TABLE>
												</td>
											</tr>";
}
$Contents .= "
											<tr height=30>
												<th class='search_box_title' >검색항목 : </th>
												<td class='search_box_item' colspan=3>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<tr>
														<td width='22%'>
														<select name='search_type' style='font-size:11px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
															<option value='mall_name' ".CompareReturnValue('mall_name',$search_type,' selected').">주문업체(쇼핑몰)</option>
															<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
															<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
															<option value='deliverycode' ".CompareReturnValue('deliverycode',$search_type,' selected').">송장번호</option>
														</select>
														</td>
														<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
											</tr>
											<tr height=33>
												<th class='search_box_title' >
												<!--label for='visitdate'>주문일자</label-->
												<select name='date_type'>
												<option value='o.date' ".CompareReturnValue('o.date',$date_type,' selected').">주문일자</option>
												<option value='o.bank_input_date' ".CompareReturnValue('o.bank_input_date',$date_type,' selected').">입금일자</option>
												<!--option value='date'>취소일자</option-->
												</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item'  colspan=3>
													<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
														<tr>
															<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
															<TD style='padding:0 5px;' align=center> ~ </TD>
															<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
															<td>";

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

											<tr height=30>
												<th class='search_box_title' >결제방법 : </th>
												<td class='search_box_item'  colspan=3>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."'>".getMethodStatus(ORDER_METHOD_BANK)."</label>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."'>".getMethodStatus(ORDER_METHOD_CARD)."</label>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."'>".getMethodStatus(ORDER_METHOD_VBANK)."</label>

												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."'>".getMethodStatus(ORDER_METHOD_ICHE)."</label>

												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."'>".getMethodStatus(ORDER_METHOD_PHONE)."</label>
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' >결제형태 : </th>
												<td class='search_box_item' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')."><label for='payment_agent_type_W'>일반(웹)결제</label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')."><label for='payment_agent_type_M'>모바일결제</label>
												</td>";

												if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
													$Contents .= "
												<th class='search_box_title'>업체명 : </th>
												<td class='search_box_item'></td>";
												}
												$Contents .= "
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
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr></form>
</table>
<form name=listform method=post action='service_mall_orders.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='page' value='$page'>
<input type=hidden id='oid' value='' >
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";


	/*if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid ";
		}

		if($admininfo[mem_type] == "MD"){
			$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
	}*/


	$sql = "SELECT count(distinct od.oid) as total
					FROM service_mall_order o LEFT JOIN service_mall_order_detail od ON o.oid=od.oid
					$where "; //, ".TBL_SHOP_PRODUCT." p, service_mall_order_detail od
	//echo $sql;
	$db1->query($sql);

	$db1->fetch();
	$total = $db1->dt[total];


	$sql = "SELECT o.oid, o.payment_price
					FROM service_mall_order o LEFT JOIN service_mall_order_detail od ON o.oid=od.oid
					$where
					GROUP BY o.oid ORDER BY date DESC LIMIT $start, $max";

	//echo $sql;
	$db1->query($sql);


 $Contents .= "<td colspan=3 align=left><b>전체 주문수 : $total 건</b></td><td colspan=10 align=right>

	<a href='excel_out.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";

if($admininfo[admin_level] == 9){

$Contents .= " <a href='service_mall_orders_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";

}else if($admininfo[admin_level] == 8){
$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
$Contents .= "<a href='service_mall_orders.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
//$Contents .= "<a href='orders.excel.hanjin.php?".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}
$Contents .= "
	</td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td class='s_td ctr' width='5%'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
		<!--td width='15%' align='center' class='m_td'><font color='#000000'><b>주문일자</b></font></td-->
		<td width='14%' align='center' class='m_td'><font color='#000000' class=small><b>주문일자/주문번호</b></font></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' class=small><b>주문자명</b></font></td>
		<td width='*' align='center' class='m_td' nowrap><font color='#000000' class=small><b>제품명</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>결제방법</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>상품금액</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>주문금액</b></font></td>
		<!--".($admininfo[admin_level] == 9 ? "<td width='5%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>적립금</b></font></td>" : "")."-->
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>적립금</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>처리상태</b></font></td>
		<td width='7%' align='center' class='e_td' nowrap><font color='#000000' class=small><b>관리</b></font></td>
	</tr>

  ";



if($db1->total){
	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);

		if($admininfo[admin_level] == 9){
			/*if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}*/
			$sql = "SELECT o.oid, od.pname, od.regdate, od.psprice, od.ptprice,od.pid, mall_name,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y
						FROM service_mall_order o, service_mall_order_detail od
						where o.oid = od.oid and o.oid = '".$db1->dt[oid]."' $addWhere
						ORDER BY od.regdate DESC ";
			//echo $sql;
		/*서브쿼리 삭제 아무것도 없을때 에러남 서브쿼리 부분에서 */
		//,			(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
		//				(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total

		}else if($admininfo[admin_level] == 8){
			$sql = "SELECT o.oid, od.pname, od.regdate, od.psprice, od.ptprice,od.pid, mall_name,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y
						FROM service_mall_order o, service_mall_order_detail od
						where o.oid = od.oid and o.oid = '".$db1->dt[oid]."' ORDER BY od.regdate DESC ";

		//,(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total
		}
		$ddb->query($sql);
		$od_count = $ddb->total;

		$status = getOrderStatus($db1->dt[status]);

		$psum = number_format($ddb->dt[total_price]);

		if ($ddb->dt[status] == ORDER_STATUS_DELIVERY_COMPLETE)		{
			$delete = "<a href=\"javascript:alert(language_data['orders.list.php']['A'][language]);\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";//[처리완료] 기록은 삭제할 수 없습니다.
		}elseif ($ddb->dt[status] != ORDER_STATUS_CANCEL_COMPLETE && $db1->dt[method] == "1"){
			$delete = "<a href=\"javascript:order_delete('delete','".$db1->dt[oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";
		}else{
			$delete = "<a href=\"javascript:act('delete','".$db1->dt[oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";
		}
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
		}elseif($ddb->dt[method] == ORDER_METHOD_NOPAY){
			$method = "무료결제";
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

		if($ddb->dt[use_reserve_price]>0) {
			$use_reserve_price="<span style='font-weight:100;'>적립금 사용: ".$currency_display[$admin_config["currency_unit"]]["front"]." ".$ddb->dt[use_reserve_price]." ".$currency_display[$admin_config["currency_unit"]]["back"]."</span>";
		} else {
			$use_reserve_price="";
		}

		$one_status = getOrderStatus($ddb->dt[status],$ddb->dt[method])."<input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."' value='".$ddb->dt[status]."'>";

		if($ddb->dt[gift] != ""){
			$od_count_plus = 0;
		}else{
			$od_count_plus = 0;
		}

		//$Contents .= "<tr ".($ddb->dt[oid] != $b_oid  ? "style='background-color:#efefef'":"")." height=28 >";// kbk
		$Contents .= "<tr height=28 >";
		if($ddb->dt[oid] != $b_oid){
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='dot-x' nowrap align='center'><input type=checkbox name='oid[]' id='oid' value='".$ddb->dt[oid]."' ".($ddb->dt[status] == "AC" ? "disabled":"")." ><input type=hidden name='bstatus[".$ddb->dt[oid]."]' value='".$ddb->dt[status]."'><input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."'></td>";
			//$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class=dot-x align=center></td>";
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='dot-x  point' style='line-height:140%' align=center>";

			$Contents .= $ddb->dt[regdate]."<br>
											<a href=\"service_mall_orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[oid]."</a><br>

										</td>";
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." style='line-height:140%' align=center class=dot-x>
			".($ddb->dt[uid] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$ddb->dt[uid]."',950,500,'member_info')\" >".$ddb->dt[mall_name]."</a>":$ddb->dt[mall_name])."<br>
			<span class='small'>".$ddb->dt[mem_group]."</span></td>";
		}
		$Contents .= "
						<td class=dot-x style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD><a  href='serive_goods_input.php?id=".$ddb->dt[pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/service_product", $ddb->dt[pid], $LargeImageSize)."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/service_product", $ddb->dt[pid], "c")."'  width=50></a></TD>
									<td width='5'></td>
									<TD class=small style='line-height:140%'>";
			/*if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b></a><br>";
			}*/
			$Contents .= cut_str($ddb->dt[pname],30)."<br><b>".$ddb->dt[option_text]."</b>
									</TD>
								</TR>
							</TABLE>
						</td>
						<td class=dot-x align='center'  nowrap>".$method."</td>
						<td class=dot-x align=center>".number_format($ddb->dt[psprice])."</td>
						<td class=dot-x align=center>".number_format($ddb->dt[ptprice])."</td>
						<td class=dot-x align=center>".number_format($ddb->dt[reserve])."P</td>";
			/*if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<td class=dot-x align=center>".number_format($ddb->dt[reserve])."P</td>";
			}*/
				/*if($bcompany_id != $ddb->dt[company_id]){
					$Contents .="<td class=dot-x align=center style='line-height:140%;' ".($bcompany_id != $ddb->dt[company_id] ? "rowspan='".$ddb->dt[company_total]."'":"")."
					>".number_format($ddb->dt[delivery_totalprice])."<br>".$delivery_pay_type." </td>";
				}

						<td class=dot-x align=center>".number_format($ddb->dt[commission])." %</td>*/
					$Contents .="<td class='dot-x point' align='center'>".$one_status."</td>";
				
		if($ddb->dt[oid] != $b_oid){
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class=dot-x align='center'  nowrap><!--img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('taxbill.php?uid=".$ddb->dt[uid]."&oid=".$ddb->dt[oid]."',680,800,'sendsms')\" style='cursor:hand;'-->";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<a href=\"service_mall_orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
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
							 <span class='small blue'>".getDeliveryPrice2($ddb->dt[oid])."</span>
							</td>
							<td class=dot-x  style='background-color:#efefef;height:30px;font-weight:bold;padding-left:5px;'  colspan=4>
							<span>현금영수증 : ".$receipt_y."</span>
							</td>
							<td class=dot-x  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=2 align='right'>
							<b style='color:red;'>결제금액 : ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db1->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." ".$use_reserve_price."&nbsp;</b>
							</td>
						</tr>";
		}

		$b_oid = $ddb->dt[oid];
		//$bcompany_id = $ddb->dt[company_id];
	}
	//$Contents .= "<tr height=3><td colspan=10 bgcolor='#DDDDDD'></td></tr>";
	}
}else{
$Contents .= "<tr height=50><td colspan=10 align=center>조회된 결과가 없습니다.</td></tr>
		";
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</tabel>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan=13 align=left valign=middle style='font-weight:bold' nowrap>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

if($pre_type == ""){
	$Contents .= "선택된 항목을 ";
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
		".deliveryCompanyList($db3->dt[quick],"select")." <div id='deliverycode' style='display:inline'><input type='text' name='deliverycode'   size=15 value='".$db3->dt[invoice_no]."'> <!--* 좌측에 송장번호를 입력해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </div>
	</div>
	<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle>";

}else if($pre_type == "IR"){
	$Contents .= "선택된 항목을 ";
	$Contents .= "<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
					<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
				</select>";
	$Contents .= " 로 상태변경

	<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle>";
}

}
$Contents .= "

    </td>
  </tr>
  <tr height=40>
    <td colspan='13' align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
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

$Script = "";

$Contents .= HelpBox("서비스주문리스트(쇼핑몰)", $help_text);
$P = new LayOut();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='service_mall_orders.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = service_menu();
$P->Navigation = "서비스관리 > 서비스주문리스트(쇼핑몰)";
$P->title = "서비스주문리스트(쇼핑몰)";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function OrderSummary($status, $title){
	$mdb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	if($status == "IR"){
	$sql = "select '구분      ','입금예정 ', '입금예정금액','입금확인건수(카드결제 미포함)', '입금확인금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."') then ptprice else '0' end),0) as incom_ready_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_COMPLETE."')  then ptprice else '0' end),0) as incom_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."') then ptprice else '0' end),0) as incom_ready_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_COMPLETE."')  then ptprice else '0' end),0) as incom_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "CA"){
	$sql = "select '구분      ','취소신청 ', '취소신청금액','취소완료건수', '취소완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."'  then 1 else '0' end),0) as cancel_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."') then ptprice else '0' end),0) as cancel_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."'  then 1 else '0' end),0) as cancel_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_COMPLETE."')  then ptprice else '0' end),0) as cancel_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."'  then 1 else '0' end),0) as cancel_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."') then ptprice else '0' end),0) as cancel_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."'  then 1 else '0' end),0) as cancel_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_COMPLETE."')  then ptprice else '0' end),0) as cancel_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "EA"){
	$sql = "select '구분      ','교환신청 ', '교환신청금액','교환완료건수', '교환완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."'  then 1 else '0' end),0) as exchange_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice else '0' end),0) as exchange_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_COMPLETE."'  then 1 else '0' end),0) as exchange_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_COMPLETE."')  then ptprice else '0' end),0) as exchange_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."'  then 1 else '0' end),0) as exchange_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice else '0' end),0) as exchange_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_COMPLETE."'  then 1 else '0' end),0) as exchange_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_COMPLETE."')  then ptprice else '0' end),0) as exchange_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "RA"){
	$sql = "select '구분      ','반품신청 ', '반품신청금액','반품완료건수', '반품완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."'  then 1 else '0' end),0) as return_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."') then ptprice else '0' end),0) as return_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_COMPLETE."'  then 1 else '0' end),0) as return_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice else '0' end),0) as return_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."'  then 1 else '0' end),0) as return_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."') then ptprice else '0' end),0) as return_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_COMPLETE."'  then 1 else '0' end),0) as return_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice else '0' end),0) as return_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "FA"){
	$sql = "select '구분      ','환불대기 ', '환불예정금액','환불완료건수', '환불완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."'  then 1 else '0' end),0) as refund_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_APPLY."') then ptprice else '0' end),0) as refund_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."'  then 1 else '0' end),0) as refund_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_COMPLETE."')  then ptprice else '0' end),0) as refund_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."'  then 1 else '0' end),0) as refund_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_APPLY."') then ptprice else '0' end),0) as refund_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."'  then 1 else '0' end),0) as refund_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_COMPLETE."')  then ptprice else '0' end),0) as refund_complete_price
			from service_mall_order_detail where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else{
		return;
	}

	$mdb->query($sql);
	$datas = $mdb->fetchall();
	//$datas = $mdb->getrows();

	$mstring = "<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'>";
if($status == "IR"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 입금예정 현황</b>";
}else if($status == "CA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 취소신청 현황</b>";
}else if($status == "EA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 교환신청 현황</b>";
}else if($status == "RA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 반품신청 현황</b>";
}else if($status == "FA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 환불 현황</b>";
}
	$mstring .= "	</td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=100 width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=3 cellspacing=1 width='100%' border='0' bgcolor=silver>
						<col width='20%'>
						<col width='20%'>
						<col width='20%'>
						<col width='20%'>
						<col width='20%'>
						";
				for($i=0;$i<count($datas);$i++){
				if($i == 0){
					$mstring .= "
						<tr height=30 ".($i == 0 ? "bgcolor=#efefef  align='center'":"bgcolor=#ffffff  align='right'").">
							<th bgcolor='#efefef' align='center'>".$datas[$i][0]." </th>
							<td style='padding-right:15px;'> ".$datas[$i][1]."</td>
							<td style='padding-right:15px;'> ".$datas[$i][2]."</td>
							<td style='padding-right:15px;'> ".$datas[$i][3]." </td>
							<td style='padding-right:15px;'> ".$datas[$i][4]." </td>
						</tr>";
				}else{
					$mstring .= "
						<tr height=30 ".($i == 0 ? "bgcolor=#efefef  align='center'":"bgcolor=#ffffff  align='right'").">
							<th bgcolor='#efefef' align='center'>".$datas[$i][0]." </th>
							<td style='padding-right:15px;'> ".number_format($datas[$i][1])." 건</td>
							<td style='padding-right:15px;'> ".number_format($datas[$i][2])." 원</td>
							<td style='padding-right:15px;'> ".number_format($datas[$i][3])." 건</td>
							<td style='padding-right:15px;'> ".number_format($datas[$i][4])." 원</td>
						</tr>";
				}
				}
				$mstring .= "
						<!--tr bgcolor=#ffffff height=30 align='right'>
							<th bgcolor='#efefef' align='center'>최근 30일 </th>
							<td style='padding-right:15px;'>15건</td>
							<td style='padding-right:15px;'>3400,000 원</td>
							<td style='padding-right:15px;'> - </td>
							<td style='padding-right:15px;'> - </td>
						</tr-->
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;text-align:right;'>* 위 통계는 주문일 기준으로 작성 됩니다.</td>
				</tr>
			</table>";
	return $mstring;
}
?>