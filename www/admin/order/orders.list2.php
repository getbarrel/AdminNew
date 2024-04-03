<?
/*
상품명에 cut_str 걸려있는거 다 제거함 kbk 13/08/06
*/
include_once("../class/layout.v4.class");
include_once("../sellertool/sellertool.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");

}

if(empty($type)){
	$type = $fix_type;
}

if($view_type == 'pos_order' || $view_type == 'sc_order'){
	$rows_cnt = '2';			// 포스관리일 경우 교환처리상태가 필요없어서 주석처리 됨을 rowspan 값을 2로 설정해줌 2013-07-07 이학봉
}else{
	$rows_cnt = '3';
}
$db = new Database;
$odb = new Database;
$ddb = new Database;
$od_db = new Database;

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
 if($view_type == 'sc_order'){//통합커머스 용도 2013-08-27 신훈식 -> 위로 올람 20130828 Hong
	if($db->dbms_type == "oracle"){
		$where = "WHERE od.status !='SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
		$ood_where = "WHERE ood.status !='SR' AND ood.product_type IN (".implode(',',$sns_product_type).") ";//총 주문상세건수를 구하기 위해 추가 kbk 13/05/31
	}else{
		$where = "WHERE od.status <> '' and od.status !='SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
		$ood_where = "WHERE ood.status <> '' and ood.status !='SR' AND ood.product_type IN (".implode(',',$sns_product_type).") ";//총 주문상세건수를 구하기 위해 추가 kbk 13/05/31
	}
	$folder_name = "sns";
 }else{
	 /*
	if($db->dbms_type == "oracle"){
		$where = "WHERE od.status !='SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
		$ood_where = "WHERE ood.status !='SR' AND ood.product_type NOT IN (".implode(',',$sns_product_type).") ";//총 주문상세건수를 구하기 위해 추가 kbk 13/05/31
	}else{
		$where = "WHERE od.status <> '' and od.status !='SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
		$ood_where = "WHERE ood.status <> '' and ood.status !='SR' AND ood.product_type NOT IN (".implode(',',$sns_product_type).") ";//총 주문상세건수를 구하기 위해 추가 kbk 13/05/31
	}*/
	if($db->dbms_type == "oracle"){
		$where = "WHERE od.status !='SR' ";
		$ood_where = "WHERE ood.status !='SR' ";//총 주문상세건수를 구하기 위해 추가 kbk 13/05/31
	}else{
		$where = "WHERE od.status <> '' and od.status !='SR' ";
		$ood_where = "WHERE ood.status <> '' and ood.status !='SR' ";//총 주문상세건수를 구하기 위해 추가 kbk 13/05/31
	}
	$folder_name = "product";
 }

if ($oid != "")		$where .= "and od.oid = '$oid' ";
if ($bname != "")	$where .= "and bname = '$bname' ";
if ($rname != "")	$where .= "and rname = '$rname' ";
if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";

if($mode!="search"){
	$orderdate=1;
}

if(!$date_type){
	$date_type="o.date";
}

