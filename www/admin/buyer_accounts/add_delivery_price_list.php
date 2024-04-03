<?
include_once("../class/layout.class");
//print_r($admin_config);
if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}

$db = new Database;
$db1 = new Database;
$odb = new Database;
$ddb = new Database;
$od_db = new Database;

if(!$title_str){
	$title_str  = "추가배송비 리스트";
}

if(!$parent_title){
	$parent_title  = "구매자정산관리";
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
$where = "WHERE od.status <> 'SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") and od.oid=odd.oid and od.od_ix=odd.od_ix and odd.order_type='3' and odd.add_delivery_price > 0 ";

if ($oid != "")		$where .= "and od.oid = '$oid' ";
if ($bname != "")	$where .= "and bname = '$bname' ";
if ($rname != "")	$where .= "and o.rname = '$rname' ";
if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";

if($date_type){
	if ($vFromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if($search_type && $search_text){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%'  or o.rname LIKE '%".trim($search_text)."%' or odd.bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}


if(empty($type)){
	$type = $fix_type;
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

if(is_array($method)){
	for($i=0;$i < count($method);$i++){
		if($method[$i] != ""){
			if($method_str == ""){
				$method_str .= "'".$method[$i]."'";
			}else{
				$method_str .= ", '".$method[$i]."' ";
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

if($product_type != ""){
	$where .= "and od.product_type = '".$product_type."'";
}

if(is_array($order_from)){
	for($i=0;$i < count($order_from);$i++){
		if($order_from[$i] != ""){
			if($order_from_str == ""){
				$order_from_str .= "'".$order_from[$i]."'";
			}else{
				$order_from_str .= ",'".$order_from[$i]."' ";
			}
		}
	}

	if($order_from_str != ""){
		$where .= "and od.order_from in ($order_from_str) ";
	}
}else{
	if($order_from){
		$where .= "and od.order_from = '$order_from' ";
	}
}


if(is_array($payment_yn)){
	for($i=0;$i < count($payment_yn);$i++){
		if($payment_yn[$i]){
			if($payment_yn_str == ""){
				$payment_yn_str .= "'".$payment_yn[$i]."'";
			}else{
				$payment_yn_str .= ", '".$payment_yn[$i]."' ";
			}
		}
	}

	if($payment_yn_str != ""){
		$where .= "and odd.payment_yn in ($payment_yn_str) ";
	}
}else{
	if($payment_yn){
		$where .= "and odd.payment_yn = '$payment_yn' ";
	}
}

if(is_array($payment_method)){
	for($i=0;$i < count($payment_method);$i++){
		if($payment_method[$i]){
			if($payment_method_str == ""){
				$payment_method_str .= "'".$payment_method[$i]."'";
			}else{
				$payment_method_str .= ", '".$payment_method[$i]."' ";
			}
		}
	}

	if($payment_method_str != ""){
		$where .= "and odd.payment_method in ($payment_method_str) ";
	}
}else{
	if($payment_method){
		$where .= "and odd.payment_method = '$payment_method' ";
	}
}

$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "$parent_title > $title_str ")."</td>
	</tr>
	<!--tr>
		<td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> $title_str </b></div>")."</td>
	</tr-->
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
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
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=15%>
											<col width=35%>
											<col width=15%>
											<col width=35%>
											<tr height=30>
												<th class='search_box_title' >판매처 선택 </th>
												<td class='search_box_item' nowrap colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>";

												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>
															<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." ><label for='order_from_offline'>오프라인 영업</label></TD>
															<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>";
															$db1->query("select * from sellertool_site_info where disp='1' ");
															$sell_order_from=$db1->fetchall();
															for($i=0;$i<count($sell_order_from);$i++){
																	$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
															}

										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>";
								if(!$pre_type){
										$Contents .= "
											<tr>
												<th class='search_box_title'>추가비용 결제상태 </th>
												<td class='search_box_item' colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>
															<TD><input type='checkbox' name='payment_yn[]'  id='payment_yn_n' value='N' ".CompareReturnValue('N',$payment_yn,' checked')." ><label for='payment_yn_n'>입금예정</label></TD>
															<TD><input type='checkbox' name='payment_yn[]'  id='payment_yn_y' value='Y' ".CompareReturnValue('Y',$payment_yn,' checked')." ><label for='payment_yn_y'>입금완료</label></TD>
															<TD><input type='checkbox' name='payment_yn[]'  id='payment_yn_l' value='L' ".CompareReturnValue('L',$payment_yn,' checked')." ><label for='payment_yn_l'>배송비 손실</label></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
														</TR>
													</TABLE>
												</td>
											</tr>	
										";
								}

$Contents .= "
											<tr>
												<th class='search_box_title'>처리상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=30>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_APPLY."' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_APPLY."'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";

							$Contents .= "
											<tr height=30>
												<th class='search_box_title'>검색항목 : </th>
												<td class='search_box_item' colspan=3>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='170px'>
													<col width='*'>
													<tr>
														<td >
														<select name='search_type' style='font-size:12px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
															<option value='bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
															<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='o.rname' ".CompareReturnValue('o.rname',$search_type,' selected').">수취인이름</option>
															<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
															<option value='deliverycode' ".CompareReturnValue('deliverycode',$search_type,' selected').">송장번호</option>
														</select>
														</td>
														<td ><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
											</tr>
											<tr height=33>
												<th class='search_box_title'>
												<!--label for='visitdate'>주문일자</label-->
												<select name='date_type'>
												<option value='".($db1->dbms_type == "oracle" ? 'o.date_' : 'o.date' )."' ".CompareReturnValue(($db1->dbms_type == "oracle" ? 'o.date_' : 'o.date' ),$date_type,' selected').">주문일자</option>
												<option value='o.bank_input_date' ".CompareReturnValue('o.bank_input_date',$date_type,' selected').">입금일자</option>
												<!--option value='date'>취소일자</option-->
												</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item' colspan=3>
													<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
														<tr>
															<TD  nowrap><input type='text' name='startDate' class='textbox point_color' value='".$startDate."' style='height:20px;width:100px;text-align:center;' id='start_datepicker'></TD>
															<TD style='padding:0 5px;' align=center> ~ </TD>
															<TD nowrap><input type='text' name='endDate' class='textbox point_color' value='".$endDate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker'></TD>
															<td>";

				$vdate = date("Ymd", time());
				$today = date("Ymd", time());
				$vyesterday = date("Ymd", time()-84600);
				$voneweekago = date("Ymd", time()-84600*7);
				$vtwoweekago = date("Ymd", time()-84600*14);
				$vfourweekago = date("Ymd", time()-84600*28);
				$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
				$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
				$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
				$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
				$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
				$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
				$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

							$Contents .= "
												<a href=\"javascript:setSelectDate('$today','$today');\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
												<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday');\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
												<a href=\"javascript:setSelectDate('$voneweekago','$today');\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
												<a href=\"javascript:setSelectDate('$v15ago','$today');\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
												<a href=\"javascript:setSelectDate('$vonemonthago','$today');\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
												<a href=\"javascript:setSelectDate('$v2monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
												<a href=\"javascript:setSelectDate('$v3monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

															</td>
														</tr>
													</table>
												</td>
											</tr>";

							$Contents .= "
											<tr height=30>
												<th class='search_box_title'>추가비용 결제방법 : </th>
												<td class='search_box_item'>";
									$Contents .= "
												<input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_BOX_ENCLOSE."' value='".ORDER_METHOD_BOX_ENCLOSE."' ".CompareReturnValue(ORDER_METHOD_BOX_ENCLOSE,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_BOX_ENCLOSE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BOX_ENCLOSE)."'>동봉</label>&nbsp;
												<!--input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>&nbsp;>
												<input type='checkbox' name='payment_method[]' id='payment_method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$payment_method,' checked')." ><label for='payment_method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label-->";
								$Contents .= "
								</td>
												<th class='search_box_title' >결제형태 : </th>
												<td class='search_box_item'>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')." ><label for='payment_agent_type_W' class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')." ><label for='payment_agent_type_M' class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_O' value='O' ".CompareReturnValue("O",$payment_agent_type,' checked')." ><label for='payment_agent_type_O' class='helpcloud' help_width='90' help_height='15' help_html='오프라인주문'><img src='../images/".$admininfo[language]."/s_payment_agent_type_o.gif' align='absmiddle'></label>
												</td>
											</tr>";


						$Contents .= "
											<tr height=30>";
											if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
											$Contents .= "
												<th class='search_box_title'>업체명 : </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												<th class='search_box_title'>담당MD : </th>
												<td class='search_box_item'>".MDSelect($md_code)."</td>";
											}

											$Contents .= "
											</tr>
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


<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
 <tr height=30>";

	if($admininfo[admin_level] == 9){
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
	}

	$sql = "SELECT od.oid
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_detail_deliveryinfo odd
		$where
		GROUP BY od.oid ";
	$db1->query($sql);
	$total = $db1->total;


	$sql = "SELECT o.oid,o.date,odd.date as apply_date, o.bname,o.rname,od.status,odd.payment_method,DATE_FORMAT(odd.payment_date,'%Y-%m-%d') as payment_date ,odd.payment_yn,o.uid as user_id,o.mem_group,od.admin_message,odd.add_delivery_price,odd.odd_ix
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_detail_deliveryinfo odd
		$where
		ORDER BY o.date DESC LIMIT $start, $max";
	$db1->query($sql);

 $Contents .= "<td colspan=3 align=left><b class=blk>전체 주문수(상품수) : ".$total." 건</b></td>
			<td colspan=9 align=right >
			</td>
		</tr>
  </table>";

	$Contents .= "
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='10%' align='center'  class='m_td' nowrap><b>요청일</b></td>
			<td width='15%' align='center' class='m_td'><b>주문일자/주문번호</b></td>
			<td width='15%' align='center'  class='m_td' nowrap><b>주문자명/수취인</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>처리상태</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>결제방법</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>결제상태</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>추가비용</b></td>
			<td width='10%' align='center' class='e_td' nowrap><b>관리</b></td>
		</tr>";

	if($db1->total){
		for ($i = 0; $i < $db1->total; $i++)
		{
			$db1->fetch($i);

			$one_status = getOrderStatus($db1->dt[status],$db1->dt[method])."<input type='hidden' id='od_status_".str_replace("-","",$db1->dt[oid])."' value='".$db1->dt[status]."'>";
			if($db1->dt[payment_yn]=='Y'){
				$payment_yn="입금완료 (".$db1->dt[payment_date].")";
			}elseif($db1->dt[payment_yn]=='N'){
				$payment_yn="입금예정  <img src='../images/".$admininfo["language"]."/btn_modify.gif' align=absmiddle onclick=\"ShowModalWindow('./add_delivery_price.pop.php?odd_ix=".$db1->dt[odd_ix]."',700,200,'add_delivery_price_pop');\"  style='cursor:pointer;' >";
			}elseif($db1->dt[payment_yn]=='L'){
				$payment_yn="손실";
			}else{
				$payment_yn="-";
			}

			$Contents .= "<tr height=28 >";
				$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".$db1->dt[apply_date]."</td>";
				$Contents .= "<td class='list_box_td point' style='line-height:140%' align=center>".$db1->dt[date]."<br><a href=\"../order/orders.read.php?oid=".$db1->dt[oid]."&pid=".$db1->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$db1->dt[oid]."</a></td>";
				$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>".($db1->dt[user_id] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$db1->dt[user_id]."',950,500,'member_info')\" >".Black_list_check($db1->dt[user_id],$db1->dt[bname]."(<span class='small'>".$db1->dt[mem_group]."</span>)")."</a>":$db1->dt[bname]."")." / ".$db1->dt[rname]."</span></td>";
				$Contents .= "<td class='list_box_td ' align='center' nowrap>".$one_status."<br><b>".$db1->dt[admin_message]."</b></td>
							<td class='list_box_td' align='center'  nowrap>".getMethodStatus($db1->dt[payment_method])."</td>
							<td class='list_box_td' align='center'  nowrap>".$payment_yn."</td>";
		$Contents .= "<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db1->dt[add_delivery_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							<td class='list_box_td' align='center'>";
					if($admininfo[admin_level] ==  9){
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						  $Contents .= "<a href=\"../order/orders.edit.php?oid=".$db1->dt[oid]."&pid=".$db1->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>";
						}else{
						   $Contents .=  "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>";
						}
					}
		$Contents .= "</td>
							</tr>";
		}

	}else{
	$Contents .= "<tr height=50><td colspan='8' align=center>조회된 결과가 없습니다.</td></tr>
				";
	}
	$Contents .= "
	  </table>";



if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan='12' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
";

$P = new LayOut();

$P->strLeftMenu = buyer_accounts_menu();
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='../order/orders.goods_list.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n";
$P->Navigation = "구매자정산관리 > $title_str ";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>