<?
include_once("../class/layout.class");
include("../inventory/inventory.lib.php");
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

$db = new Database;
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


if($max==""){
	$max = 20; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
//AND od.product_type NOT IN (".implode(',',$sns_product_type).")
if($page_type == "manual_purchase_list"){
	$where = "WHERE od.status <> 'SR'  and od.order_from = 'MA' ";
}else{
	$where = "WHERE od.status <> 'SR'  ";
}

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


if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " ORDER BY order_date DESC ";
}

$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("$title_str", "배송관리 > $title_str ")."</td>
	</tr>";
if($pre_type == ORDER_STATUS_DELIVERY_READY){
$Contents .= "
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<!--table id='tab_01' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='invoice_input_excel.php'\">일괄송장입력</td>
									<th class='box_03'></th>
								</tr>
							</table-->";
if($delivery_status != "WDR"){
							$Contents .= "
							<table id='tab_02'  ".(($list_type == "" || $list_type == "item") ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?list_type=item&delivery_status=$delivery_status'\">품목종합 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>";
}
							$Contents .= "
							<table id='tab_03' ".($list_type == "item_member" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?list_type=item_member&delivery_status=$delivery_status'\">품목/회원별 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' ".($list_type == "order" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?list_type=order&delivery_status=$delivery_status'\">주문별 리스트</td>
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
if(false){
$Contents .= "
		<!--td style='width:25%;' valign=top>
			<table width=100% border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>최근 주문현황</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=150 width='270px' valign=top style='padding-top:5px;'>".PrintOrderSummary3()."</td>
				</tr>
			</table>
		</td-->";
}
$Contents .= "
		<td style='width:75%;' colspan=2 valign=top>
			<form name='search_frm' method='get' action=''>
			<input type=hidden name='list_type' value='".$list_type."'>
			<table width=100%  border=0>
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
												<td class='search_box_item'>
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
												<td class='input_box_title'><b>목록갯수</b></td>
												<td class='input_box_item'>
													<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
														<option value='5' ".CompareReturnValue(5,$max).">5</option>
														<option value='10' ".CompareReturnValue(10,$max).">10</option>
														<option value='20' ".CompareReturnValue(20,$max).">20</option>
														<option value='50' ".CompareReturnValue(50,$max).">50</option>
														<option value='100' ".CompareReturnValue(100,$max).">100</option>
													</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
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
											<tr>
												<th class='search_box_title'>판매처 선택 </th>
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
															$db->query("select * from sellertool_site_info where disp='1' ");
															$sell_order_from=$db->fetchall();
															if(count($sell_order_from) > 0){

																for($i=0;$i<count($sell_order_from);$i++){
																		$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
																}
															}else{
															$Contents .= "
															<TD></TD>
															<TD></TD>
															<TD></TD>";
															}
											
										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>
											<!--tr height=30>
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
														<option value='0' ".CompareReturnValue('0',$product_type,' selected').">국내</option>
														<option value='2' ".CompareReturnValue('2',$product_type,' selected').">선매입</option>
														<option value='1' ".CompareReturnValue('1',$product_type,' selected').">사이트 주문</option>
													</select>
												</td>
											</tr-->
										";
if(!$pre_type){
$Contents .= "
											<tr>
												<th class='search_box_title'>처리상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<TR>
														<TD ><input type='checkbox' name='type[]' id='type_ir' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_ir'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_ic' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_ic'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_oc' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_oc'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_or' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_or'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
													</TR>
													<TR>
														<TD><input type='checkbox' name='type[]' id='type_r1' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_r2' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_ca' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_ca'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_xx' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_xx'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>
													</TR>
													<TR>
														<TD><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_ei' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_ei'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_ed' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_ec' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>

													</TR>
												</TABLE>
												</td>
											</tr>";
}else if($pre_type == "EA"){
$Contents .= "
											<tr>
												<th class='search_box_title'>처리상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>

													<TR height=30>
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
}else if($pre_type == "RA"){
$Contents .= "
											<tr>
												<th class='search_box_title'>처리상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>
													<col width='20%'>

													<TR height=30>
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
														<td></td>

													</TR>
												</TABLE>
												</td>
											</tr>";
}
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
			$where .= " and o.oid = od.oid and od.pcode = gu.gu_ix and od.stock_use_yn = 'Y' and  od.company_id = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid and od.pcode = gu.gu_ix and od.stock_use_yn = 'Y'  ";
		}

		if($admininfo[mem_type] == "MD"){
			$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
	}

	if($delivery_status){
		$where .= " and od.delivery_status = '".$delivery_status."' ";
	}

	$sql = "SELECT count(distinct od.od_ix) as total
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , inventory_goods_unit gu right join inventory_goods g on g.gid = gu.gid 
					$where "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo nl2br($sql);
	$db1->query($sql);

	$db1->fetch();
	$order_goods_total = $db1->dt[total];

	$sql = "SELECT od.oid
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , inventory_goods_unit gu right join inventory_goods g on g.gid = gu.gid 
					$where GROUP BY od.oid "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo $sql;
	//exit;
	$db1->query($sql);


	$total = $db1->total;

	if($db1->dbms_type == "oracle"){
		$sql = "SELECT distinct o.oid , o.payment_price ,date_
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , inventory_goods_unit gu right join inventory_goods g on g.gid = gu.gid 
					$where
					ORDER BY date_ DESC LIMIT $start, $max";
	}else{
		$sql = "SELECT o.oid, o.payment_price
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, inventory_goods_unit gu right join inventory_goods g on g.gid = gu.gid 
					$where
					GROUP BY od.oid $orderbyString LIMIT $start, $max";
	}

	//echo nl2br($sql);
	$db1->query($sql);
	$_order_info = $db1->fetchall();

//exit;
 $Contents .= "<td colspan=3 align=left><b>전체 주문수(상품수) : ".$order_goods_total." 건</b></td>
				<td colspan=5 align=right>";
if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "<a href='../order/excel_out.php?excel_type=delivery' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}

if($admininfo[admin_level] == 9){
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		$Contents .= " <a href='../order/orders_excel2003.php?excel_type=delivery&delivery_status=$delivery_status&".$type_param."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
}else if($admininfo[admin_level] == 8){
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
		$Contents .= "<a href='../order/orders_excel2003.php?excel_type=delivery&delivery_status=$delivery_status&".$type_param."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
//$Contents .= "<a href='orders.excel.hanjin.php?".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}
$Contents .= "
  </td>
  </tr>
  </table>";

if($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDA"){
	$Contents .= "<form name=listform method=post onsubmit='return CheckStatusUpdate(this)' action='../order/orders.goods_list.act.php' target='act' >
	<input type='hidden' name='act' value='delivery_update'>
	<input type='hidden' name='pre_type' value='$pre_type'>
	<!--input type='hidden' name='list_type' value='order'-->
	<input type='hidden' id='od_ix' />";
}
$Contents .= "
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >";
	if($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDA"){
		$Contents .= "<td class='s_td' width='15px' rowspan=2><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'></td>";
	}
	$Contents .= "
		<!--td width='15%' align='center' class='m_td' rowspan=2><font color='#000000'><b>주문일자</b></font></td-->
		<td width='8%' align='center' class='s_td' style='line-height:140%;' rowspan=2><font color='#000000' ><b>".OrderByLink("주문일자", "o.date", $ordertype)."<br>주문번호</b></font></td>
		<td width='6%' align='center'  class='m_td' rowspan=2 style='line-height:140%;' nowrap><font color='#000000' ><b>".OrderByLink("주문자명", "o.bname", $ordertype)."<br>받는사람명</b></font></td>
		<td width='6%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>판매처</b></font></td>
		<td width='*' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>상품명</b></font></td>
		<td width='6%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>옵션</b></font></td>
		<td width='3%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>수량</b></font></td>
		<td width='6%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>품목명</b></font></td>
		<!--td width='6%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>재고</b></font></td-->
		<td width=30% align='center' class=m_td colspan=5>사업장/보관장소</td>";
if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "
		<td width='7%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>배송방법</b></font></td>
		<td width='6%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>	
		<td width='6%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>출고처리상태</b></font></td>";
	if($delivery_status == "WDR"){
$Contents .= "
		<td width='6%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
	}
}else if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
	$Contents .= "
		<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>택배사</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>송장번호/위치추적</b></font></td>
		<td width='10%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
}else{
	$Contents .= "
		<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>
		<td width='10%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
}
$Contents .= "
	</tr>
	<tr align=center height=30>
		<td class=m_td width='100px'>사업장</td>
		<td class=m_td width='100px'>창고</td>
		<td class=m_td width='120px'>보관장소</td>	
		<td class=m_td width='90px'>재고</td>
		<td class=m_td width='90px'>단위</td>	
	</tr>

  ";