if($orderdate){
	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
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

if(is_array($refund_type)){
	for($i=0;$i < count($refund_type);$i++){
		if($refund_type[$i]){
			if($refund_type_str == ""){
				$refund_type_str .= "'".$refund_type[$i]."'";
			}else{
				$refund_type_str .= ", '".$refund_type[$i]."' ";
			}
		}
	}

	if($refund_type_str != ""){
		$where .= "and od.refund_status in ($refund_type_str) ";
	}
}else{
	if($refund_type){
		$where .= "and od.refund_status = '$refund_type' ";
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

if($md_code != ""){
	$where .= "and od.md_code = '".$md_code."'";
}

$Contents = "
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0><form name='search_frm' method='get' action=''><input type='hidden' name='mode' value='search' />
				<tr height=25>
					<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=160 width='100%' valign=top style='padding-top:5px;'>
					
									<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 3px 0;height:120px;'>
										<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
										<col width=5%>
										<col width=10%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
											<tr height=30>
												<th class='search_box_title' colspan='2'>판매처 선택 </th>
												<td class='search_box_item' nowrap colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>";

										if($view_type == 'offline_order'){		//영업관리용도
												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>오프라인 영업</label></TD>
														";
										}else if($view_type == 'pos_order'){		//포스관리용도
												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." checked><label for='order_from_pos'>POS</label></TD>
														";
										}else{
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
															<TD></TD>

															";
															}
										}

										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>
											<tr>
												<th class='search_box_title' rowspan='2' >결제 </th>
												<th class='search_box_title' >결제상태 </th>
												<td class='search_box_item' colspan='3'>
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
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>";
									if($view_type == 'pos_order'){
											$Contents .= "
														<TD></TD>
														<TD></TD>";
									}else{
											$Contents .= "
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_DEFERRED_PAYMENT."' value='".ORDER_STATUS_DEFERRED_PAYMENT."' ".CompareReturnValue(ORDER_STATUS_DEFERRED_PAYMENT,$type,' checked')." ><label for='type_".ORDER_STATUS_DEFERRED_PAYMENT."'>".getOrderStatus(ORDER_STATUS_DEFERRED_PAYMENT)."</label></TD>
														<TD></TD>";
									}
											$Contents .= "
													</TR>
												</TABLE>
												</td>
											</tr>
											<tr>
												<th class='search_box_title' >환불상태 </th>
												<td class='search_box_item' colspan='3'>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=25>
														<TD><input type='checkbox' name='refund_type[]' id='refund_type_".ORDER_STATUS_REFUND_APPLY."' value='".ORDER_STATUS_REFUND_APPLY."' ".CompareReturnValue(ORDER_STATUS_REFUND_APPLY,$refund_type,' checked')." ><label for='refund_type_".ORDER_STATUS_REFUND_APPLY."'>".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</label></TD>
														<TD><input type='checkbox' name='refund_type[]'  id='refund_type_".ORDER_STATUS_REFUND_COMPLETE."' value='".ORDER_STATUS_REFUND_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_REFUND_COMPLETE,$refund_type,' checked')." ><label for='refund_type_".ORDER_STATUS_REFUND_COMPLETE."'>".getOrderStatus(ORDER_STATUS_REFUND_COMPLETE)."</label></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
								if($view_type != 'pos_order'){
									$Contents .= "
											<tr>
												<th class='search_box_title' colspan='2'>출고처리상태 </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=25>
														<TD ><input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY,$delivery_status,' checked')." ><label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)."</label></TD>
														<TD ><input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_READY,$delivery_status,' checked')." ><label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)."</label></TD>
														<TD ><input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE,$delivery_status,' checked')." ><label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE)."</label></TD>
														<TD ></TD>
														<TD ></TD>
														<TD ></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
								}

									$Contents .= "
											<tr>
												<th class='search_box_title' rowspan='".$rows_cnt."'>처리상태</th>
												<th class='search_box_title' >배송처리상태 </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=25>";
									if($view_type == 'pos_order'){
									$Contents .= "
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$type,' checked')." ><label for='type_".ORDER_STATUS_BUY_FINALIZED."'>".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label></TD>
														<TD ></TD>
														<TD ></TD>
														<TD ></TD>
														<TD ></TD>";
									}else{

									$Contents .= "
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_DELAY,$type,' checked')."><label for='type_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_ING."'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$type,' checked')." ><label for='type_".ORDER_STATUS_BUY_FINALIZED."'>".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label></TD>";
									}
									$Contents .= "
													</TR>
												</TABLE>
												</td>
											</tr>";
									if($view_type != 'pos_order' && $view_type != 'sc_order'){
									$Contents .= "
											<tr>
												<th class='search_box_title' >교환처리상태 </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=25>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_DENY."' value='".ORDER_STATUS_EXCHANGE_DENY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DENY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DENY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DENY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_DELIVERY."' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DELIVERY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_ACCEPT."' value='".ORDER_STATUS_EXCHANGE_ACCEPT."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ACCEPT,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_ACCEPT."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ACCEPT)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_DEFER."' value='".ORDER_STATUS_EXCHANGE_DEFER."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DEFER,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DEFER."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DEFER)."</label></TD>
													</TR>
													<TR>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' value='".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_IMPOSSIBLE,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_IMPOSSIBLE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
									}
									$Contents .= "
											<tr>
												<th class='search_box_title' >반품처리상태 </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													";
								if($view_type == 'pos_order'){
									$Contents .= "
													<TR height=25>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
													</TR>";
								}else if($view_type == 'sc_order'){
									$Contents .= "
													<TR height=25>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
													</TR>";
								}else{
									$Contents .= "
													<TR height=25>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_APPLY."' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_APPLY."'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label>
														</TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_DENY."' value='".ORDER_STATUS_RETURN_DENY."' ".CompareReturnValue(ORDER_STATUS_RETURN_DENY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_DENY."'>".getOrderStatus(ORDER_STATUS_RETURN_DENY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' ".CompareReturnValue(ORDER_STATUS_RETURN_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_ING."'>".getOrderStatus(ORDER_STATUS_RETURN_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_DELIVERY."' value='".ORDER_STATUS_RETURN_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_RETURN_DELIVERY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_DELIVERY."'>".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_ACCEPT."' value='".ORDER_STATUS_RETURN_ACCEPT."' ".CompareReturnValue(ORDER_STATUS_RETURN_ACCEPT,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_ACCEPT."'>".getOrderStatus(ORDER_STATUS_RETURN_ACCEPT)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_DEFER."' value='".ORDER_STATUS_RETURN_DEFER."' ".CompareReturnValue(ORDER_STATUS_RETURN_DEFER,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_DEFER."'>".getOrderStatus(ORDER_STATUS_RETURN_DEFER)."</label></TD>
													</TR>
													<TR height=25>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_IMPOSSIBLE."' value='".ORDER_STATUS_RETURN_IMPOSSIBLE."' ".CompareReturnValue(ORDER_STATUS_RETURN_IMPOSSIBLE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_RETURN_IMPOSSIBLE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
													</TR>";
								}
									$Contents .= "
												</TABLE>
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>검색항목 </th>
												<td class='search_box_item' colspan=3>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='200px'>
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
														<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
											</tr>
											<tr height=33>
												<th class='search_box_title' colspan='2'>
												<select name='date_type'>
												<option value='".($db->dbms_type == "oracle" ? 'o.date_' : 'o.date' )."' ".CompareReturnValue(($db->dbms_type == "oracle" ? 'o.date_' : 'o.date' ),$date_type,' selected').">주문일자</option>
												<option value='o.bank_input_date' ".CompareReturnValue('o.bank_input_date',$date_type,' selected').">입금일자</option>
												<!--option value='date'>취소일자</option-->
												</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item'  colspan=3>
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
																<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
																<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
																<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
																<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
																<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
																<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
																<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>결제방법 : </th>
												<td class='search_box_item' nowrap>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>&nbsp;-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>
												</td>
												<th class='search_box_title' >결제형태 : </th>
												<td class='search_box_item'>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')." ><label for='payment_agent_type_W' class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')." ><label for='payment_agent_type_M' class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_O' value='O' ".CompareReturnValue("O",$payment_agent_type,' checked')." ><label for='payment_agent_type_O' class='helpcloud' help_width='90' help_height='15' help_html='오프라인주문'><img src='../images/".$admininfo[language]."/s_payment_agent_type_o.gif' align='absmiddle'></label>
												</td>
											</tr>";

										if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
											$Contents .= "
											<tr height=30>
												<th class='search_box_title' colspan='2'>셀러명 : </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												<th class='search_box_title'>담당MD : </th>
												<td class='search_box_item'>".MDSelect($md_code)."</td>
											</tr>";
										}

								$Contents .= "
										</table>
										</TD>
									</TR>
									</TABLE>
								
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
<form name=listform method=post action='../order/orders.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='page' value='$page'>
<input type=hidden id='oid' value='' >
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";

	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
			$ood_where.=" and o.oid = ood.oid and  ood.company_id = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid ";
			$ood_where.=" and o.oid = ood.oid ";
		}

		if($admininfo[mem_type] == "MD"){
			$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			$ood_where.=" and ood.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
		$ood_where.=" and o.oid = ood.oid and ood.company_id = '".$admininfo[company_id]."'";
	}

	if($view_type == 'offline_order'){		//영업관리 용도 2013-07-05 이학봉
		$where .= " and od.order_from in ('offline') ";
		$ood_where .= " and od.order_from in ('offline') ";
	}else if($view_type == 'pos_order'){		//포스관리 용도 2013-07-05 이학봉
		$where .= " and od.order_from in ('pos') ";
		$ood_where .= " and od.order_from in ('pos') ";
	}

	$sql = "SELECT count(distinct od.od_ix) as total
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od

	$sql = "SELECT count(distinct o.oid) as total
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where ";//od.od_ix로 되어 있어서 페이징에 맞지 않으므로 o.oid로 변경 kbk 13/05/31
	//echo nl2br($sql);
	$db->query($sql);

	$db->fetch();
	$total = $db->dt[total];

	if($db->dbms_type == "oracle"){
		$sql = "SELECT distinct o.oid , o.payment_price ,date_ , payment_agent_type,o.status
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date_ DESC LIMIT $start, $max";
	}else{

		$sql = "SELECT distinct o.oid , o.payment_price ,date , payment_agent_type,o.status
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
					$where
					ORDER BY date DESC LIMIT $start, $max";//쿼리 과부하로 인해 o.payment_price 뺌 -> 대표님 작업 kbk 13/05/31
	}
	//echo nl2br($sql);
	//echo $sql;
	$db->query($sql);


