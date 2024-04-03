<?
/*include_once("../class/layout.class");


$pre_type = ORDER_STATUS_DELIVERY_READY;

$fix_type = array(ORDER_STATUS_INCOM_COMPLETE,ORDER_STATUS_DELIVERY_READY,ORDER_STATUS_DELIVERY_DELAY);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

$view_type = "sc_order";
$view_type_sub = "due_date";

$parent_title = "주문관리";
$title_str = "배송예정리스트";

include("../order/delivery_process.php");*/

include_once("../class/layout.class");

$pre_type = ORDER_STATUS_INCOM_COMPLETE;

$fix_type = array(ORDER_STATUS_INCOM_COMPLETE);

for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}

//$view_type = "sc_order";
$view_type_sub = "due_date";

$parent_title = "주문관리";
$title_str = "발송예정리스트";
//print_r($admin_config);
if ($startDate == ""){
	//$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$after10day = mktime(0, 0, 0, date("m")  , date("d")+15, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d");
	$eDate = date("Y/m/d", $after10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd", $after10day);
}

$db1 = new Database;
$odb = new Database;
$ddb = new Database;
$od_db = new MySQL;
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
//$where = " AND od.status <> 'SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
if($view_type == "sc_order"){
	$where = " AND od.status <> 'SR' AND od.product_type IN (".implode(',',$sns_product_type).") and od.due_date not in ('0','') ";
}else{
	$where = " AND od.status <> 'SR' and od.due_date not in ('0','') ";
}

if ($oid != "")		$where .= "and od.oid = '$oid' ";
if ($bname != "")	$where .= "and bname = '$bname' ";
if ($rname != "")	$where .= "and rname = '$rname' ";
if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";

if($mode==''){
	$orderdate=1;
}

if($orderdate){
	if ($date_type != ""){
		$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
	}else{
		$where.=" AND od.due_date between $startDate and $endDate ";
	}
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

if($product_type != ""){
	$where .= "and od.product_type = '".$product_type."'";
}

if($admin_div != ""){
	if($admin_div=='c'){
		$where .= "and od.company_id = '".$admininfo[company_id]."'";
	}else{
		$where .= "and od.company_id != '".$admininfo[company_id]."'";
	}
}

if($pre_type != ORDER_STATUS_DELIVERY_READY){
	if(is_array($delivery_status)){
		for($i=0;$i < count($delivery_status);$i++){
			if($delivery_status[$i] != ""){
				if($delivery_status_str == ""){
					$delivery_status_str .= "'".$delivery_status[$i]."'";
				}else{
					$delivery_status_str .= ", '".$delivery_status[$i]."' ";
				}
			}
		}

		if($delivery_status_str != ""){
			$where .= "and od.delivery_status in ($delivery_status_str) ";
		}
	}else{
		if($delivery_status){
			$where .= "and od.delivery_status = '$delivery_status' ";
		}
	}
}else{
	if($_COOKIE[view_wdr_order] == 1){
		$where .= "and (od.delivery_status not in ('WDA','WDC') or od.delivery_status is null)";
	}else{
		$where .= "and (od.delivery_status not in ('WDA','WDR','WDC') or od.delivery_status is null)";
	}
}


$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("$title_str", "배송관리 > $title_str ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<!--tr>
		<td colspan=2>
			".OrderSummary($pre_type, $title_str)."
		</td>
	</tr-->
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
			<form name='search_frm' method='get' action=''>
				<input type='hidden' name='mode' value='search' />
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
											<!--tr height=30>
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
													
													if($view_type == 'offline_order'){
														$Contents .= "
														
															<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>오프라인 영업</label></TD>
														";
													}else{
														$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>
															<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." ><label for='order_from_offline'>오프라인 영업</label></TD>
															<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>";
														$db1->query("select * from sellertool_site_info where disp='1' ");
													$sell_order_from=$db1->fetchall();
													for($i=0;$i<count($sell_order_from);$i++){
															$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
													}
													}

													
										$Contents .= "
														</TR>
													</table>
												</td>
											</tr-->";
/*
if($pre_type == ORDER_STATUS_DELIVERY_READY||$pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY||$pre_type ==ORDER_STATUS_INCOM_COMPLETE){
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
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_DELAY,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label></TD>
														<TD ></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
}*/
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
												<select name='date_type'>
												<option value='od.due_date'>발송예정일</option>
												<option value='".($db1->dbms_type == "oracle" ? 'o.date_' : 'o.date' )."' ".CompareReturnValue(($db1->dbms_type == "oracle" ? 'o.date_' : 'o.date' ),$date_type,' selected').">주문일자</option>
												<option value='od.ic_date' ".CompareReturnValue('od.ic_date',$date_type,' selected').">입금일자</option>
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
									if($admininfo[admin_level] == 9){
							$Contents .= "
											<tr height=30>
												<th class='search_box_title'>상품관리구분 : </th>
												<td class='search_box_item' colspan='3'>
													<input type='radio' name='admin_div' id='admin_div_a' value='' ".CompareReturnValue('',$admin_div,' checked')."><label for='admin_div_a'>전체 상품</label>
													<input type='radio' name='admin_div' id='admin_div_c' value='c' ".CompareReturnValue('c',$admin_div,' checked')."><label for='admin_div_c'>본사 상품</label>
													<input type='radio' name='admin_div' id='admin_div_s' value='s' ".CompareReturnValue('s',$admin_div,' checked')."><label for='admin_div_s'>셀러 상품</label>
												</td>
											</tr>";
									}
									$Contents .= "
											<tr height=30>";
											if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
											$Contents .= "
												<th class='search_box_title'>업체명 : </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												<th class='search_box_title'>담당MD : </th>
												<td class='search_box_item'>".MDSelect($md_code)."</td>
												";
											}
											$Contents .= "
												<!--th class='search_box_title' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">상품구분 : </th>
												<td class='search_box_item'>
													<select name='product_type'>
														<option value='' ".CompareReturnValue('',$product_type,' selected').">전체보기</option>
														<option value='0' ".CompareReturnValue('0',$product_type,' selected').">국내</option>
														<option value='2' ".CompareReturnValue('2',$product_type,' selected').">선매입</option>
														<option value='1' ".CompareReturnValue('1',$product_type,' selected').">사이트 주문</option>
													</select>
												</td-->
											</tr>
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

<form name=listform method=post action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='pre_type' value='$pre_type'>
<input type='hidden' id='oid' value=''>
<input type='hidden' id='od_ix' value=''>
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

	if($view_type == 'offline_order'){		//영업관리 용도 2013-07-05 이학봉
		$where .= " and od.order_from in ('offline') ";
	}

	/*$sql = "SELECT count(distinct od.od_ix) as total
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo nl2br($sql);
	$db1->query($sql);

	$db1->fetch();
	$order_goods_total = $db1->dt[total];

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

	//echo nl2br($sql);
	$db1->query($sql);*/

//exit;

$sql="SELECT od.od_ix FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
	where o.oid = od.oid $where
	$addWhere ORDER BY company_id DESC, od.status DESC
	 ";
$db1->query($sql);
$total=$db1->total;

 $Contents .= "<td colspan=3 align=left>
					<b>전체 주문수(상품수) : ".$total." 건</b>
					<!--input type='checkbox' name='view_wdr_order' id='view_wdr_order' onclick=\"ToggleOrder('WDR')\" ".($_COOKIE[view_wdr_order] == 1 ? "checked":"")." >
					<label for='view_wdr_order'> 출고대기포함</label-->
					</td>
				<td colspan=5 align=right>";
if($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY || $pre_type == ORDER_STATUS_INCOM_COMPLETE){
	//$Contents .=DeliveryMethod("","","onchange=\"$('[name^=delivery_method]').val(this.value);if(this.value=='TE'){ $('[name^=quick]').val(13);}else{ $('[name^=quick]').val('');}\"","select")." ".deliveryCompanyList2("","onchange=\"$('[name^=quick]').val(this.value);\"",$ddb->dt[company_id])." 일괄 변경";
}
if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "<a href='/admin/order/excel_out.php?excel_type=delivery&pre_type=$pre_type' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}

if($admininfo[admin_level] == 9){
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		$Contents .= " <a href='/admin/order/orders_excel2003.php?excel_type=delivery&pre_type=$pre_type&".$type_param."&".$delivery_type_param."&".$QUERY_STRING."&view_type=sc_order&view_type_sub=due_date'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
}else if($admininfo[admin_level] == 8){
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
		$Contents .= "<a href='/admin/order/orders_excel2003.php?excel_type=delivery&pre_type=$pre_type&".$type_param."&".$delivery_type_param."&".$QUERY_STRING."view_type=sc_order&view_type_sub=due_date'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
//$Contents .= "<a href='orders.excel.hanjin.php?".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}
$Contents .= "
  </td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='30' >";
	$Contents .= "<td class='s_td' width='15px'><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'></td>";
	$Contents .= "
		<td width='7%' align='center'  class='s_td' nowrap><font color='#000000' ><b>판매처</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>발송예정일</b></font></td>
		<td width='12%' align='center' class='m_td'><font color='#000000' ><b>주문일자/주문번호</b></font></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' ><b>주문자명/수취인</b></font></td>
		<td width='*' align='center' class='m_td' nowrap><font color='#000000' ><b>제품명</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>옵션</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>판매가(할인가)</b></font></td>
		<td width='4%' align='center' class='m_td' nowrap><font color='#000000' ><b>수량</b></font></td>
		<!--td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>배송비</b></font></td-->";
if($pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_READY && $pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE){
		$Contents .= "<td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>재고/진행/부족</b></font></td>";
}
if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "
		<!--td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td-->
		<td width='7%' align='center' class='e_td' nowrap><font color='#000000' class=small><b >처리상태<br>출고처리상태</b></font></td>";
}else if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE||$pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE){
	$Contents .= "
		<!--td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td-->
		<td width='7%' align='center' class='e_td' nowrap><font color='#000000' class=small><b >처리상태<br>출고처리상태</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>배송타입/택배사</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>송장번호/위치추적</b></font></td>
		<!--td width='10%' align='center' class='m_td' nowrap><font color='#000000' ><b>관리</b></font></td-->";
}elseif($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY||$pre_type ==ORDER_STATUS_INCOM_COMPLETE){
	/*
	$Contents .= "
		<!--td width='5%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td-->
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' class=small><b >처리상태<br>출고처리상태</b></font></td>
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' ><b>배송타입</b></font></td>
		<td width='5%' align='center' class='e_td' nowrap><font color='#000000' ><b>관리</b></font></td>";
	*/
	$Contents .= "
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>";
}else{
	$Contents .= "
		<!--td width='5%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td-->
		<td width='5%' align='center' class='m_td' nowrap><font color='#000000' class=small><b >처리상태<br>출고처리상태</b></font></td>
		<!--td width='5%' align='center' class='e_td' nowrap><font color='#000000' ><b>관리</b></font></td-->";
}
$Contents .= "
	</tr>

  ";

if($total) {
		

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

			if($pre_type != ORDER_STATUS_DELIVERY_READY){
				if(is_array($delivery_status)){
					if($delivery_status_str != ""){
						$addWhere .= "and od.delivery_status in ($delivery_status_str) ";
					}
				}else{
					if($delivery_status){
						$addWhere .= "and od.delivery_status = '$delivery_status' ";
					}
				}
			}else{
				if($_COOKIE[view_wdr_order] == 1){
					$addWhere .= "and (od.delivery_status not in ('WDA','WDC') or od.delivery_status is null) ";
				}else{
					$addWhere .= "and (od.delivery_status not in ('WDA','WDR','WDC') or od.delivery_status is null) ";
				}
				
			}

			if($db1->dbms_type == "oracle"){
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.pid, od.option_text, od.option1, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid_ as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,od.delivery_method,od.stock_use_yn,
						tid, od.status, method, total_price, date_,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,od.order_from,od.delivery_status,od.set_group,od.sub_pname,od.product_type,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, od.due_date
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid $where
						$addWhere ORDER BY od.due_date ASC, od.od_ix ASC LIMIT $start, $max
						 "; //od.company_id DESC, od.status DESC
			}else{
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.pid, od.option_text, od.option1, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,od.delivery_method,od.stock_use_yn,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,od.order_from,od.delivery_status,od.set_group,od.sub_pname,od.product_type,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 0,1) as delivery_pay_type,
						(select COUNT(od_ix) as set_total from ".TBL_SHOP_ORDER_DETAIL." where oid = od.oid and set_group = od.set_group ) as set_total,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, od.due_date
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid $where
						$addWhere ORDER BY od.due_date ASC, od.od_ix ASC LIMIT $start, $max
						 "; //company_id DESC, od.status DESC
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

			if($pre_type != ORDER_STATUS_DELIVERY_READY){
				if(is_array($delivery_status)){
					if($delivery_status_str != ""){
						$addWhere .= "and od.delivery_status in ($delivery_status_str) ";
					}
				}else{
					if($delivery_status){
						$addWhere .= "and od.delivery_status = '$delivery_status' ";
					}
				}
			}else{
				if($_COOKIE[view_wdr_order] == 1){
					$addWhere .= "and (od.delivery_status not in ('WDA','WDC') or od.delivery_status is null) ";
				}else{
					$addWhere .= "and (od.delivery_status not in ('WDA','WDR','WDC') or od.delivery_status is null) ";
				}
				
				
			}

			if($db->dbms_type == "oracle"){
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.pid, od.option_text, od.option1, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid_ as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,od.delivery_method,od.stock_use_yn,
						tid, od.status, method, total_price, date_ ,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,od.order_from,od.delivery_status,od.set_group,od.sub_pname,od.product_type,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, od.due_date
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid and od.company_id ='".$admininfo[company_id]."' $where
						$addWhere ORDER BY od.due_date ASC, od.od_ix ASC LIMIT $start, $max
						 ";//od.company_id DESC, od.status DESC
			}else{
				$sql = "SELECT o.oid, od.od_ix,od.pname,od.mimg, od.pid, od.option_text, od.option1, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid as user_id,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,od.admin_message,od.delivery_method,od.stock_use_yn,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,od.order_from,od.delivery_status,od.set_group,od.sub_pname,od.product_type,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, od.due_date
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid and od.company_id ='".$admininfo[company_id]."' $where
						$addWhere ORDER BY od.due_date ASC, od.od_ix ASC LIMIT $start, $max
						 ";//od.company_id DESC, od.status DESC
			}
		}

		$ddb->query($sql);


		$od_count = $ddb->total;

		$status = getOrderStatus($db1->dt[status]);

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

		//$set_total=$ddb->dt["set_total"];



		$Contents .= "<tr height=28 >";
		
			$Contents .= "<td  class='list_box_td' nowrap align='center'><input type=checkbox name='od_ix[]' oid='".$ddb->dt[oid]."' set_group='".$ddb->dt[set_group]."' id='od_ix' value='".$ddb->dt[od_ix]."' ></td>";

		//if($ddb->dt[oid] != $b_oid){

			$Contents .= "<td class='list_box_td' align=center rowspan='".$set_total."'>".getOrderFromName($ddb->dt[order_from])."</td>";
			$Contents .= "<td class='list_box_td point' align=center rowspan='".$set_total."'>".substr($ddb->dt[due_date],0,4)."-".substr($ddb->dt[due_date],4,2)."-".substr($ddb->dt[due_date],6,2)."</td>";
			$Contents .= "<td class='list_box_td' style='line-height:140%' align=center rowspan='".$set_total."'>
						  ".$ddb->dt[regdate]."<br>
						  <a href=\"/admin/order/orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small' target='_blank'>".$ddb->dt[oid]."</a>
						  </td>";
			$Contents .= "<td style='line-height:140%' align=center class='list_box_td' rowspan='".$set_total."'>
						  ".($ddb->dt[uid] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$ddb->dt[uid]."',950,500,'member_info')\" >".Black_list_check($ddb->dt[uid],$ddb->dt[bname])."</a>":$ddb->dt[bname])."<span class='small'>(".$ddb->dt[mem_group].")</span> <br> ".$ddb->dt[rname]."<br>
						  </td>";
		//}
			$Contents .= "<td class='list_box_td' style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD align='center'>
									<a href='../sns/goods_input.php?id=".$ddb->dt[pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], "m", $ddb->dt)."'  width=50 style='margin:5px;'></a> <br/>";

											if($ddb->dt[product_type]=='21'||$ddb->dt[product_type]=='31'){
												$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($ddb->dt[product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$ddb->dt[product_type].".gif' align='absmiddle' ></label> ";
											}
											if($ddb->dt[stock_use_yn]=='Y'){
												$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
											}
						$Contents .= "
									</TD>
									<td width='5'></td>
									<TD style='line-height:140%;text-align:left;'>";
			if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b></a><br/>";
			}
						if($ddb->dt[product_type]=='99'){
							$Contents .= "<b class='red' >".cut_str($ddb->dt[pname],30)."</b><br/><strong>".get_product_setname($ddb->dt[pid],$ddb->dt[option1],"<br />")."</strong>".cut_str($ddb->dt[sub_pname],30);
						}else if($ddb->dt[product_type]=='21'||$ddb->dt[product_type]=='31'){
							$Contents .= "<b class='blue' >".$ddb->dt[pname]."</b><br/><strong>".get_product_setname($ddb->dt[pid],$ddb->dt[option1],"<br />")."</strong>".cut_str($ddb->dt[sub_pname],30);
						}else{
							$Contents .= cut_str($ddb->dt[pname],30);
						}
						$Contents .="
									</TD>
								</TR>
							</TABLE>
						</td>";
						$Contents .= "<td class='' align='left'>".strip_tags($ddb->dt[option_text])."</td>
						<td class='list_box_td' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
						<td class='list_box_td point' align=center>".number_format($ddb->dt[pcnt])."</td>";

			/*if($bcompany_id != $ddb->dt[company_id]){
				$sql = "SELECT COUNT(DISTINCT(od.od_ix)) AS com_cnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					where o.oid = od.oid and o.oid = '".$ddb->dt[oid]."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") AND od.company_id='".$ddb->dt[company_id]."' 
					$addWhere $order_view_type_str ";//o.payment_price 추가 kbk 13/05/31

				$od_db->query($sql);//$od_db는 상단에서 선언
				$od_db->fetch();
				$com_cnt=$od_db->dt["com_cnt"];

				$Contents .="<td class='' align=center style='line-height:140%;' ".($bcompany_id != $ddb->dt[company_id] ? "rowspan='".$com_cnt."'":"")."
				>".number_format($ddb->dt[delivery_totalprice])."<br>".$delivery_pay_type." </td>";
			}*/

			if($pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_READY && $pre_type != ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE){
				$Contents .="<td class='list_box_td ' align=center>".number_format($ddb->dt[stock])."/-".number_format($ddb->dt[sell_ing_cnt])."/".($ddb->dt[stock]-$ddb->dt[sell_ing_cnt] < 0 ? "<b class='red'>".number_format($ddb->dt[stock]-$ddb->dt[sell_ing_cnt])."</b>" : "-0")."</td>";
			}
			$Contents .="<td class='list_box_td point' align='center' nowrap>".$one_status;
			$Contents .="<br><b>".$ddb->dt[admin_message]."</b></td>";//</td>
			//$Contents .="<!--td class='list_box_td point' align=center-->".getOrderStatus($ddb->dt[delivery_status])."</td>";

if($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type == ORDER_STATUS_DELIVERY_ING||$pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
	$Contents .= "
		<td align='center' class='list_box_td'> ".DeliveryMethod("",$ddb->dt[delivery_method],"","text")." <br/> ".deliveryCompanyList($ddb->dt[quick],"text")." </td>
		<td align='center' class='list_box_td'><a href=\"javascript:searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$ddb->dt[invoice_no])."')\">".$ddb->dt[invoice_no]."</a></td>";
		/*
		$Contents .= "
		<td align='center' class='list_box_td'>";
		if($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type == ORDER_STATUS_DELIVERY_ING){
			 if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents.="
				<img src='../images/".$admininfo["language"]."/btn_delivery_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."', '".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_DELIVERY_COMPLETE."')\" style='cursor:pointer;'>";
			}else{
				$Contents.="
				<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_delivery_complete.gif' align=absmiddle style='cursor:pointer;'></a>";
			}
		}elseif($pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .=  "<input type='button' value='구매확정' onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."', '".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_BUY_FINALIZED."')\" /> <!--a href=\"orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle  style='cursor:pointer;'></a-->";
			}else{
				$Contents .=  "<input type='button' value='구매확정' onclick=\"".$auth_update_msg."\" /> <!--a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle  style='cursor:pointer;'></a-->";
			}
		}
	$Contents.="</td>";
	*/

}elseif($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){
	/*
	$Contents .= "<td  class='list_box_td ' align='center' style='' nowrap><!--img src='../images/".$admininfo["language"]."/btn_cancel_confirm.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."','".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')\" style='cursor:pointer;'--><input type='button' value='출고대기' align=absmiddle onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."','".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')\" style='cursor:pointer;'></td>";
	*/
}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY){

		//if($bcompany_id != $ddb->dt[company_id] || $b_status != $ddb->dt[status]){
			/*
			$Contents .= "<td  class='list_box_td' align='center'  >
			".DeliveryMethod("delivery_method[".$ddb->dt[od_ix]."]","","onchange=\"SetDeliveryInfoCopy('delivery_method',$(this),'".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."');if(this.value=='TE'){ $('select[name^=quick]').filter('[name*=".$ddb->dt[od_ix]."]').val(13)}else{ $('select[name^=quick]').filter('[name*=".$ddb->dt[od_ix]."]').val('')}\"","select")."<br>
			".deliveryCompanyList2("quick[".$ddb->dt[od_ix]."]"," style='margin-top:5px;' onchange=\"SetDeliveryInfoCopy('quick',$(this),'".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."')\"",$ddb->dt[company_id])."
			</td>";
			$Contents .= "<td  class='list_box_td point' align='center' style='padding:10px 0px;'  nowrap>";
				if($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
					$Contents .=  "
								<table>
								<tr>
									<td></td>
									<td><input type='text' name='deliverycode[".$ddb->dt[od_ix]."]' class=textbox   size=15 value='' validation=true title='송장번호' onkeyup=\"SetDeliveryInfoCopy('deliverycode',$(this),'".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."')\" ></td>
                                    ";

                                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                                    $Contents.="
									<td><img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:pointer;' onclick=\"SelectDeliveryIng('".$ddb->dt[product_type]."','".$ddb->dt[oid]."','".$ddb->dt[set_group]."','".$ddb->dt[od_ix]."');\"></td>";
                                }else{
                                    $Contents.="
									<td><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:pointer;'></a></td>";
                                }
								$Contents.="
                                </tr>
								</table>";
				}
			$Contents .= "</td>";
			*/
		//}
}
		$Contents .= "</tr>";
		$b_oid = $ddb->dt[oid];
		$b_status = $ddb->dt[status];
		$bcompany_id = $ddb->dt[company_id];
	}
}else{

	if($pre_type == ORDER_STATUS_DELIVERY_READY)											$result_colpan="12";
	elseif($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)				$result_colpan="13";
	elseif($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_READY||$pre_type == ORDER_STATUS_INCOM_COMPLETE)				$result_colpan="14";
	elseif($pre_type == ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type ==ORDER_STATUS_DELIVERY_ING||$pre_type ==ORDER_STATUS_DELIVERY_COMPLETE)				$result_colpan="15";
	else			$result_colpan="14";
	$Contents .= "<tr height=50><td colspan='".$result_colpan."' align=center>조회된 결과가 없습니다.</td></tr>";
}