if(count($_order_info)){
	for ($i = 0; $i < count($_order_info); $i++)
	{
		//$db1->fetch($i);

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
		
			if($delivery_status){
				$addWhere .= "and delivery_status = '".$delivery_status."' ";
			}


			if($db1->dbms_type == "oracle"){
				/*
				$sql = "SELECT o.oid,   od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, user_code_ as user_id,od.company_id,
						o.delivery_price as pay_delivery_price,com_name,od.pid,  bname, mem_group,od.admin_message,od.order_from,
						 od.status, od.delivery_status, method, total_price, date_, od.company_name, od.company_id,od.quick, od.invoice_no, gu.gid, gu.unit,   g.gname, 
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid 
						left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
						left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix and ps.section_type = 'D'
						left join  inventory_place_info pi on ps.pi_ix = pi.pi_ix							
						where o.oid = od.oid and od.pcode = gu.gu_ix and od.stock_use_yn = 'Y'  
						and o.oid = '".$_order_info[$i][oid]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere 
						ORDER BY od.company_id DESC, od.status DESC 
						 "; //ORDER BY company_id DESC
					*/
					$sql = "SELECT o.oid,   od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, user_code_ as user_id,od.company_id,
						o.delivery_price as pay_delivery_price,com_name,od.pid,  bname, mem_group,od.admin_message,od.order_from,
						 od.status, od.delivery_status,  total_price, date_, od.company_name, od.company_id,od.quick, od.invoice_no, gu.gid, gu.unit,   g.gname, 
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							group by company_id) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid 
						left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
						left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix and ps.section_type = 'D'
						left join  inventory_place_info pi on ps.pi_ix = pi.pi_ix							
						where o.oid = od.oid and od.pcode = gu.gu_ix and od.stock_use_yn = 'Y'  
						and o.oid = '".$_order_info[$i][oid]."' 
						$addWhere 
						ORDER BY od.company_id DESC, od.status DESC 
						 "; //ORDER BY company_id DESC
						 //and product_type NOT IN (".implode(',',$sns_product_type).")
			}else{
				$sql = "SELECT o.oid,  od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, user_code as user_id,od.company_id,
						o.delivery_price as pay_delivery_price,com_name,od.pid,  bname, mem_group,od.admin_message,od.order_from,
						 od.status, od.delivery_status,  total_price, UNIX_TIMESTAMP(order_date) AS date, od.company_name, od.company_id,od.quick, od.invoice_no,
						gu.gu_ix, gu.gid, gu.unit,  g.gname, pi.place_name, ps.section_name, ips.stock, g.pi_ix, g.ps_ix, 
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							group by company_id  limit 0,1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 0,1) as delivery_pay_type, 
						(select psi_ix from inventory_product_stockinfo ips where ips.gid = gu.gid and ips.unit = gu.unit order by expiry_date asc limit 0,1) as psi_ix,
						(select com_name as company_name from common_company_detail ccd where ccd.company_id = pi.company_id   limit 1) as ips_company_name
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid 
						left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
						left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix and ps.section_type = 'D'
						left join  inventory_place_info pi on ps.pi_ix = pi.pi_ix							
						where o.oid = od.oid and od.pcode = gu.gu_ix and od.stock_use_yn = 'Y'  
						and o.oid = '".$_order_info[$i][oid]."' 
						$addWhere 

						group by od.pcode
						ORDER BY company_id DESC, od.status DESC
						
						
						 "; //ORDER BY company_id DESC //limit 1 and ips.stock > 0 
						 //and od.product_type NOT IN (".implode(',',$sns_product_type).")
			}
		if($_order_info[$i][oid] == "201306131424-8577"){
			//echo nl2br($sql)."<br><br>";
		}
		if($i > 4){
			//echo $i;
		//	exit;
		}else{
			//echo nl2br($sql)."<br><br>";
		}


		/*서브쿼리 삭제 아무것도 없을때 에러남 서브쿼리 부분에서 */
		//,			(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
		//				(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total

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
				$sql = "SELECT o.oid,  od.od_ix,od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, user_code_ as user_id,company_id,
						o.delivery_price as pay_delivery_price,com_name,od.pid,  bname, mem_group,od.admin_message,od.order_from,
						 od.status, od.delivery_status,   total_price, date_ , od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							group by company_id) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$_order_info[$i][oid]."' and od.company_id ='".$admininfo[company_id]."' 
						$addWhere ORDER BY od.company_id DESC, od.status DESC
						 "; //ORDER BY company_id DESC
						 //and product_type NOT IN (".implode(',',$sns_product_type).")
			}else{
				$sql = "SELECT o.oid,   od.od_ix,od.pcode, od.pname,od.mimg, od.option_text, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, user_code as user_id,company_id,
						o.delivery_price as pay_delivery_price,com_name,od.pid,  bname, mem_group,od.admin_message,od.order_from,
						 od.status, od.delivery_status,  total_price, UNIX_TIMESTAMP(order_date) AS date, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$_order_info[$i][oid]."' and od.company_id ='".$admininfo[company_id]."' 
						$addWhere 
						and delivery_status = 'WDR'
						ORDER BY company_id DESC, od.status DESC
						 "; //ORDER BY company_id DESC
						 //and od.product_type NOT IN (".implode(',',$sns_product_type).")
			}
		//,(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total
		}

		$ddb->query($sql);
		$order_detail_infos = $ddb->fetchall();
		//echo $sql;

		$od_count = $ddb->total;

		$status = getOrderStatus($_order_info[$i][status]);


	$bcompany_id = '';
	for($j=0;$j < count($order_detail_infos);$j++){
		
		if($order_detail_infos[$j][delivery_pay_type] == "1"){
			$delivery_pay_type = "선불";
		}elseif($order_detail_infos[$j][delivery_pay_type] == "2"){
			$delivery_pay_type = "착불";
		}else{
			$delivery_pay_type = "무료";
		}

		$one_status = getOrderStatus($order_detail_infos[$j][status],$order_detail_infos[$j][method])."<input type='hidden' id='od_status_".str_replace("-","",$order_detail_infos[$j][oid])."' value='".$order_detail_infos[$j][status]."'>";

		if($order_detail_infos[$j][gift] != ""){
			$od_count_plus = 0;
		}else{
			$od_count_plus = 0;
		}

		//$Contents .= "<tr ".($order_detail_infos[$j][oid] != $b_oid  ? "style='background-color:#efefef'":"")." height=28 >";// kbk
		
		

		$sql = "select g.cid,g.gname, g.gcode, g.admin, (gu.buying_price*ips.stock) as buying_price, gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, 
					pi.place_name, ps.ps_ix, ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid, gu.safestock, gu.sell_ing_cnt, ips.expiry_date ,
					 (select com_name as company_name from common_company_detail ccd where ccd.company_id = pi.company_id limit 1) as ips_company_name
					from inventory_goods g 
					left join inventory_goods_unit gu on g.gid =gu.gid 
					left join inventory_place_info pi on pi.pi_ix = g.pi_ix and pi.pi_ix = '".$order_detail_infos[$j][pi_ix]."'
					left join inventory_place_section ps on pi.pi_ix = ps.pi_ix and  ps.pi_ix = g.pi_ix and ps.section_type = 'D'
					left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit and ips.pi_ix = ps.pi_ix and ips.ps_ix = ps.ps_ix
					where gu.gu_ix = '".$order_detail_infos[$j][gu_ix]."' 
					";
		$ddb->query($sql);
		$ddb->fetch();
		$warehouse_info = $ddb->dt;
		

		$Contents .= "<tr height=28 >";
	
		//$od_ix_str .= "<input type='hidden' name='od_ix[]' id='od_ix' value='".$order_detail_infos[$j][od_ix]."'  >";
		if($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDA" ){
				$Contents .= "<td class='list_box_td' nowrap align='center'>
							  <input type=checkbox name='od_ix[]' id='od_ix' oid='".$order_detail_infos[$j][oid]."' value='".$order_detail_infos[$j][od_ix]."' >
						  </td>";

			}
		if($order_detail_infos[$j][oid] != $b_oid){

			$Contents .= "<td ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='list_box_td' style='line-height:140%' align=center>
						  ".$order_detail_infos[$j][regdate]."<br>
						  <a href=\"../order/orders.read.php?oid=".$order_detail_infos[$j][oid]."&pid=".$order_detail_infos[$j][pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$order_detail_infos[$j][oid]."</a>
						  </td>";
			$Contents .= "<td ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." style='line-height:140%' align=center class='list_box_td'>
						  ".($order_detail_infos[$j][user_code] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$order_detail_infos[$j][user_code]."',950,500,'member_info')\" >".Black_list_check($order_detail_infos[$j][user_code],$order_detail_infos[$j][bname])."</a>":$order_detail_infos[$j][bname])."<span class='small'>(".$order_detail_infos[$j][mem_group].")</span> <br> ".$order_detail_infos[$j][rname]."<br>
						  </td>";
			$Contents .= "<td ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class='list_box_td' align=center>".getOrderFromName($order_detail_infos[$j][order_from])."</td>";
		}
			$Contents .= "<td class='list_box_td' style='padding:5px'  >
							<TABLE>
								<TR>
									<TD>
									<a href='../product/goods_input.php?id=".$order_detail_infos[$j][pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_detail_infos[$j][pid], "m", $order_detail_infos[$j])."'  width=50></a>
									</TD>
									<td width='5'></td>
									<TD style='line-height:140%;text-align:left;'>";
			if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$order_detail_infos[$j][company_id]."&mmode=pop',960,600,'brand')\"><b class=small >".($order_detail_infos[$j][company_name] ? $order_detail_infos[$j][company_name]:"-")."</b></a><br>";
			}
			$Contents .= cut_str($order_detail_infos[$j][pname],30)."<br><b>".$order_detail_infos[$j][option_text]."</b><!--b>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_detail_infos[$j][psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </b-->
									</TD>
								</TR>
							</TABLE>
						</td>
						<td>".$order_detail_infos[$j][option_text]."</td>
						<td class='list_box_td point' align=center >".number_format($order_detail_infos[$j][pcnt])."</td>
						<td class='list_box_td' align=center><a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$order_detail_infos[$j][gid]."',1000,800,'item_info')\">".$order_detail_infos[$j][gname]."</a></td>";
		
		//if($bcompany_id != $order_detail_infos[$j][company_id] || $b_status != $order_detail_infos[$j][status]){gname
			//
			
		//}
			if(!($order_detail_infos[$j][pi_ix] && $order_detail_infos[$j][ps_ix])){
				$Contents .="<td class='list_box_td ' align=center colspan=5  style='line-height:140%;'>품목의 기본창고가 지정되야 합니다.<br><a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$order_detail_infos[$j][gid]."',1000,800,'item_info')\"><b style='color:red;'>품목 기본창고지정</b></a></td>";
			}else{
				$Contents .="<td class='list_box_td ' align=center>".$warehouse_info[ips_company_name]."</td>";
				$Contents .="<td class='list_box_td point' align=center>".$warehouse_info[place_name]."</td>";
				$Contents .="<td class='list_box_td point' align=center>".$warehouse_info[section_name]."</td>";
				$Contents .="<td class='list_box_td' align=center>".number_format($warehouse_info[stock])."</td>";
				$Contents .="<td class='list_box_td point' align=center>".getUnit($warehouse_info[unit], "basic_unit","","text")."</td>";
			}
			
if($order_detail_infos[$j][oid] != $b_oid){			
			$Contents .= "<td style='text-align:center;padding-bottom:5px;' ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"").">
									".getDeliveryMethod($order_detail_infos[$j][delivery_method],"","","text")."<br/>".$delivery_pay_type."
									</td>";
			$Contents .="<td class='list_box_td' align='center' style='line-height:140%;' ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")."  nowrap>".$one_status;
			if($pre_type == ORDER_STATUS_INCOM_COMPLETE){
				$Contents .="<input type=checkbox name='od_ix[]' class='od_ix_".$order_detail_infos[$j][oid]."' value='".$order_detail_infos[$j][od_ix]."' checked> ";
			}
			$Contents .="<br><b>".$order_detail_infos[$j][admin_message]."</b></td>";
			//$Contents .="<td class='list_box_td point' align=center>".$order_detail_infos[$j][delivery_status]."</td>";
			
}

			//if($order_detail_infos[$j][oid] != $b_oid){
				$Contents .= "<td   class='list_box_td' align='center'  nowrap>".getDeliveryStatus($order_detail_infos[$j][delivery_status],"","","text")." ";
				if($delivery_status == "WDR"){
					$Contents .= "<input type=checkbox name='od_ix[]' class='od_ix_".$order_detail_infos[$j][oid]."' value='".$order_detail_infos[$j][od_ix]."' checked>";
				}
				$Contents .= "</td>";
			//}
		