$Contents .= "<td colspan=3 align=left><b class=blk>전체 주문수 : $total 건</b></td><td colspan=10 align=right>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
	<a href='../order/excel_out.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
}else{
    $Contents .= "
	<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}
if($admininfo[admin_level] == 9){
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
        $Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다..'>
        <a href='../order/orders_excel2003.php?view_type=$view_type&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></span>";
    }else{
        $Contents .= "
        <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
    }
}else if($admininfo[admin_level] == 8){
    $Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
        $Contents .= "
        <a href='../order/orders_excel2003.php?view_type=$view_type&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
    }else{
        $Contents .= "
        <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
    }
}

$Contents .= "
	</td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='border-bottem:0px;'>
	<col width='25px'/>
	<col width='8%'/>
	<col width='15%'/>
	<col width='*'/>
	<col width='11%'/>
	<col width='11%'/>
	<col width='11%'/>
	<col width='11%'/>
	<col width='11%'/>
	<col width='6%'/>
		<tr height='25' >
		<td align='center' class='s_td' style='background-color:#fff7da;' ><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' ><font color='#000000' class=small><b>판매처</b></font></td>
		<td align='center'  class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>주문일</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>주문번호</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap><font color='#000000' class=small><b>주문자/수취인</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>결제방법/구분</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>입금상태(입금일)</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>영수증</b></font></td>
		".($admininfo[admin_level]==9 ? "<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>총 결제금액</b></font></td>" : "" )."
		<td align='center' class='e_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>관리</b></font></td>
	</tr>
	</table>
	 <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td width='*' align='center' class='m_td' ><font color='#000000' class=small ><b>상품명</b></font></td>
		<td width='12%' align='center'  class='m_td' nowrap ><font color='#000000' class=small ><b>옵션</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>매입가</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>정가</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>판매가(할인가)</b></font></td>
		<td width='3%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>수량</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>상품가격</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>배송방법/배송비</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>적립금</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>수수료</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>재고/진행/부족</b></font></td>
		<td width='6%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>처리상태</b></font></td>
		".($admininfo[admin_level]==9 ? "<td width='6%' align='center' class='m_td' nowrap ><font color='#000000' class=small ><b>출고처리상태</b></font></td>" : "" )."
	</tr>";


