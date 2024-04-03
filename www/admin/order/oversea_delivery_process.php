<?
include_once("../class/layout.class");
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
	$title_str  = "매출진행관리";
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
$where = "WHERE od.status <> 'SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";

if ($oid != "")		$where .= "and od.oid = '$oid' ";
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
//print_r($type);

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

$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("$title_str", "배송관리 > $title_str ")."</td>
	</tr>";
/*
if($pre_type == ORDER_STATUS_DELIVERY_READY){
$Contents .= "
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='invoice_input_excel.php'\">일괄송장입력</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' class='on'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='delivery_ready.php'\">배송준비중 상품목록</td>
									<th class='box_03'></th>
								</tr>
							</table>

						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>";
}
*/
$Contents .= "
	<!--tr>
		<td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> $title_str </b></div>")."</td>
	</tr-->
</table>
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
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=15%>
											<col width=35%>
											<col width=15%>
											<col width=35%>
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
															<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
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
											<tr height=27>
												<th class='search_box_title''>
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
												<th class='search_box_title'>결제방법 : </th>
												<td class='search_box_item'>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."'>".getMethodStatus(ORDER_METHOD_BANK)."</label>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."'>".getMethodStatus(ORDER_METHOD_CARD)."</label>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."'>".getMethodStatus(ORDER_METHOD_VBANK)."</label>

												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."'>".getMethodStatus(ORDER_METHOD_ICHE)."</label>

												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."'>".getMethodStatus(ORDER_METHOD_PHONE)."</label>
												</td>
												<th class='search_box_title'>결제형태 : </th>
												<td class='search_box_item' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')."><label for='payment_agent_type_W'>일반(웹)결제</label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')."><label for='payment_agent_type_M'>모바일결제</label>
												</td>
											</tr>
											<tr height=30>";
											if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
											$Contents .= "
												<th class='search_box_title'>업체명 : </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>";
											}
											$Contents .= "
												<th class='search_box_title' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">상품구분 : </th>
												<td class='search_box_item'>
													<select name='product_type'>
														<option value='' ".CompareReturnValue('',$product_type,' selected').">전체보기</option>
														<option value='2' ".CompareReturnValue('2',$product_type,' selected').">선매입</option>
														<option value='1' ".CompareReturnValue('1',$product_type,' selected').">사이트 주문</option>
													</select>
												</td>
											</tr>
										";

$Contents .= "

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

<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";


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
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where GROUP BY od.oid "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo $sql;
	//exit;
	$db1->query($sql);


	$total = $db1->total;

	if($db1->dbms_type == "oracle"){
		$sql = "SELECT distinct o.oid , o.payment_price ,date_
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date_ DESC LIMIT $start, $max";
	}else{
		$sql = "SELECT o.oid, o.payment_price
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					GROUP BY od.oid ORDER BY date DESC LIMIT $start, $max";
	}

	//echo $sql;
	$db1->query($sql);

//exit;
 $Contents .= "<td colspan=3 align=left><b>전체 주문수 : $total 건</b></td>
				<td colspan=5 align=right>";