if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
	$Contents .= "
		<td align='center' class='list_box_td' nowrap>".deliveryCompanyList($order_detail_infos[$j][quick],"text")."</td>
		<td align='center' class='list_box_td' nowrap><a href=\"javascript:searchGoodsFlow('".$order_detail_infos[$j][quick]."', '".str_replace("-","",$order_detail_infos[$j][invoice_no])."')\">".$order_detail_infos[$j][invoice_no]."</a></td>";
	
}
if(($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDR") || $pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
		if($bcompany_id != $order_detail_infos[$j][company_id] || $b_status != $order_detail_infos[$j][status]){

			$Contents .= "<td  class='list_box_td point' align='center' style='padding:10px 0px;line-height:130%;'  ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." nowrap> ";
				if($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDR"){
					//if($order_detail_infos[$j][delivery_method] == "TE"){
						$Contents .=  "<form name=listform method=post action='../order/orders.goods_list.act.php' onSubmit='return orderStatusUpdate(this)'  target='act'>
									<input type='hidden' name='oid' value='".$order_detail_infos[$j][oid]."'>
									<input type='hidden' name='act' value='delivery_update'>
									<input type='hidden' name='od_ix_str' value=''>
									<input type='hidden' name='list_type' value='order'>
									<input type='hidden' name='pre_type' value='$pre_type'>
									<table width=100%>								
									<tr id='doortodoor_area_".$order_detail_infos[$j][oid]."'>
										<td>".DeliveryMethod("delivery_method","","onchange=\"/*if(this.value=='TE'){ $('select[name=delivery_company]').eq($('select[name=delivery_method]').index(this)).val(13)}else{ $('select[name=delivery_company]').eq($('select[name=delivery_method]').index(this)).val('')}*/\"","select")."</td>
										<td>".deliveryCompanyList("","SelectbySeller","",$order_detail_infos[$j][company_id])."</td>
									</tr>
									<tr id='doortodoor_area_".$order_detail_infos[$j][oid]."'>
										<td><input type='text' name='deliverycode' class=textbox   size=15 value='".$db2->dt[deliverycode]."' validation=true title='송장번호'></td>
										";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
										$Contents.="
										<td><input type=image src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></td>";
									}else{
										$Contents.="
										<td><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></a></td>";
									}
									$Contents.="
									</tr>
									</table>
								</form>";
					//}
				}else if($pre_type == ORDER_STATUS_DELIVERY_READY){
			
				}else if($pre_type == ORDER_STATUS_DELIVERY_ING){
				    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                        $Contents.="
                        <img src='../images/".$admininfo["language"]."/btn_delivery_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$order_detail_infos[$j][oid]."', '".$order_detail_infos[$j][od_ix]."','".$pre_type."','".ORDER_STATUS_DELIVERY_COMPLETE."')\" style='cursor:hand;'>";
                    }else{
                        $Contents.="
                        <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_delivery_complete.gif' align=absmiddle style='cursor:hand;'></a>";
                    }

				}else if($pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
							if($order_detail_infos[$j][oid] != $b_oid){
								//$Contents .=  "<td  class='list_box_td' align='center' style='padding:10px 0px;' ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count)."'":"")." nowrap>";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                                    $Contents .=  "<a href=\"orders.edit.php?oid=".$order_detail_infos[$j][oid]."&pid=".$order_detail_infos[$j][pid]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle  style='cursor:hand;'></a>";
                                }else{
                                    $Contents .=  "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle  style='cursor:hand;'></a>";
                                }
								//$Contents .= "</td>";
							}

				}else{
						
				}

			$Contents .= "</td>";
			

		}
		
	

}
		$Contents .= "</tr>";

		$b_oid = $order_detail_infos[$j][oid];
		$b_status = $order_detail_infos[$j][status];
		$bcompany_id = $order_detail_infos[$j][company_id];
	}

	}
}else{
$Contents .= "<tr height=50><td colspan=16 align=center>조회된 결과가 없습니다.</td></tr>
			";
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
	</table>";

if($pre_type==ORDER_STATUS_DELIVERY_READY && $delivery_status=='WDA'){

	if($delivery_status=='WDA'){
		$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한품목 전체</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>출고대기 상태변경</label> 
		<input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ><label for='update_kind_level1'>주문출력하기</label>
	</nobr>";

		$help_text = "
		<script type='text/javascript'>
		<!--
			function HelpTextChangeStatus(level,status){
				$('#ht_'+level+'_reason').hide();
				$('.ht_'+level+'_reason_'+status).show();
				if(status=='DD'){
					$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',false);
					$('.ht_'+level+'_reason_'+status).find('.delay').show();
					$('.ht_'+level+'_reason_'+status).find('.common').hide();
					$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',true);
				}else{
					$('.ht_'+level+'_reason_'+status).find('.common').attr('disabled',false);
					$('.ht_'+level+'_reason_'+status).find('.common').show();
					$('.ht_'+level+'_reason_'+status).find('.delay').hide();
					$('.ht_'+level+'_reason_'+status).find('.delay').attr('disabled',true);
				}
				$('#ht_'+level+'_msg').hide();
				$('.ht_'+level+'_msg_'+status).show();
				$('#ht_'+level+'_delivery').hide();
				$('.ht_'+level+'_delivery_'+status).show();
			}

			$(document).ready(function(){";
				//$help_text .= "HelpTextChangeStatus('level0','".ORDER_STATUS_CANCEL_COMPLETE."');";
			$help_text .= "
			});

		//-->
		</script>
		<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>출고처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 출고처리 상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
		<div id='help_text_level0'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level0_status'>
				<td class='input_box_title'> <b>출고처리상태</b></td>
				<td class='input_box_item'> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)."</label> "; //onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')\"
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_WDACC' value='WDACC' ><label for='level0_update_delivery_status_WDACC'  >출고요청 취소</label> ";
			$help_text .= "
				</td>
			</tr>
		</table>
		</div>
		<div id='help_text_level1' style='display:none'>
			<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level1_status'>
				<td class='input_box_title'> <b>주문출력하기</b></td>
				<td class='input_box_item'>
					<input type='radio' name='level1_status' id='level1_update_status_provider_print' value='provider_print' checked><label for='level1_update_status_provider_print'>공급자용</label> <input type='radio' name='level1_status' id='level1_update_status_buyer_print' value='buyer_print' ><label for='level1_update_status_buyer_print'>구매자용</label> <input type='radio' name='level1_status' id='level1_update_status_combo_print' value='combo_print' ><label for='level1_update_status_combo_print'>공급자+구매자용</label> <input type='radio' name='level1_status' id='level1_update_status_noprice_print' value='noprice_print' ><label for='level1_update_status_noprice_print'>가격 노출 X</label>
					<br/>&nbsp;&nbsp; * <span class='small'>인쇄시 머릿글이나 바닦글 또는 여백을 설정하시고 싶으시면 옵션에 인쇄 -> 페이지 설정에서 설정해주시면됩니다. </span>
					<br/>&nbsp;&nbsp; * <span class='small'>구분선이라 라인이 안나올시 배경색 및 이미지 인쇄 체크박스 체크 해주시면 됩니다.</span>
					<!--span class='small'>주문이 인쇄가 안되거나 ActiveX 컨트롤이 설치가 안될때 수동으로 받아서 설치해 주시기 바랍니다. <input type='button' value='다운로드' onclick=\"location.href='./scriptx/ScriptX.msi'\" /> </span-->
				</td>
			</tr>
		</table>
		</div>
		<div id='help_text_level2' style='display:none'></div>";
		 $help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	}

	$Contents .= HelpBox($help_title, $help_text,450);
}