if($db->total){
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

			if($search_type && $search_text){
				if($search_type == "combi_name"){
					$addWhere .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
				}elseif($search_type =="pname"){
					$addWhere .= "and od.pname LIKE '%".trim($search_text)."%' ";
				}else{
					$addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
				}
			}

			if($ddb->dbms_type == "oracle"){
				$sql = "SELECT   o.oid, o.delivery_box_no, o.payment_price, od.od_ix, od.product_type, od.pname, od.mimg, od.option_text, od.option1, od.regdate,od.coprice,od.listprice, od.psprice, od.pcnt, od.ptprice,od.commission, uid_ as user_id,company_id,od.delivery_price,od.delivery_status,o.bank_input_date,od.stock_use_yn,o.delivery_method,o.payment_agent_type,od.sub_pname,
						o.delivery_price as pay_delivery_price,com_name,od.pid, bname,rname, mem_group,o.use_reserve_price,od.order_from,od.pcode,od.admin_message,
						tid, o.status as ostatus,od.status, method, total_price, receipt_y, od.company_name, od.company_id, od.reserve, od.co_pid,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, o.taxsheet_yn, o.tax_affairs_yn
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (pod.id=od.option1)  left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid and o.oid = '".$db->dt[oid]."' 
						$addWhere
						$order_view_type_str
						ORDER BY od.company_id DESC ";
						//o.payment_price 추가 kbk 13/05/31
						//AND od.product_type NOT IN (".implode(',',$sns_product_type).") 20130828 Hong

			}else{
				$sql = "SELECT o.oid, o.delivery_box_no, o.payment_price, od.od_ix, od.product_type, od.pid, od.pname, od.mimg, od.option_text, od.option1, od.regdate, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice,od.commission, uid as user_id,company_id,od.delivery_price,od.delivery_status,o.bank_input_date,od.stock_use_yn,o.delivery_method,o.payment_agent_type,od.sub_pname,
						o.delivery_price as pay_delivery_price,com_name,od.pid, bname,rname, mem_group,o.use_reserve_price,od.order_from,od.pcode,od.admin_message,
						tid, o.status as ostatus,od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id, od.reserve, od.co_pid,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, o.taxsheet_yn, o.tax_affairs_yn
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid and o.oid = '".$db->dt[oid]."' 
						$addWhere
						$order_view_type_str
						ORDER BY company_id DESC ";
						//o.payment_price 추가 kbk 13/05/31
						//AND od.product_type NOT IN (".implode(',',$sns_product_type).") 20130828 Hong
			}

		}else if($admininfo[admin_level] == 8){

			if($search_type && $search_text){
				if($search_type == "combi_name"){
					$addWhere .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
				}elseif($search_type =="pname"){
					$addWhere .= "and od.pname LIKE '%".trim($search_text)."%' ";
				}else{
					$addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
				}
			}

			if($ddb->dbms_type == "oracle"){
				$sql = "SELECT o.oid, o.delivery_box_no, o.payment_price, od.od_ix, od.product_type, od.pname, od.mimg, od.option_text, od.regdate, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice,od.commission, uid_ as user_id,company_id,od.delivery_price,od.delivery_status,o.bank_input_date,od.stock_use_yn,o.delivery_method,o.payment_agent_type,od.sub_pname,
						o.delivery_price as pay_delivery_price,com_name,od.pid, bname,rname, mem_group,o.use_reserve_price,od.order_from,od.pcode,od.admin_message,
						tid, o.status as ostatus,od.status, method, total_price, receipt_y, od.company_name, od.company_id, od.reserve, od.co_pid,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as delivery_totalprice,
						(select IFNULL(company_total,'') as company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id ) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, o.taxsheet_yn, o.tax_affairs_yn
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid and o.oid = '".$db->dt[oid]."' and od.company_id ='".$admininfo[company_id]."' 
						$addWhere
						$order_view_type_str
						ORDER BY od.company_id DESC ";
						//o.payment_price 추가 kbk 13/05/31
						//AND od.product_type NOT IN (".implode(',',$sns_product_type).") 20130828 Hong
			}else{
				$sql = "SELECT o.oid, o.delivery_box_no, o.payment_price, od.od_ix, od.product_type, od.pname, od.mimg, od.option_text, od.regdate, od.coprice,od.listprice,od.psprice, od.pcnt, od.ptprice,od.commission, uid as user_id,company_id,od.delivery_price,od.delivery_status,o.bank_input_date,od.stock_use_yn,o.delivery_method,o.payment_agent_type,od.sub_pname,
						o.delivery_price as pay_delivery_price,com_name,od.pid, bname,rname, mem_group,o.use_reserve_price,od.order_from,od.pcode,od.admin_message,
						tid, o.status as ostatus,od.status, method, total_price, receipt_y, od.company_name, od.company_id,  od.reserve,od.co_pid,
						(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_totalprice,
						(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as company_total,
						(select delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type,
						(case when od.option1 != 0 then pod.option_stock else p.stock end) as stock,
						(case when od.option1 != 0 then pod.option_sell_ing_cnt else p.sell_ing_cnt end) as sell_ing_cnt, o.taxsheet_yn, o.tax_affairs_yn
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option1=pod.id) left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid) 
						where o.oid = od.oid and o.oid = '".$db->dt[oid]."' and od.company_id ='".$admininfo[company_id]."'
						$addWhere
						$order_view_type_str
						ORDER BY od.company_id DESC ";
						//o.payment_price 추가 kbk 13/05/31
						//AND od.product_type NOT IN (".implode(',',$sns_product_type).") 20130828 Hong
			}
		}

		/*
		쿼리에 , o.taxsheet_yn, o.tax_affairs_yn 추가 kbk 13/08/06
		*/

		$ddb->query($sql);
		$od_count = $ddb->total;
		/*
		if ($ddb->dt[status] == ORDER_STATUS_DELIVERY_COMPLETE)		{
			$delete = "<a href=\"javascript:alert(language_data['orders.list.php']['A'][language]);\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";//[처리완료] 기록은 삭제할 수 없습니다.
		}elseif ($ddb->dt[status] != ORDER_STATUS_CANCEL_COMPLETE && $db->dt[method] == "1"){
			$delete = "<a href=\"javascript:order_delete('delete','".$db->dt[oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";
		}else{
			$delete = "<a href=\"javascript:act('delete','".$db->dt[oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:2px;'></a>";
		}*/
	$bcompany_id = '';
	for($j=0;$j < $ddb->total;$j++){
		$ddb->fetch($j);


		if ($ddb->dt[method] == ORDER_METHOD_CARD)
		{
			if($ddb->dt[bank] == ""){
				$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>";
			}else{
				$method = $db->dt[bank];
			}
			$receipt_y = "카드결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_BANK){
			$method = "<label class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_PHONE){
			$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>";
		}elseif($ddb->dt[method] == ORDER_METHOD_AFTER){
			$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_AFTER)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_AFTER.".gif' align='absmiddle'></label>";
		}elseif($ddb->dt[method] == ORDER_METHOD_VBANK){
			$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_ICHE){
			$method = "<label class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_MOBILE){
			$method = "<label class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_MOBILE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_MOBILE.".gif' align='absmiddle'></label>";
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
		}elseif($ddb->dt[method] == ORDER_METHOD_CASH){
			$method = "<label class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}else{
            $receipt_y = "제휴사";
		}

		if($ddb->dt[delivery_pay_type] == "1"){
			$delivery_pay_type = "선불";
		}elseif($ddb->dt[delivery_pay_type] == "2"){
			$delivery_pay_type = "착불";
		}else{
			$delivery_pay_type = "무료";
		}

		switch($ddb->dt[delivery_method]){
			case 'TE' :
				//$delivery_type = '택배';
				break;
			case 'QU':
				$delivery_type = '퀵서비스';
				break;
			case 'TR':
				$delivery_type = '화물(트럭)';
				break;
			case 'SE':
				$delivery_type = '방문수령';
				break;
			case 'DI':
				$delivery_type = '직배송';
				break;
		
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
		$receipt_type="";
		if($ddb->dt[receipt_y]=="Y") {
			if($ddb->dt[method]=="0" || $ddb->dt[method]=="4" || $ddb->dt[method]=="5" || $ddb->dt[method]=="10") {
				//$receipt_type="현금영수증";
				$sql="SELECT m_useopt FROM receipt WHERE order_no='".$ddb->dt[oid]."' ";
				$od_db->query($sql);
				if($od_db->total) {
					$od_db->fetch();
					switch($od_db->dt["m_useopt"]) {
						case("0") : $receipt_type="소득공제";
						break;
						case("1") : $receipt_type="지출증빙";
						break;
						default : $receipt_type="미발급";
					}
				} else {
					$receipt_type="미발급";
				}
			} else if($ddb->dt[method]=="1") {
				$receipt_type="카드전표";
			}
		} else if($ddb->dt[taxsheet_yn]=="Y") {
			$receipt_type="세금계산서";
		} else {
			$receipt_type="미발급";
		}

		if($ddb->dt[tax_affairs_yn]=="Y") {
			$affairs_states="발행";
		} else {
			$affairs_states="미발행";
		}

		if($ddb->dt[method]=="1") $affairs_states="발행";

		if($ddb->dt[oid] != $b_oid){
		//f0fff2
		$Contents .= "<tr>
							<td class='' style='background-color:#fff7da;height:30px;font-weight:bold;' class=blue colspan='".($admininfo[admin_level]==9 ? "13" : "12")."' >
								<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
									<col width='15px'/>
									<col width='8%'/>
									<col width='15%'/>
									<col width='*'/>
									<col width='11%'/>
									<col width='11%'/>
									<col width='11%'/>
									<col width='11%'/>
									<col width='11%'/>
									<col width='6%'/>
									<tr>
										<td align='center' style='background-color:#fff7da;'><input type=checkbox name='oid[]' id='oid' value='".$ddb->dt[oid]."' ><input type=hidden name='bstatus[".$ddb->dt[oid]."]' value='".$ddb->dt[ostatus]."'><input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."'></td>
										<td  align='center' style='background-color:#fff7da;'><font color='#000000' class=small><b>".getOrderFromName($ddb->dt[order_from])."</b></font></td>
										<td  align='center'  style='background-color:#fff7da;'nowrap><font color='orange' class=small><b>".$ddb->dt[regdate]."</b></font></td>
										<td  align='center'  style='background-color:#fff7da;'nowrap><font color='blue' class=small><b><a href=\"../order/orders.read.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[oid]."</a></b>".($ddb->dt[delivery_box_no] ? "<b style='color:red;'>-".$ddb->dt[delivery_box_no]."</b>":"")."</font></td>
										<td  align='center'  style='background-color:#fff7da;'nowrap><font color='#000000' class=small><b>".$ddb->dt[bname]."/".$ddb->dt[rname]."</b></font></td>
										<td  align='center'  style='background-color:#fff7da;'nowrap><font color='#000000' class=small><b>".$method." / ".getPaymentAgentType($db->dt[payment_agent_type],'img')."</b></font></td>
										<td  align='center' style='background-color:#fff7da;' nowrap><font color='red' class=small><b>".getOrderStatus($ddb->dt[ostatus]).(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") && $ddb->dt[ostatus] =='IR' && $admininfo[admin_level]==9 ? " <img src='../images/".$admininfo["language"]."/btn_incom_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."','', 'IR', '".ORDER_STATUS_INCOM_COMPLETE."')\" style='cursor:pointer;' >":"").($ddb->dt[ostatus] =='IC' && $ddb->dt[bank_input_date] ? "(".$ddb->dt[bank_input_date].")" : "")."</b></font></td>
										<!--td  align='center'  style='background-color:#fff7da;'nowrap><font color='#000000' class=small><b>현금영수증/".$receipt_y."</b></font></td-->
										<td  align='center'  style='background-color:#fff7da;'nowrap><font color='#000000' class=small><b>".$receipt_type."/".$affairs_states."</b></font></td>";
										if($admininfo[admin_level]==9){
											$Contents .= "<td  align='center'  style='background-color:#fff7da;'nowrap><font color='red' class=small><b>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($ddb->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</b></font></td>";
										}
										$Contents .= "
										<td  align='center'  style='background-color:#fff7da;'nowrap>";

									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
											$Contents .= "<a href=\"../order/orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'><!--btc_modify.gif--></a> ";
									}else{
											$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'><!--btc_modify.gif--></a> ";
									}
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
											$Contents .= $delete;
									}
									
		$Contents .= "
										</td>
									</tr>
								</table>
							</td>
						</tr>";
		}

		$Contents .= "
						<tr>
							<td >
								<TABLE>
									<TR>
										<TD align='center'>
											<a  href='../".$folder_name."/goods_input.php?id=".$ddb->dt[pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], 'm',$ddb->dt)."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], 'm',$ddb->dt)."'  width=50 style='margin:5px;'></a><br/>";
											
											if($ddb->dt[product_type]=='21'||$ddb->dt[product_type]=='31'){
											$Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($ddb->dt[product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$ddb->dt[product_type].".gif' align='absmiddle' ></label> ";
											}
											if($ddb->dt[stock_use_yn]=='Y'){
											$Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
											}

						$Contents .= "
										</TD>
										<td width='5'></td>
										<TD class=small style='line-height:140%'>";
				if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
					$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b></a><br>";
				}
				if($ddb->dt[co_pid] != "" && $ddb->dt[co_pid] != "0000000000"){
					$Contents .= "<img src='../images/".$admininfo["language"]."/ico_wholesale.gif' border=0 align=absmiddle  title='도매주문'>  ";
				}
				
				if($ddb->dt[product_type]=='99'){
					$Contents .= "<b class='red' >".$ddb->dt[pname]."</b><br/><strong>".get_product_setname($ddb->dt[pid],$ddb->dt[option1],"<br />")."</strong>".$ddb->dt[sub_pname];
				}else if($ddb->dt[product_type]=='21'||$ddb->dt[product_type]=='31'){
					$Contents .= "<b class='blue' >".$ddb->dt[pname]."</b><br/><strong>".get_product_setname($ddb->dt[pid],$ddb->dt[option1],"<br />")."</strong>".$ddb->dt[sub_pname];
				}else{
					$Contents .= $ddb->dt[pname];
				}
				$Contents .="
										</TD>
									</TR>
								</TABLE>
							</td>
							<td>".strip_tags($ddb->dt[option_text])."</td>
							<td class='' align=center>".number_format($ddb->dt[coprice])."</td>
							<td class='' align=center>".number_format($ddb->dt[listprice])."</td>
							<td class='' align=center>".number_format($ddb->dt[psprice])."</td>
							<td class='' align=center>".number_format($ddb->dt[pcnt])."</td>
							<td class='' align=center>".number_format($ddb->dt[ptprice])."</td>";

				if($bcompany_id != $ddb->dt[company_id]){
					$sql = "SELECT COUNT(DISTINCT(od.od_ix)) AS com_cnt
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$db->dt[oid]."' AND od.company_id='".$ddb->dt[company_id]."' 
						$addWhere $order_view_type_str ";
						//o.payment_price 추가 kbk 13/05/31
						//AND od.product_type NOT IN (".implode(',',$sns_product_type).") 20130828 Hong

					$od_db->query($sql);//$od_db는 상단에서 선언
					$od_db->fetch();
					$com_cnt=$od_db->dt["com_cnt"];
					$Contents .="<td class='' align=center style='line-height:140%;' ".($bcompany_id != $ddb->dt[company_id] ? "rowspan='".$com_cnt."'":"")."
					>".($ddb->dt[payment_agent_type] == 'O' ? $delivery_type:"")."<br>".number_format($ddb->dt[delivery_totalprice])."<br>".$delivery_pay_type." </td>";
				}

		$Contents .="
							<td class='' align=center>".number_format($ddb->dt[reserve]*$ddb->dt[pcnt])."P</td>
							<td class='' align=center>".($ddb->dt[ptprice]*$ddb->dt[commission]/100)."(".number_format($ddb->dt[commission])."%)</td>
							<td class='' align=center>".number_format($ddb->dt[stock])."/-".number_format($ddb->dt[sell_ing_cnt])."/".($ddb->dt[stock]-$ddb->dt[sell_ing_cnt] < 0 ? "<b class='red'>".number_format($ddb->dt[stock]-$ddb->dt[sell_ing_cnt])."</b>" : "-0")."</td>
							<td class='' point' align='center'>".$one_status."<br><b>".$ddb->dt[admin_message]."</b></td>";
		if($admininfo[admin_level]==9){
			$Contents .= "<td class='' point' align='center'>".getOrderStatus($ddb->dt[delivery_status])."</td>";
		}
		$Contents .= "
						</tr>";
		/*
		if($j == ($ddb->total-1)){
		$Contents .= "<tr>
							<td class='' style='height:30px;font-weight:bold;' colspan=2 align='center' >
								소계
							</td>
							<td class='' style='font-weight:bold;' class=blue colspan=10>
								<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
									<tr>
										<td align='center' ><font  class=small ><b>주문금액</b></font></td>
										<td align='center' nowrap ><font  class=small ><b> - 할인가</b></font></td>
										<td align='center' nowrap ><font  class=small ><b> - 쿠폰할인액</b></font></td>
										<td align='center'  nowrap ><font  class=small ><b> - 회원할인액</b></font></td>
										<td align='center'  nowrap ><font  class=small ><b> - 에누리</b></font></td>
										<td align='center'  nowrap ><font  class=small ><b> - 배송할인액</b></font></td>
										<td align='center'  nowrap ><font  class=small ><b> - 마일리지사용액</b></font></td>
										<td align='center'  nowrap ><font  class=small ><b> = 결제금액</b></font></td>
										<td align='center'  nowrap ><font  class=small ><b> + 예치금</b></font></td>
									</tr>
								</table>
							</td>
						</tr>";
		}*/

	/*
		$Contents .= "<tr height=28 >";
		if($ddb->dt[oid] != $b_oid){
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='' nowrap align='center'></td>";
			//$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class='' align=center></td>";
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='point' style='line-height:140%' align=center>";

			$Contents .= $ddb->dt[regdate]."<br>
											<a href=\"orders.read.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[oid]."</a><br>

										</td>";
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." style='line-height:140%' align=center class=''>
			".($ddb->dt[user_id] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$ddb->dt[user_id]."',950,500,'member_info')\" >".Black_list_check($ddb->dt[user_id],$ddb->dt[bname])."</a>":$ddb->dt[bname])."<br>
			<span class='small'>".($ddb->dt[mem_group] != "" ? $ddb->dt[mem_group] : $ddb->dt[order_from])."</span></td>";
		}
		$Contents .= "
						<td class='' style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD><a  href='../product/goods_input.php?id=".$ddb->dt[pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], 'm',$ddb->dt)."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], 'm',$ddb->dt)."'  width=50 style='margin:5px;'></a></TD>
									<td width='5'></td>
									<TD class=small style='line-height:140%'>";
			if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b></a><br>";
			}
			if($ddb->dt[co_pid] != "" && $ddb->dt[co_pid] != "0000000000"){
				$Contents .= "<img src='../images/".$admininfo["language"]."/ico_wholesale.gif' border=0 align=absmiddle  title='도매주문'>  ";
			}

			$Contents .= cut_str($ddb->dt[pname],30)."<br><b>".$ddb->dt[option_text]."</b>
									</TD>

								</TR>
							</TABLE>
						</td>";

			$Contents .="<td class='' align='center'  nowrap>".getProductType($ddb->dt[product_type])."</td>";

                $Contents .="

						<td class='' align=center>".number_format($ddb->dt[ptprice])."</td>
						<td class='' align=center>".number_format($ddb->dt[reserve])."P</td>";

					$Contents .="	<td class='' align=center>".number_format($ddb->dt[commission])." %</td>
						<td class='' point' align='center'>".$one_status."<br><b>".$ddb->dt[admin_message]."</b></td>";
		if($ddb->dt[oid] != $b_oid){
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class='' align='center'  style='padding:3px;' nowrap><!--img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('taxbill.php?uid=".$ddb->dt[user_id]."&oid=".$ddb->dt[oid]."',680,800,'sendsms')\" style='cursor:hand;'-->";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<a href=\"orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'><!--btc_modify.gif--></a> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'><!--btc_modify.gif--></a> ";
			}
			if($ddb->dt[co_pid] != "" && $ddb->dt[co_pid] != "0000000000" && false){
				$Contents .= "<br><a href=\"javascript:PoPWindow('../goodss/goodss_order.pop.php?oid=".$ddb->dt[oid]."&od_ix=".$ddb->dt[od_ix]."',600,300,'goodss_order')\"  ><!--a href=\"#\" onclick=\"GoodssOrder('".$ddb->dt[oid]."')\"--><img src='../images/".$admininfo["language"]."/btn_whosaleorder.gif' border=0 align=absmiddle style='margin:2px;' title='도매주문'></a> ";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .= "<br>".$delete;
			}

			$Contents .= "</td>";
		}
		$Contents .= "</tr>";
		*/

		$b_oid = $ddb->dt[oid];
		$bcompany_id = $ddb->dt[company_id];
	}
	}
}else{
$Contents .= "<tr height=50><td colspan=14 align=center>조회된 결과가 없습니다.</td></tr>
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
/*
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

	$Contents .= "선택된 항목을 ";
	if($admininfo[admin_level] == 9){

	$Contents .= "
			<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
					<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
					<!--option value='".ORDER_STATUS_ADVANCE_ING."' >".getOrderStatus(ORDER_STATUS_ADVANCE_ING)."</option-->";

//해외배송관리조건걸어주어야함!
	$Contents .= "
					<option value='".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY)."</option>
					<!--option value='".ORDER_STATUS_AIR_TRANSPORT_READY."' >".getOrderStatus(ORDER_STATUS_AIR_TRANSPORT_READY)."</option-->";

	$Contents .= "
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

}
*/
$Contents .= "

    </td>
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
$Contents .= HelpBox("주문리스트", $help_text);

$P = new LayOut();
if($view_type == "sellertool"){
	$P->strLeftMenu = sellertool_menu();
}else if($view_type == "offline_order"){
	$P->strLeftMenu = offline_order_menu();
}else if($view_type == "pos_order"){
	$P->strLeftMenu = pos_order_menu();
}else if($view_type == "sc_order"){
	$P->strLeftMenu = sns_menu();
}else{
	$P->strLeftMenu = order_menu();
}
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n".$Script;
$P->Navigation = "주문관리 > 주문리스트";
$P->title = "주문리스트";
$P->strContents = $Contents;
$P->PageStr = page_bar($total, $page, $max,$query_string."#list_top","");
echo $P->PrintLayOut();

?>