if($pre_type == 'AIR_TRANSPORT_EXCEL' || $pre_type == 'OVERSEA_WAREHOUSE_DELIVERY_READY_EXCEL'){//일괄송장입력시!!!
	$Contents .= "<a href='excel_out.php?excel_type=delivery' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}

if($admininfo[admin_level] == 9){
	if($pre_type == 'AIR_TRANSPORT_EXCEL' || $pre_type == 'OVERSEA_WAREHOUSE_DELIVERY_READY_EXCEL'){//일괄송장입력시!!!
		$Contents .= " <a href='orders_excel2003.php?excel_type=delivery&".$type_param."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
}else if($admininfo[admin_level] == 8){
	if($pre_type == 'AIR_TRANSPORT_EXCEL' || $pre_type == 'OVERSEA_WAREHOUSE_DELIVERY_READY_EXCEL'){//일괄송장입력시!!!
		$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
		$Contents .= "<a href='orders_excel2003.php?excel_type=delivery&".$type_param."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
//$Contents .= "<a href='orders.excel.hanjin.php?".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}

$Contents .= "
  </td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td width='14%' align='center' class='s_td'><font color='#000000' ><b>주문일자/주문번호</b></font></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' ><b>주문자명/받는사람명</b></font></td>
		<td width='*' align='center' class='m_td' nowrap><font color='#000000' ><b>제품명</b></font></td>
		<td width='4%' align='center' class='m_td' nowrap><font color='#000000' ><b>수량</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>주문금액</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>배송비</b></font></td>";
if($pre_type == 'AIR_TRANSPORT_EXCEL' || $pre_type == 'OVERSEA_WAREHOUSE_DELIVERY_READY_EXCEL'){//일괄송장입력시!!!
	$Contents .= "
		<td width='7%' align='center' class='e_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>";
}else if($pre_type == ORDER_STATUS_AIR_TRANSPORT_ING){
	$Contents .= "
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>택배사</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>송장번호/위치추적</b></font></td>
		<td width='10%' align='center' class='e_td' nowrap><font color='#000000' ><b>관리</b></font></td>";
}else{
	$Contents .= "
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>
		<td width='10%' align='center' class='e_td' nowrap><font color='#000000' ><b>관리</b></font></td>";
}
$Contents .= "
	</tr>

  ";



if($db1->total){
	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

			if(is_array($type)){

				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			}

			if($ddb->dbms_type == "oracle"){
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid_ as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,
						tid, od.status, method, total_price, receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$db1->dt[oid]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere ORDER BY od.company_id DESC, od.status DESC
						 ";
			}else{
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id  limit 0,1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 0,1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$db1->dt[oid]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere ORDER BY company_id DESC, od.status DESC
						 ";
			}


		}else if($admininfo[admin_level] == 8){
			if(is_array($type)){


				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			}

			if($db->dbms_type == "oracle"){
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid_ as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,
						tid, od.status, method, total_price, date_,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$db1->dt[oid]."' and od.company_id ='".$admininfo[company_id]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere ORDER BY od.company_id DESC, od.status DESC
						 ";
			}else{
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$db1->dt[oid]."' and od.company_id ='".$admininfo[company_id]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere ORDER BY company_id DESC, od.status DESC
						 ";
			}

		}

		$ddb->query($sql);
		//echo $sql;

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
			$use_reserve_price="<span style='font-weight:100;'>적립금 사용: ".$currency_display[$admin_config["currency_unit"]]["front"]."".$ddb->dt[use_reserve_price]." ".$currency_display[$admin_config["currency_unit"]]["back"]."</span>";
		} else {
			$use_reserve_price="";
		}

		$one_status = getOrderStatus($ddb->dt[status],$ddb->dt[method])."<input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."' value='".$ddb->dt[status]."'>";

		if($ddb->dt[gift] != ""){
			$od_count_plus = 0;
		}else{
			$od_count_plus = 0;
		}

		$Contents .= "<tr height=28 >";
		if($ddb->dt[oid] != $b_oid){
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='list_box_td' style='line-height:140%' align=center>
						  ".$ddb->dt[regdate]."<br>
						  <a href=\"orders.read.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[oid]."</a>
						  </td>";
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." style='line-height:140%' align=center class='list_box_td'>
						  ".($ddb->dt[user_id] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$ddb->dt[user_id]."',950,500,'member_info')\" >".Black_list_check($ddb->dt[user_id],$ddb->dt[bname])."</a>":$ddb->dt[bname])."<span class='small'>(".$ddb->dt[mem_group].")</span> <br> ".$ddb->dt[rname]."<br>
						  </td>";
		}
			$Contents .= "<td class='list_box_td' style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD>
									<a href='../product/goods_input.php?id=".$ddb->dt[pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], "m", $ddb->dt)."'  width=50></a>
									</TD>
									<td width='5'></td>
									<TD style='line-height:140%;text-align:left;'>";
			if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand')\"><b class=small >".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b></a><br>";
			}
			$Contents .= cut_str($ddb->dt[pname],30)."<br><b>".$ddb->dt[option_text]."</b><!--b>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </b-->
									</TD>
								</TR>
							</TABLE>
						</td>
						<td class='list_box_td point' align=center>".number_format($ddb->dt[pcnt])."</td>
						<td class='list_box_td' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
		if($bcompany_id != $ddb->dt[company_id] || $b_status != $ddb->dt[status]){
			//
			$Contents .="<td class='list_box_td' align=center ".($bcompany_id != $ddb->dt[company_id] || $b_status != $ddb->dt[status] ? "rowspan='".$ddb->dt[company_total]."'":"").">
						".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[delivery_totalprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."<br>".$delivery_pay_type." </td>";
		}

			$Contents .="<td class='list_box_td point' align='center' nowrap>".$one_status;
			if($pre_type == ORDER_STATUS_AIR_TRANSPORT_READY){
				$Contents .="<input type=checkbox name='od_ix[]' class='od_ix_".$ddb->dt[oid]."' value='".$ddb->dt[od_ix]."' checked> ";
			}
			$Contents .="<br><b>".$ddb->dt[admin_message]."</b></td>";

if($pre_type == ORDER_STATUS_AIR_TRANSPORT_ING){
	$Contents .= "
		<td align='center' class='list_box_td' nowrap>".deliveryCompanyList($ddb->dt[quick],"text")."</td>
		<td align='center' class='list_box_td' nowrap><a href=\"javascript:searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$ddb->dt[invoice_no])."')\">".$ddb->dt[invoice_no]."</a></td>";
}

if($pre_type == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY || $pre_type == ORDER_STATUS_AIR_TRANSPORT_READY || $pre_type == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING || $pre_type == ORDER_STATUS_AIR_TRANSPORT_ING){
		if($bcompany_id != $ddb->dt[company_id] || $b_status != $ddb->dt[status]){
			$Contents .= "<td  class='list_box_td point' align='center' style='padding:10px 0px;'  ".($bcompany_id != $ddb->dt[company_id] ? "rowspan='".$ddb->dt[company_total]."'":"")." nowrap>";
				if($pre_type == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY){

					$Contents .=  "<a href=\"javascript:PoPWindow3('./order_siteinfo.php?mmode=pop&oid=".$ddb->dt[oid]."&od_ix=".$ddb->dt[od_ix]."',500,600,'order_siteinfo')\"'><img src='../images/".$admininfo["language"]."/btn_OWdelivery_ing.gif' align=absmiddle></a><br />";

				}else if($pre_type == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING){
					$Contents .=  "<a href=\"javascript:PoPWindow3('./order_siteinfo.php?mmode=pop&oid=".$ddb->dt[oid]."&od_ix=".$ddb->dt[od_ix]."',500,600,'order_siteinfo')\"'><img src='../images/".$admininfo["language"]."/btn_order_siteinfo.gif' align=absmiddle></a><br />";
					$Contents .=  "<a href=\"javascript:ChangeStatus('status_update', '".$ddb->dt[oid]."', '".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_AIR_TRANSPORT_READY."')\"><img src='../images/".$admininfo["language"]."/btn_air_transport_ready.gif' align=absmiddle></a><br />";
				}else if($pre_type == ORDER_STATUS_AIR_TRANSPORT_READY){
					$Contents .=  "<form name=listform method=post action='orders.goods_list.act.php' onSubmit='return orderStatusUpdate(this)'  target='act'>
									<input type='hidden' name='oid' value='".$ddb->dt[oid]."'>
									<input type='hidden' name='act' value='delivery_update'>
									<input type='hidden' name='od_ix_str' value=''>
									<input type='hidden' name='pre_type' value='$pre_type'>
								<table>
								<tr>
									<td>".deliveryCompanyList("","SelectbySeller","",$ddb->dt[company_id])."</td>
									<td><input type='text' name='deliverycode' class=textbox   size=15 value='".$db2->dt[deliverycode]."' validation=true title='송장번호'></td>
									<td><input type=image src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></td>
								</tr>
								</table>
								</form>";
				}else if($pre_type == ORDER_STATUS_AIR_TRANSPORT_ING){
					$Contents .=  "<a href=\"javascript:ChangeStatus('status_update', '".$ddb->dt[oid]."', '".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_DELIVERY_ING."')\"><img src='../images/".$admininfo["language"]."/btn_delivery_ing.gif' align=absmiddle  style='margin:1px 0px;cursor:pointer;'></a><br />";
				}

			$Contents .= "</td>";
		}
}
		$Contents .= "</tr>";




/*
		if(($od_count-1) == $j && $admininfo[admin_level] == 9){
			$Contents .= "<tr >
							<td class='list_box_td' style='background-color:#efefef;height:30px;font-weight:bold;padding:0 0 0 10px' class=blue colspan=4>
							 <span class='small blue'>".getDeliveryPrice2($ddb->dt[oid])."</span>
							</td>
							<td class='list_box_td'  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=3>
							<span>현금영수증 : ".$receipt_y."</span>
							</td>
							<td class='list_box_td'  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=5 align='right'>
							<b style='color:red;'>결제금액 : ".number_format($db1->dt[payment_price])." 원 ".$use_reserve_price."&nbsp;</b>
							</td>
						</tr>";
		}
*/
		$b_oid = $ddb->dt[oid];
		$b_status = $ddb->dt[status];
		$bcompany_id = $ddb->dt[company_id];
	}

	//$Contents .= "<tr height=3><td colspan=10 bgcolor='#DDDDDD'></td></tr>";
	}
}else{
$Contents .= "<tr height=50><td colspan=".($pre_type == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING || $pre_type == ORDER_STATUS_AIR_TRANSPORT_ING ? '10' : '8' )." align=center>조회된 결과가 없습니다.</td></tr>";
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</table>
	 <table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
  <tr height=40>
    <td colspan='10' align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
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
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents .= HelpBox("빠른송장입력", $help_text);
$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='orders.goods_list.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n";
$P->Navigation = "배송관리 > $title_str";
$P->title = $title_str;
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
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."') then ptprice else '0' end),0) as incom_ready_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_COMPLETE."')  then ptprice else '0' end),0) as incom_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "CA"){
	$sql = "select '구분      ','취소신청 ', '취소신청금액','취소완료건수', '취소완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."'  then 1 else '0' end),0) as cancel_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."') then ptprice else '0' end),0) as cancel_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."'  then 1 else '0' end),0) as cancel_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_COMPLETE."')  then ptprice else '0' end),0) as cancel_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."'  then 1 else '0' end),0) as cancel_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."') then ptprice else '0' end),0) as cancel_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."'  then 1 else '0' end),0) as cancel_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_COMPLETE."')  then ptprice else '0' end),0) as cancel_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "EA"){
	$sql = "select '구분      ','교환신청 ', '교환신청금액','교환완료(회수완료)건수', '교환완료(회수완료)금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."'  then 1 else '0' end),0) as exchange_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice else '0' end),0) as exchange_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_COMPLETE."'  then 1 else '0' end),0) as exchange_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_COMPLETE."')  then ptprice else '0' end),0) as exchange_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."'  then 1 else '0' end),0) as exchange_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice else '0' end),0) as exchange_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_COMPLETE."'  then 1 else '0' end),0) as exchange_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_COMPLETE."')  then ptprice else '0' end),0) as exchange_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "RA"){
	$sql = "select '구분      ','반품신청 ', '반품신청금액','반품완료건수', '반품완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."'  then 1 else '0' end),0) as return_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."') then ptprice else '0' end),0) as return_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_COMPLETE."'  then 1 else '0' end),0) as return_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice else '0' end),0) as return_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."'  then 1 else '0' end),0) as return_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."') then ptprice else '0' end),0) as return_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_COMPLETE."'  then 1 else '0' end),0) as return_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice else '0' end),0) as return_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "FA"){
	$sql = "select '구분      ','환불대기 ', '환불예정금액','환불완료건수', '환불완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."'  then 1 else '0' end),0) as refund_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_APPLY."') then ptprice else '0' end),0) as refund_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."'  then 1 else '0' end),0) as refund_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_COMPLETE."')  then ptprice else '0' end),0) as refund_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."'  then 1 else '0' end),0) as refund_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_APPLY."') then ptprice else '0' end),0) as refund_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."'  then 1 else '0' end),0) as refund_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_COMPLETE."')  then ptprice else '0' end),0) as refund_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
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