if($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDA"){
	$Contents .= "</form>";
}

$Contents .= "
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

$Script = "<script language='javascript'>
function ChangeWarehouseStatus(oid, od_ix, status){
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'delivery_status_update','oid':oid,'od_ix':od_ix},
		url: './delivery_ready.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
				
				
		},  
		success: function(data){ 
			alert(data);
			document.location.reload();
		} 
	}); 
}



function ChangeWarehouseStatus(oid, od_ix, delivery_status){	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'delivery_status_update_oid','oid':oid,'od_ix':od_ix,'delivery_status':delivery_status},
		url: './delivery_ready.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
				
				
		},  
		success: function(data){ 
			alert(data);
			document.location.reload();
		} 
	}); 
}



function orderStatusUpdate(frm){
	
	var od_ix_str = '';
	$('.od_ix_'+frm.oid.value+':checked').each(function(){
		if(od_ix_str == ''){
			od_ix_str = $(this).val();
		}else{
			od_ix_str += ','+ $(this).val();
		}
		
	});
	frm.od_ix_str.value = od_ix_str;
	if(frm.od_ix_str.value == ''){
		alert(language_data['orders.goods_list.js']['W'][language]);//'배송완료 처리할 상품을 선택해주세요'
		return false;
	}
	
	if(frm.delivery_method.value == ''){
		alert('배송 방법을 선택해주세요.');
		frm.delivery_method.focus();
		return false;
	}

	if(frm.delivery_company.value == ''){
		alert(language_data['orders.goods_list.js']['T'][language]);//배송 업체를 선택해주세요
		frm.delivery_company.focus();
		return false;
	}

	if(frm.deliverycode.value.length < 1){
		alert(language_data['orders.goods_list.js']['U'][language]);//송장번호를 입력해주세요
		frm.deliverycode.focus();
		return false;
	}

	
	
	if(confirm(language_data['orders.goods_list.js']['V'][language])){//'선택된 주문상품을 배송처리 하시겠습니까?'
		return true;
	}else{
		return false;
	}
}

</script>";


if($page_type == "manual_purchase_list"){
	$P = new LayOut();

	$P->strLeftMenu = estimate_menu();
	$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
	$P->addScript = "<script language='javascript' src='../order/orders.goods_list.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
	$P->Navigation = "견적서관리 > 수동수주서 > $title_str";
	$P->title = $title_str;
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();

	$P->strLeftMenu = inventory_menu();
	$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
	$P->addScript = "<script language='javascript' src='orders.goods_list.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
	$P->Navigation = "재고관리 > 출고관리 > $title_str";
	$P->title = $title_str;
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

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