$Contents .= "
	</table>";

$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>처리상태변경</label>
	</nobr>";

$help_text = "
<script type='text/javascript'>
<!--
	function HelpTextChangeStatus(status){
		$('#ht_level0_reason').hide();
		$('.ht_level0_reason_'+status).show();
		$('#ht_level0_msg').hide();
		$('.ht_level0_msg_'+status).show();
	}

	$(document).ready(function(){";
		if($pre_type==ORDER_STATUS_INCOM_READY){
			$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_INCOM_COMPLETE."');";
		}elseif($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
			$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_READY."');";
		}
$help_text .= "
	});

//-->
</script>
<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
<div id='help_text_level0'>
<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
	<col width=170>
	<col width=*>
	<tr id='ht_level0_status'>
		<td class='input_box_title'> <b>결제상태</b></td>
		<td class='input_box_item'>";
		if($pre_type==ORDER_STATUS_INCOM_READY){
			$help_text .= "
			<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_INCOM_COMPLETE."')\" checked><label for='level0_update_status_".ORDER_STATUS_INCOM_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>
			<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label>";
		}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
			$help_text .= "
			<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_READY."')\" checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>
			<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_CANCEL_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>";
		}
$help_text .= "
		</td>
	</tr>";
	if($pre_type==ORDER_STATUS_INCOM_READY){
	$help_text .= "<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' >";
	}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
	$help_text .= "<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_CANCEL_COMPLETE."' >";
	}
	$help_text .= "
		<td class='input_box_title'> <b>거부사유</b></td>
		<td class='input_box_item'> 
			<select name='level0_reason_code' style='font-size:12px;'>";
				$help_text .= "<option value='' >취소사유</option>";
				foreach($order_select_status_div['A']['IC']['CA'] as $key => $val){
					$help_text .= "<option value='".$key."' >".$val[title]."</option>";
				}
				$help_text .= "
			</select>
		</td>
	</tr>";
	if($pre_type==ORDER_STATUS_INCOM_READY){
	$help_text .= "<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' >";
	}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
	$help_text .= "<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_CANCEL_COMPLETE."' >";
	}
	$help_text .= "
		<td class='input_box_title'> <b>기타</b></td>
		<td class='input_box_item'>
			 <input type=text name='level0_msg'  class='textbox' value='' style='width:350px;' >
		</td>
	</tr>
</table>
</div>
<div id='help_text_level1' style='display:none'></div>
<div id='help_text_level2' style='display:none'></div>
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

$Contents .= HelpBox($help_title, $help_text,250);


if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
</form>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
  <tr height=40>
    <td colspan='10' align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$Script = "
<script language='javascript'>
function ToggleOrder(type){
	if(type == 'WDR'){
		if($('#view_wdr_order').attr('checked') == true || $('#view_wdr_order').attr('checked') == 'checked'){		
			
			$.cookie('view_wdr_order', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_wdr_order', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}else if(type == 'project'){
		if($('#view_project_job').attr('checked') == true || $('#view_project_job').attr('checked') == 'checked'){		
			$.cookie('view_project_job', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_project_job', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
	}
	document.location.reload();
}
</script>
";

$P = new LayOut();
if($view_type == "offline_order"){
	$P->strLeftMenu = offline_order_menu();
}elseif($view_type == "sc_order"){
	$P->strLeftMenu = sns_menu();
}else{
	$P->strLeftMenu = order_menu();
}
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='../order/orders.goods_list.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->Navigation = "배송관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();


function OrderSummary($status, $title){
	return false;
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