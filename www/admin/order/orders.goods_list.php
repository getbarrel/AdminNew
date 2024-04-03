<?
include_once("../class/layout.class");
include("../order/orders.lib.php");
include("../inventory/inventory.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));
	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

$ddb = new Database;
 $db2 = new Database;

if(!$title_str){
	$title_str  = "매출진행관리";
}

if(!$parent_title){
	$parent_title  = "매출관리";
}

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-86400*date("w"));
$lastday = date("Ymd", time()+86400*(6-date("w")));
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);

if($max == ""){
	$max = 15; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
 if($view_type == 'sc_order'){
	$where = "WHERE od.status != 'SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
	$folder_name = "sns";
 }else{
	$where = "WHERE od.status != 'SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
	$folder_name = "product";
}


if($mmode == "personalization"){
	$where .= " and o.user_code = '".$mem_ix."' ";
}

if($md_code != ""){
    $where .= "and od.md_code = '".$md_code."'";
}

if($mode!="search"){
	if(($view_type == 'sc_order' && $pre_type==ORDER_STATUS_INCOM_COMPLETE) || $pre_type == ORDER_STATUS_RETURN_APPLY || $pre_type==ORDER_STATUS_RETURN_ING || $pre_type == ORDER_STATUS_EXCHANGE_APPLY || $pre_type==ORDER_STATUS_EXCHANGE_ING){
		$orderdate=0;
	}else{
		$orderdate=1;
	}
}

if(!$date_type){
	$date_type="o.order_date";
}

if($orderdate){
	//$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
	$where .= "and ".$date_type." between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}

if($pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE){
	if(empty($refund_type)){
		$refund_type = $fix_type;
	}
}else{
	if(empty($type)){
		$type = $fix_type;
	}
}


if($product_type != ""){
	$where .= "and od.product_type = '".$product_type."'";
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
	if($view_type == 'pos_order'){
		$where .= "and od.status = 'RC' ";
	}else{
		if($type){
			$where .= "and od.status = '$type' ";
		}
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

if(is_array($refund_method)){
	for($i=0;$i < count($refund_method);$i++){
		if($refund_method[$i]){
			if($refund_method_str == ""){
				$refund_method_str .= "'".$refund_method[$i]."'";
			}else{
				$refund_method_str .= ", '".$refund_method[$i]."' ";
			}
		}
	}

	if($refund_method_str != ""){
		$where .= "and o.refund_method in ($refund_method_str) ";
	}
}else{
	if($refund_method){
		$where .= "and o.refund_method = '$refund_method' ";
	}
}

$left_join="";

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
		$where .= "and op.method in ($method_str) ";
		$left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method in ($method_str)) ";
	}
}else{
	if($method){
		$where .= "and op.method = '$method' ";
		$left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method = '$method') ";
	}
}

if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (bname LIKE '%".trim($search_text)."%'  or odd.rname LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}
	if($search_type == "combi_name" || substr_count($search_type,'odd.') > 0){
		$left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
	}
}

if($send_type!=""){
	$where .= "and odd.send_type = '$send_type' ";
	if(substr_count($left_join,'shop_order_detail_deliveryinfo') == 0){
		$left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
	}
}

if($bank!=""){
	$where .= "and op.bank = '$bank' ";
	if(substr_count($left_join,'shop_order_payment op')==0){
		$left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.bank = '$bank') ";
	}
}

if($reason_code!=""){
	$where .= "and os.reason_code = '".$reason_code."' ";
	$left_join .= " left join shop_order_status os on (os.od_ix=od.od_ix and os.reason_code = '".$reason_code."') ";
}


if(is_array($p_admin) && count($p_admin) == 1){
	if($p_admin[0]=="A"){
		$where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
	}elseif($p_admin[0]=="S"){
		$where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
	}
}else{
	if($p_admin=="A"){
		$where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
	}elseif($p_admin=="S"){
		$where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
	}
}

if($stock_use_yn != ""){
	$where .= "and od.stock_use_yn = '".$stock_use_yn."'";
}

if($view_type == 'inventory'){
	if($order_cnt == '1'){
		$where .= " and (select count(odd.od_ix) as cnt from shop_order_detail as odd where odd.oid = od.oid) = 1";
	}else if($order_cnt == '2'){
		$where .= " and (select count(odd.od_ix) as cnt from shop_order_detail as odd where odd.oid = od.oid) > 1";
	}
}

if($mall_ix != ""){
	$where .= "and od.mall_ix = '".$mall_ix."'";
}

if(is_array($real_refund_method)){
    $refundMethod = "";
    foreach($real_refund_method as $p=>$v){
        if($refundMethod == ''){
            $refundMethod = "'".$v."'";
        }else{
            $refundMethod .= ",'".$v."'";
        }
    }
    $where .= "and od.real_refund_method in (".$refundMethod.")";
}

if(is_array($exchange_delivery_type)){
    $exchange_delivery = "";
    foreach($exchange_delivery_type as $p=>$v){
        if($exchange_delivery == ''){
            $exchange_delivery = "'".$v."'";
        }else{
            $exchange_delivery .= ",'".$v."'";
        }
    }
    $where .= "and od.exchange_delivery_type in (".$exchange_delivery.")";
}


$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "$parent_title > $title_str ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<form name='search_frm' method='get' action=''>
			<input type='hidden' name='mode' value='search' />
			<input type='hidden' name='mmode' value='$mmode'>
			<input type='hidden' name='mem_ix' value='$mem_ix'>
			<table width=100%  border=0>
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
											<col width=35%>";
	if($_SESSION["admin_config"][front_multiview] == "Y"){
	$Contents .= "
	<tr>
		<td class='search_box_title' colspan='1'> 프론트 전시 구분</td>
		<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
	</tr>";
	}
	$Contents .= "";
										if($admininfo[admin_level]==9){
											$Contents .= "
											<tr height=30>
												<th class='search_box_title' >판매처 선택 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' nowrap colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<TR height=25>";

											if($view_type == "offline_order" || $view_type_order == 'offline_order_order'){
												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>통합구매</label></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
													";
											}else if($view_type == 'pos_order'){
												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." checked><label for='order_from_pos'>POS</label></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
													";
											}else{
												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>";
															$slave_db->query("select * from sellertool_site_info where disp='1' ");
															$sell_order_from=$slave_db->fetchall();
															for($i=0;$i<count($sell_order_from);$i++){
																	
																	if($i==5 || ($i > 5 && $i%8==5)) $Contents .= "</TR><TR>";

																	$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
															}
											}

										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>";
										}

									if((($view_type == 'offline_order' && !($pre_type == ORDER_STATUS_EXCHANGE_APPLY || $pre_type == ORDER_STATUS_EXCHANGE_ING || $pre_type == ORDER_STATUS_RETURN_APPLY || $pre_type == ORDER_STATUS_RETURN_ING) )|| $view_type_order == 'offline_order_order') && $pre_type != 'MethodBank' ){		//영업관리용 2013-07-05 이학봉
										$Contents .= "
											<tr>
												<th class='search_box_title'>결제상태 </th>
												<td class='search_box_item' colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>";
													if($pre_type == ORDER_STATUS_INCOM_COMPLETE){
													$Contents .= "
															<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." checked ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
															<TD></TD>";
													}else if($pre_type == ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE){
													$Contents .= "
															<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,$type,' checked')." checked ><label for='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label></TD>
															<TD></TD>";
													}else if($pre_type == ORDER_STATUS_CANCEL_COMPLETE){
													$Contents .= "
															<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." checked ><label for='type_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>
															<TD></TD>";
													}else if($pre_type == ORDER_STATUS_INCOM_READY){
													$Contents .= "
															<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." checked ><label for='type_".ORDER_STATUS_INCOM_READY."'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
															<TD></TD>";
													} else{
													$Contents .= "
															<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_DEFERRED_PAYMENT."' value='".ORDER_STATUS_DEFERRED_PAYMENT."' ".CompareReturnValue(ORDER_STATUS_DEFERRED_PAYMENT,$type,' checked')." checked ><label for='type_".ORDER_STATUS_DEFERRED_PAYMENT."'>".getOrderStatus(ORDER_STATUS_DEFERRED_PAYMENT)."</label></TD>
															<TD></TD>";
													}
													$Contents .= "
														</TR>
													</TABLE>
												</td>
											</tr>	
										";
									}

if($pre_type == 'MethodBank'){
$Contents .= "
											<tr>
												<th class='search_box_title'>처리상태</th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=30>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_READY."'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>";

													if($view_type == 'offline_order'){
													$Contents .= "
															<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_DEFERRED_PAYMENT."' value='".ORDER_STATUS_DEFERRED_PAYMENT."' ".CompareReturnValue(ORDER_STATUS_DEFERRED_PAYMENT,$type,' checked')." checked ><label for='type_".ORDER_STATUS_DEFERRED_PAYMENT."'>".getOrderStatus(ORDER_STATUS_DEFERRED_PAYMENT)."</label></TD>
															<TD></TD>";
													}

													$Contents .= "
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
}

if($pre_type == "MethodBank"){
$bank_info=print_shop_bank();
//print_r($bank_info);
$Contents .= "
											<tr>
												<th class='search_box_title'>무통장계좌</th>
												<td class='search_box_item' colspan=3>
													<select name='bank'>
														<option value=''>계좌를 선택해주요.</option>";
														for($i=0;$i<count($bank_info);$i++){
															$Contents .= "<option value='".$bank_info[$i][bank_name]." ".$bank_info[$i][bank_number]." ".$bank_info[$i][bank_owner]."' ".CompareReturnValue($bank_info[$i][bank_name]." ".$bank_info[$i][bank_number]." ".$bank_info[$i][bank_owner],$bank,' selected').">".$bank_info[$i][bank_name]." ".$bank_info[$i][bank_number]."</option>";
														}
														
$Contents .= "
													</select>
												</td>
											</tr>
											";
}
						if($pre_type==ORDER_STATUS_CANCEL_COMPLETE||$pre_type=='refund' || $view_type == "buyer_accounts"){
							$Contents .= "
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
						}
                        if($pre_type=='sos_product' ){
                            $Contents .= "
                                                                    <tr>
                                                                        <th class='search_box_title' >주문상태 </th>
                                                                        <td class='search_box_item' colspan='3'>
                                                                        <table cellpadding=0 cellspacing=0 width='100%' border='0' >
                                                                            <col width='15%'>
                                                                            <col width='15%'>
                                                                            <col width='15%'>
                                                                            <col width='15%'>
                                                                            <col width='15%'>
                                                                            <col width='15%'>
                                                                            <TR height=25>
                                                                                <TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
                                                                                <TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$type,' checked')." ><label for='type_".ORDER_STATUS_BUY_FINALIZED."'>".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label></TD>
                                                                                <TD></TD>
                                                                                <TD></TD>
                                                                                <TD></TD>
                                                                                <TD></TD>
                                                                            </TR>
                                                                        </TABLE>
                                                                        </td>
                                                                    </tr>";
                        }
						/*
						if($pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE){
							$Contents .= "
											<tr>
												<th class='search_box_title' >환불방법 </th>
												<td class='search_box_item' colspan='3'>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='16%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=25>
														<TD><input type='checkbox' name='refund_method[]' id='refund_method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$refund_method,' checked')." ><label for='refund_method_".ORDER_METHOD_CARD."' >카드(PG부분취소연결)</label></TD>
														<TD><input type='checkbox' name='refund_method[]' id='refund_method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$refund_method,' checked')." ><label for='refund_method_".ORDER_METHOD_ICHE."' >실(PG부분취소연결)</label></TD>
														<TD><input type='checkbox' name='refund_method[]' id='refund_method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$refund_method,' checked')." ><label for='refund_method_".ORDER_METHOD_VBANK."'>가상계좌</label></TD>
														<TD><input type='checkbox' name='refund_method[]' id='refund_method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$refund_method,' checked')." ><label for='refund_method_".ORDER_METHOD_BANK."' >무통장</label></TD>
														<TD><input type='checkbox' name='refund_method[]' id='refund_method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$refund_method,' checked')." ><label for='refund_method_".ORDER_METHOD_CASH."' >현금</label></TD>
														<TD><input type='checkbox' name='refund_method[]' id='refund_method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$refund_method,' checked')." ><label for='refund_method_".ORDER_METHOD_SAVEPRICE."' >예치금</label></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
						}
						*/


if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING){

if($admininfo[admin_level] == 9){
	if($company_id != ""){
		$state_cnt_where = " and od.company_id = '".$company_id."'";
	}

	if($admininfo[mem_type] == "MD"){
		$state_cnt_where = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}
}else if($admininfo[admin_level] == 8){
	$state_cnt_where = " and od.company_id = '".$admininfo[company_id]."'";
}

$slave_db->query("select status , count(*) as cnt from shop_order_detail od where status in ('".ORDER_STATUS_EXCHANGE_APPLY."','".ORDER_STATUS_EXCHANGE_DENY."','".ORDER_STATUS_EXCHANGE_ING."','".ORDER_STATUS_EXCHANGE_DELIVERY."','".ORDER_STATUS_EXCHANGE_ACCEPT."','".ORDER_STATUS_EXCHANGE_DEFER."','".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."') AND product_type NOT IN (".implode(',',$sns_product_type).") $state_cnt_where group by status");
$state_cnt_array=array();
$state_cnt_date=$slave_db->fetchall("object");
if(count($state_cnt_date)){
	foreach($state_cnt_date as $scd){
		$state_cnt_array[$scd["status"]]=$scd["cnt"];
	}
}

$Contents .= "						<tr height=30>
												<th class='search_box_title'>교환처리상태</th>
												<td class='search_box_item' colspan=3>";
										if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){
											$Contents .= "
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_APPLY])."</b>)</label>
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_DENY."' value='".ORDER_STATUS_EXCHANGE_DENY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DENY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DENY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DENY)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_DENY])."</b>)</label>
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_ING])."</b>)</label>
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_DELIVERY."' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DELIVERY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_DELIVERY])."</b>)</label>";
										}
											$Contents .= "
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_ACCEPT."' value='".ORDER_STATUS_EXCHANGE_ACCEPT."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ACCEPT,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_ACCEPT."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ACCEPT)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_ACCEPT])."</b>)</label>
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_DEFER."' value='".ORDER_STATUS_EXCHANGE_DEFER."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DEFER,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DEFER."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DEFER)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_DEFER])."</b>)</label>
													<input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' value='".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_IMPOSSIBLE,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_IMPOSSIBLE)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_EXCHANGE_IMPOSSIBLE])."</b>)</label>";
										if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){
											$Contents .= "
													<!--input type='radio' name='type' id='type_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label-->";//교환확정으로 처리할 경우 배송준비중으로 변경하므로 검색에서 교환확정 제외 with 박과장님 kbk 13/08/06
										}
											$Contents .= "
												</td>
											</tr>";
}

if($pre_type==ORDER_STATUS_RETURN_APPLY||$pre_type==ORDER_STATUS_RETURN_ING){

if($admininfo[admin_level] == 9){
	if($company_id != ""){
		$state_cnt_where = " and od.company_id = '".$company_id."'";
	}

	if($admininfo[mem_type] == "MD"){
		$state_cnt_where = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}
}else if($admininfo[admin_level] == 8){
	$state_cnt_where = " and od.company_id = '".$admininfo[company_id]."'";
}

$slave_db->query("select status , count(*) as cnt from shop_order_detail od where status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_DENY."','".ORDER_STATUS_RETURN_ING."','".ORDER_STATUS_RETURN_DELIVERY."','".ORDER_STATUS_RETURN_ACCEPT."','".ORDER_STATUS_RETURN_DEFER."','".ORDER_STATUS_RETURN_IMPOSSIBLE."') AND product_type NOT IN (".implode(',',$sns_product_type).") $state_cnt_where group by status");
$state_cnt_array=array();
$state_cnt_date=$slave_db->fetchall("object");
if(count($state_cnt_date)){
	foreach($state_cnt_date as $scd){
		$state_cnt_array[$scd["status"]]=$scd["cnt"];
	}
}

$Contents .= "						<tr height=30>
												<th class='search_box_title'>반품처리상태</th>
												<td class='search_box_item' colspan=3>";
										if($view_type == "pos_order"){
											if($pre_type==ORDER_STATUS_RETURN_APPLY){
												$Contents .= "
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." checked><label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
											}
										}else{
											if($pre_type==ORDER_STATUS_RETURN_APPLY){
												$Contents .= "
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_APPLY."' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_APPLY."'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_APPLY])."</b>)</label>
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_DENY."' value='".ORDER_STATUS_RETURN_DENY."' ".CompareReturnValue(ORDER_STATUS_RETURN_DENY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_DENY."'>".getOrderStatus(ORDER_STATUS_RETURN_DENY)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_DENY])."</b>)</label>
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' ".CompareReturnValue(ORDER_STATUS_RETURN_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_ING."'>".getOrderStatus(ORDER_STATUS_RETURN_ING)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_ING])."</b>)</label>
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_DELIVERY."' value='".ORDER_STATUS_RETURN_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_RETURN_DELIVERY,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_DELIVERY."'>".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_DELIVERY])."</b>)</label>";
											}
												$Contents .= "
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_ACCEPT."' value='".ORDER_STATUS_RETURN_ACCEPT."' ".CompareReturnValue(ORDER_STATUS_RETURN_ACCEPT,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_ACCEPT."'>".getOrderStatus(ORDER_STATUS_RETURN_ACCEPT)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_ACCEPT])."</b>)</label>
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_DEFER."' value='".ORDER_STATUS_RETURN_DEFER."' ".CompareReturnValue(ORDER_STATUS_RETURN_DEFER,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_DEFER."'>".getOrderStatus(ORDER_STATUS_RETURN_DEFER)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_DEFER])."</b>)</label>
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_IMPOSSIBLE."' value='".ORDER_STATUS_RETURN_IMPOSSIBLE."' ".CompareReturnValue(ORDER_STATUS_RETURN_IMPOSSIBLE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_RETURN_IMPOSSIBLE)." (<b class='red'>".number_format($state_cnt_array[ORDER_STATUS_RETURN_IMPOSSIBLE])."</b>)</label>";
											if($pre_type==ORDER_STATUS_RETURN_APPLY){
												$Contents .= "
														<input type='radio' name='type' id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
											}
										
										}
											$Contents .= "
												</td>
											</tr>";
}

if($type==ORDER_STATUS_RETURN_APPLY || $type==ORDER_STATUS_EXCHANGE_APPLY){

$Contents .= "						<tr height=30>
												<th class='search_box_title'>배송선택</th>
												<td class='search_box_item' colspan=3>
													<input type='radio' name='send_type' id='send_type_a' value='' ".CompareReturnValue('',$send_type,' checked')." ><label for='send_type_a'>전체</label>
													<input type='radio' name='send_type' id='send_type_1' value='1' ".CompareReturnValue('1',$send_type,' checked')." ><label for='send_type_1'>직접발송</label>
													<input type='radio' name='send_type' id='send_type_2' value='2' ".CompareReturnValue('2',$send_type,' checked')." ><label for='send_type_2'>지정택배요청</label>
												</td>
											</tr>";
}

//사유검색 추가 2014-02-21 HONG
if($pre_type == ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE || $pre_type == ORDER_STATUS_CANCEL_COMPLETE || $pre_type == ORDER_STATUS_CANCEL_APPLY){

$Contents .= "						<tr height=30>
												<th class='search_box_title'>취소사유</th>
												<td class='search_box_item' colspan=3>
													<select name='reason_code' style='font-size:12px;'>";
														$Contents .= "<option value='' >사유</option>";
														foreach($order_select_status_div["A"]["IR"]["CA"] as $key => $val){
															$Contents .= "<option value='".$key."' ".CompareReturnValue($key,$reason_code,' selected').">".$val[title]."</option>";
														}
														$Contents .= "
													</select>
												</td>
											</tr>";
}


							$Contents .= "
											<tr height=33>
												<th class='search_box_title'>
													<select name='date_type'>
													<option value='o.order_date' ".CompareReturnValue('o.order_date',$date_type,' selected').">주문일자</option>";
												if($pre_type == ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE || $pre_type == ORDER_STATUS_CANCEL_COMPLETE){
													$Contents .= "<option value='od.cc_date' ".CompareReturnValue('od.cc_date',$date_type,' selected').">취소완료일자</option>";
												}

												
												
												if($pre_type != ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE){
													$Contents .= "<option value='od.ic_date' ".CompareReturnValue('od.ic_date',$date_type,' selected').">입금일자</option>";
												}

												if($pre_type == ORDER_STATUS_CANCEL_COMPLETE || $pre_type==ORDER_STATUS_REFUND_APPLY || $pre_type==ORDER_STATUS_REFUND_COMPLETE || $pre_type=='refund'){
													$Contents .= "<option value='od.fa_date' ".CompareReturnValue('od.fa_date',$date_type,' selected').">환불신청일자</option>";
													$Contents .= "<option value='od.fc_date' ".CompareReturnValue('od.fc_date',$date_type,' selected').">환불완료일자</option>";
												}

												if($pre_type == ORDER_STATUS_EXCHANGE_APPLY || $pre_type == ORDER_STATUS_EXCHANGE_ING){
													$Contents .= "<option value='od.ea_date' ".CompareReturnValue('od.ea_date',$date_type,' selected').">교환요청일자</option>";
												}

												if($pre_type == ORDER_STATUS_RETURN_APPLY || $pre_type == ORDER_STATUS_RETURN_ING){
													$Contents .= "<option value='od.ra_date' ".CompareReturnValue('od.ra_date',$date_type,' selected').">반품요청일자</option>";
												}

												$Contents .= "
													</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item' colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>";



						if($pre_type==ORDER_STATUS_CANCEL_COMPLETE||$pre_type==ORDER_STATUS_INCOM_READY||$pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE){
							$Contents .= "
											<tr height=30>
												<th class='search_box_title'>결제방법</th>
												<td class='search_box_item'>";
								if($pre_type==ORDER_STATUS_INCOM_READY){
									$Contents .= "
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;";
								}elseif($pre_type==ORDER_STATUS_CANCEL_COMPLETE||$pre_type=='refund'||$pre_type==ORDER_STATUS_REFUND_APPLY||$pre_type==ORDER_STATUS_REFUND_COMPLETE){
									$Contents .= "
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle'></label>&nbsp;
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_RESERVE."' value='".ORDER_METHOD_RESERVE."' ".CompareReturnValue(ORDER_METHOD_RESERVE,$method,' checked')." ><label for='method_".ORDER_METHOD_RESERVE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_RESERVE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_RESERVE.".gif' align='absmiddle'></label>&nbsp;
												
												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CART_COUPON."' value='".ORDER_METHOD_CART_COUPON."' ".CompareReturnValue(ORDER_METHOD_CART_COUPON,$method,' checked')." > <label for='method_".ORDER_METHOD_CART_COUPON."' class='helpcloud' help_width='100' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CART_COUPON)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CART_COUPON.".gif' align='absmiddle'> </label>&nbsp;

												<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ASCROW."' value='".ORDER_METHOD_ASCROW."' ".CompareReturnValue(ORDER_METHOD_ASCROW,$method,' checked')." ><label for='method_".ORDER_METHOD_ASCROW."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ASCROW)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ASCROW.".gif' align='absmiddle'></label>
												
												<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PAYCO."' value='".ORDER_METHOD_PAYCO."' ".CompareReturnValue(ORDER_METHOD_PAYCO,$method,' checked')." ><label for='method_".ORDER_METHOD_PAYCO."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PAYCO)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PAYCO.".gif' align='absmiddle'></label-->";
								}
								$Contents .= "
								</td>
												<th class='search_box_title' >결제형태</th>
												<td class='search_box_item'>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')." ><label for='payment_agent_type_W' class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')." ><label for='payment_agent_type_M' class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>
													<!--
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_O' value='O' ".CompareReturnValue("O",$payment_agent_type,' checked')." ><label for='payment_agent_type_O' class='helpcloud' help_width='90' help_height='15' help_html='오프라인주문'><img src='../images/".$admininfo[language]."/s_payment_agent_type_o.gif' align='absmiddle'></label>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_p' value='P' ".CompareReturnValue("P",$payment_agent_type,' checked')." ><label for='payment_agent_type_O' class='helpcloud' help_width='90' help_height='15' help_html='POS'><img src='../images/".$admininfo[language]."/s_payment_agent_type_p.gif' align='absmiddle'></label>
													-->
												</td>
											</tr>";

						}
                        if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING || $pre_type ==ORDER_STATUS_EXCHANGE_READY){
                            $Contents .= "
											<tr height=30>
												<th class='search_box_title'>처리상태</th>
												<td class='search_box_item' colspan='3'>
                                                    <input type='checkbox' name='exchange_delivery_type[]' id='exchange_delivery_type_I' value='I' " . CompareReturnValue("I", $exchange_delivery_type, ' checked') . " >
                                                    <label for='exchange_delivery_type_I' help_width='90' help_height='15' >입고후발송</label>
                                                    <input type='checkbox' name='exchange_delivery_type[]' id='exchange_delivery_type_C' value='C' " . CompareReturnValue("C", $exchange_delivery_type, ' checked') . " >
                                                    <label for='exchange_delivery_type_C' help_width='90' help_height='15' >맞교환발송</label>
                                                    <input type='checkbox' name='exchange_delivery_type[]' id='exchange_delivery_type_F' value='F' " . CompareReturnValue("F", $exchange_delivery_type, ' checked') . " >
                                                    <label for='exchange_delivery_type_F' help_width='90' help_height='15' >선발송</label>
								                </td>
											</tr>";
                        }
                        if($pre_type != 'sos_product') {
                            $Contents .= "
											<tr height=30>
												<th class='search_box_title'>환불수단</th>
												<td class='search_box_item' colspan='3'>
                                                    <input type='checkbox' name='real_refund_method[]' id='refund_method_cash' value='1' " . CompareReturnValue("1",
                                    $real_refund_method, ' checked') . " >
                                                    <label for='refund_method_cash' help_width='90' help_height='15' >현금</label>
                                                    <input type='checkbox' name='real_refund_method[]' id='refund_method_reserve' value='2' " . CompareReturnValue("2",
                                    $real_refund_method, ' checked') . " >
                                                    <label for='refund_method_reserve' help_width='90' help_height='15' >적립금</label>
								                </td>
											</tr>";

                        }
						
						if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9 && $pre_type!=ORDER_STATUS_INCOM_READY){
											$Contents .= "
											<tr height=30 style='display:none;'>";
												$Contents .= "
                                                <!--
												<th class='search_box_title'>셀러명</th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												-->
												<th class='search_box_title'>담당MD</th>
												<td class='search_box_item' colspan='3'>".MDSelect($md_code)."</td>
												<!--th class='search_box_title' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">상품구분</th>
												<td class='search_box_item'>
													<select name='product_type'>
														<option value='' ".CompareReturnValue('',$product_type,' selected').">전체보기</option>
														<option value='0' ".CompareReturnValue('0',$product_type,' selected').">국내</option>
														<option value='2' ".CompareReturnValue('2',$product_type,' selected').">선매입</option>
														<option value='1' ".CompareReturnValue('1',$product_type,' selected').">사이트 주문</option>
													</select>
												</td-->
											</tr>
											<tr height=30>
												<th class='search_box_title'>상품관리구분 </th>
												<td class='search_box_item' colspan='3'>";
											
											
											if($view_type == 'inventory'){
											$Contents .= "
													<input type='checkbox' name='stock_use_yn' id='stock_use_y' value='Y' ".CompareReturnValue("Y",$stock_use_yn,' checked')." ><label for='stock_use_y'>WMS상품</label>&nbsp;";
											}else{
											$Contents .= "
													<input type='checkbox' name='p_admin[]' id='p_admin_a' value='A' ".CompareReturnValue("A",$p_admin,' checked')." ><label for='p_admin_a'>본사상품</label>&nbsp;
													<!--<input type='checkbox' name='p_admin[]' id='p_admin_s' value='S' ".CompareReturnValue("S",$p_admin,' checked')." ><label for='p_admin_s'>셀러상품</label>&nbsp;-->
													";
											
											}
											$Contents .= "
												</td>
											</tr>";
						}
$Contents .= "
											<tr height=30>
												<th class='search_box_title'>조건검색</th>
												<td class='search_box_item'>
													<table cellpadding='0' cellspacing='0' border='0' ><!--width='100%'-->
													<tr>
														<td >
														<select name='search_type' style='font-size:12px;'>";
														if($mmode != "personalization"){
															$Contents .= "
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자명+수취인명</option>
															<option value='o.bname' ".CompareReturnValue('o.bname',$search_type,' selected').">주문자명</option>
															<option value='o.buserid' ".CompareReturnValue('o.buserid',$search_type,' selected').">주문자ID</option>";
														}
														$Contents .= "
															<option value='od.pname' ".CompareReturnValue('od.pname',$search_type,' selected').">상품명</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='odd.rname' ".CompareReturnValue('odd.rname',$search_type,' selected').">수취인명</option>
															<option value='o.bmobile' ".CompareReturnValue('o.bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='odd.rmobile' ".CompareReturnValue('odd.rmobile',$search_type,' selected').">수취인핸드폰</option>
															<option value='od.invoice_no' ".CompareReturnValue('od.invoice_no',$search_type,' selected').">송장번호</option>
															<option value='od.option_text' ".CompareReturnValue('od.option_text',$search_type,' selected').">옵션명</option>
															<option value='od.gid' ".CompareReturnValue('od.gid',$search_type,' selected').">품목코드</option>
															<option value='od.gu_ix' ".CompareReturnValue('od.gu_ix',$search_type,' selected').">품목시스템코드</option>
														</select>
														</td>
														<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style='' ></td>
													</tr>
													</table>
												</td>
												<td class='input_box_title'> 목록갯수</td>
												<td class='input_box_item'>
													<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
													<option value='5' ".CompareReturnValue(5,$max).">5</option>
													<option value='10' ".CompareReturnValue(10,$max).">10</option>
													<option value='15' ".CompareReturnValue(15,$max).">15</option>
													<option value='20' ".CompareReturnValue(20,$max).">20</option>
													<option value='50' ".CompareReturnValue(50,$max).">50</option>
													<option value='100' ".CompareReturnValue(100,$max).">100</option>
													</select> <span >한페이지에 보여질 갯수를 선택해주세요</span>
												</td>
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
	</tr>
</table>
</form>
<form name=listform method=post action='../order/orders.goods_list.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
<input type='hidden' name='act' value='select_status_update'>

<input type='hidden' name='search_searialize_value' value='". urlencode(serialize($_GET))."'>
<input type='hidden' name='type_param_value' value='". urlencode(serialize($fix_type))."'>
<input type='hidden' name='oet_ix' id='oet_ix' value='' />
<input type='hidden' name='view_type' id='view_type' value='".$view_type."' />
<input type='hidden' name='orderdate' id='orderdate' value='".$orderdate."' />
<input type='hidden' name='startDate' id='startDate' value='".$startDate."' />
<input type='hidden' name='endDate' id='endDate' value='".$endDate."' />

<input type='hidden' name='pre_type' value='$pre_type'>
<input type='hidden' name='product_type' value='$product_type'>
<input type='hidden' id='oid' value=''>
<input type='hidden' id='od_ix' value=''>


<input type='hidden' name='irs' value=''>
<input type='hidden' name='ipw' value=''>


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
	}else{
		$where .= " and o.oid = od.oid ";
	}

	if($view_type == 'offline_order' || $view_type_order == 'offline_order_order'){		//영업관리 용도 2013-07-05 이학봉

		$where .= " and od.order_from in ('offline') ";		// 영업관리는 판매처 : 통합구매 결제상태 : 후불(외상) 만 가져옴
		$ood_where .= " and od.order_from in ('offline') ";

	}else if($view_type == 'pos_order'){
		$where .= " and od.order_from in ('pos')";		// 영업관리는 판매처 : 통합구매 결제상태 : 후불(외상) 만 가져옴
		$ood_where .= " and od.order_from in ('pos') ";
	}

	$sql = "SELECT count(distinct od.od_ix) as total
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
					$left_join
					$where ";
	$master_db->query($sql);
	$master_db->fetch();
	$order_goods_total = $master_db->dt[total];


	$sql = "SELECT od.oid
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
				$left_join
				$where
				GROUP BY od.oid ";

	$master_db->query($sql);
	$total = $master_db->total;

	if($pre_type=='refund'){
		$ORDER_BY = "od.fa_date DESC";
		//$ORDER_BY = "o.order_date DESC";
	}else{
		$ORDER_BY = "o.order_date DESC";
	}

	$sql = "SELECT distinct o.oid
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
				$left_join
				$where
				ORDER BY $ORDER_BY LIMIT $start, $max";
	$master_db->query($sql);


 $Contents .= "<td colspan=3 align=left>
					<b class=blk>전체 주문수(상품수) : <span class='blue'>".$total."</span> (".$order_goods_total.") 건</b>
				 </td>
					<td colspan=9 align=right >";

if($admininfo[admin_level] != 9){
	$Contents .= "<span style='color:red'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
}

/*
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents .= "
	<a href='../order/excel_out.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a> ";
}else{
    $Contents .= "
	<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a> ";
}
*/




if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	//$Contents .= "<a href='../order/orders_excel2003.php?view_type=$view_type&".$type_param."&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
	$Contents .= orderExcelTemplateSelect("O");

	if($pre_type == 'IR') {
        $Contents .= "
        <select name='excel_down_type'>
            <option value='1'>검색한주문 </option>
            <option value='2'>선택한주문 </option>
        </select>
        ";
    }
	$Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다..'>
	<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"excelDown(document.listform)\" ></span>";

}else{
	$Contents .= "
	<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
}

$Contents .= "
  </td>
  </tr>
  </table>
	";

	//뷰가 계속해서 틀려지므로 너무 길어서 따로땜 20130629 Hong
	include("orders.goods_list.contents.php");


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
</table>";

if($pre_type==ORDER_STATUS_EXCHANGE_READY){

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
			$('#ht_level0_delivery').hide();
			$('.ht_level0_delivery_'+status).show();
		}

		$(document).ready(function(){
			HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_DELAY."');
		});

	//-->
	</script>
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>결제/배송처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제/배송처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level0_status'>
			<td class='input_box_title'> <b>처리상태</b></td>
			<td class='input_box_item'> 
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_READY."')\" checked ><label for='level0_update_status_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>
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

}elseif($pre_type==ORDER_STATUS_CANCEL_APPLY){

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
			$('#ht_level0_delivery').hide();
			$('.ht_level0_delivery_'+status).show();
		}

		$(document).ready(function(){";
			if($_SESSION["admininfo"]["admin_level"] ==9){
				$help_text .= "
				HelpTextChangeStatus('".ORDER_STATUS_CANCEL_COMPLETE."');";
			}else{
				$help_text .= "
				HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_READY."');";
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
			<td class='input_box_title'> <b>처리상태</b></td>
			<td class='input_box_item'> ";

				if($_SESSION["admininfo"]["admin_level"]==9){
					$help_text .= "
					<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_CANCEL_COMPLETE."')\" checked><label for='level0_update_status_".ORDER_STATUS_CANCEL_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>";
				}

				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_READY."')\" ".($_SESSION["admininfo"]["admin_level"]!=9 ? "checked":"")."><label for='level0_update_status_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>";
				
				if($_SESSION["admininfo"]["admin_level"]==9){
					$help_text .= "
					<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_DELAY."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label>";
					if($pre_type != ORDER_STATUS_CANCEL_APPLY){
                        $help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_ING."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_ING."'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label>";
                    }
				}
			$help_text .= "
			</td>
		</tr>
		<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_DELIVERY_READY." ht_level0_reason_".ORDER_STATUS_DELIVERY_DELAY."'>
			<td class='input_box_title'> <b>거부사유</b></td>
			<td class='input_box_item'> 
				<select name='level0_reason_code' style='font-size:12px;'>";
					$help_text .= "<option value='' >거부사유</option>";
					foreach($order_select_status_div['A']['CA']['CD'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
			</td>
		</tr>
		<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_DELIVERY_READY." ht_level0_msg_".ORDER_STATUS_DELIVERY_DELAY."' >
			<td class='input_box_title'> <b>기타</b></td>
			<td class='input_box_item'>
				 <input type=text name='level0_msg'  class='textbox' value='' style='width:350px;' >
			</td>
		</tr>
		<tr id='ht_level0_delivery' class='ht_level0_delivery_".ORDER_STATUS_DELIVERY_ING."'>
			<td class='input_box_title'> <b>배송선택</b></td>
			<td class='input_box_item'>
				".deliveryCompanyList2("delivery_company","",$_SESSION["admininfo"]["company_id"],"")." <input type='text' name='deliverycode' class=textbox   size=15 value='' validation=true title='송장번호'>
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

}elseif($pre_type==ORDER_STATUS_INCOM_READY || ($pre_type == ORDER_STATUS_INCOM_COMPLETE && $view_type != "sc_order")  ||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
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
			$('select[name=level0_reason_code]').hide().attr('disabled',true);
			$('select.ht_level0_reason_'+status).show().attr('disabled',false);

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
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_INCOM_COMPLETE."')\" checked><label for='level0_update_status_".ORDER_STATUS_INCOM_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>";
				if($_SESSION["admininfo"]["admin_level"] == 9){
					$help_text .= "
					<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label>";
				}
			}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_READY."')\" checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_DELAY."')\" ><label for='level0_update_status_".ORDER_STATUS_DELIVERY_DELAY."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label>";
				if($_SESSION["admininfo"]["admin_level"] == 9){
					$help_text .= "
					<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_CANCEL_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>";
				}
			}
	$help_text .= "
			</td>
		</tr>";
		if($pre_type==ORDER_STATUS_INCOM_READY){
		$help_text .= "<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' >";
		}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
		$help_text .= "<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_CANCEL_COMPLETE." ht_level0_reason_".ORDER_STATUS_DELIVERY_DELAY."' >";
		}
		$help_text .= "
			<td class='input_box_title'> <b>거부사유</b></td>
			<td class='input_box_item'> 
				
				<select name='level0_reason_code' style='font-size:12px;' class='ht_level0_reason_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>";
					$help_text .= "<option value='' >취소사유</option>";
					foreach($order_select_status_div['A']['IR']['CA'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>

				<select name='level0_reason_code' style='font-size:12px;' class='ht_level0_reason_".ORDER_STATUS_CANCEL_COMPLETE."'>";
					$help_text .= "<option value='' >취소사유</option>";
					foreach($order_select_status_div['A']['IC']['CA'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>

				<select name='level0_reason_code' style='font-size:12px;' class='ht_level0_reason_".ORDER_STATUS_DELIVERY_DELAY."'>";
					$help_text .= "<option value='' >지연사유</option>";
					foreach($order_select_status_div['A']['DD']['DD'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
			</td>
		</tr>";
		if($pre_type==ORDER_STATUS_INCOM_READY){
		$help_text .= "<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' >";
		}elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type ==ORDER_STATUS_DEFERRED_PAYMENT){
		$help_text .= "<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_CANCEL_COMPLETE." ht_level0_msg_".ORDER_STATUS_DELIVERY_DELAY."' >";
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

}elseif(($pre_type==ORDER_STATUS_EXCHANGE_APPLY && $type!=ORDER_STATUS_EXCHANGE_COMPLETE )||$pre_type == ORDER_STATUS_EXCHANGE_ING){
	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>교환처리상태변경</label>
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(status){
			$('#ht_level0_reason').hide();

			$('[name=level0_reason_code]').attr('disabled',true);
			$('.level0_select_reason_'+status).attr('disabled',false);
			$('[name=level0_reason_code]').hide();
			$('.level0_select_reason_'+status).show();

			$('.ht_level0_reason_'+status).show();

			$('#ht_level0_msg').hide();
			$('.ht_level0_msg_'+status).show();
			$('#ht_level0_refund_status').hide();
			$('.ht_level0_refund_status_'+status).show();
			$('#ht_level0_return_product_state').hide();
			$('.ht_level0_return_product_state_'+status).show();
			$('#ht_level0_return_inventory').hide();
			$('.ht_level0_return_inventory_'+status).show();

		}

		$(document).ready(function(){";
			
			if($type==ORDER_STATUS_EXCHANGE_APPLY){//교환요청일때
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DENY."');";
			}elseif($type == ORDER_STATUS_EXCHANGE_DENY){//교환거부일때
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_ING."');";
			}elseif($type == ORDER_STATUS_EXCHANGE_ING){//교환승인일때
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DELIVERY."');";
			}elseif($type == ORDER_STATUS_EXCHANGE_DELIVERY){//교환상품배송중
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_ACCEPT."');";
			}elseif($type == ORDER_STATUS_EXCHANGE_ACCEPT){//교환상품회수
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DEFER."');";
			}elseif($type == ORDER_STATUS_EXCHANGE_DEFER){//교환보류
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."');";
			}elseif($type == ORDER_STATUS_EXCHANGE_IMPOSSIBLE){//교환불가
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DEFER."');";
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
			<td class='input_box_title'> <b>교환처리상태</b></td>
			<td class='input_box_item'>";
			if($type==ORDER_STATUS_EXCHANGE_APPLY){//교환요청일때
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_DENY."' value='".ORDER_STATUS_EXCHANGE_DENY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DENY."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_DENY."'  >".getOrderStatus(ORDER_STATUS_EXCHANGE_DENY)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_ING."')\"><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_EXCHANGE_DENY){//교환거부일때
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_ING."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_ING."'  >".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_EXCHANGE_ING){//교환승인일때
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_DELIVERY."' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DELIVERY."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_DELIVERY."'  >".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_ACCEPT."' value='".ORDER_STATUS_EXCHANGE_ACCEPT."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_ACCEPT."')\"><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_ACCEPT."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ACCEPT)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_EXCHANGE_DELIVERY){//교환상품배송중
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_ACCEPT."' value='".ORDER_STATUS_EXCHANGE_ACCEPT."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_ACCEPT."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_ACCEPT."'  >".getOrderStatus(ORDER_STATUS_EXCHANGE_ACCEPT)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_EXCHANGE_ACCEPT){//교환상품회수
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_DEFER."' value='".ORDER_STATUS_EXCHANGE_DEFER."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DEFER."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_DEFER."'  >".getOrderStatus(ORDER_STATUS_EXCHANGE_DEFER)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' value='".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."')\"><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_IMPOSSIBLE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_EXCHANGE_DEFER){//교환보류
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' value='".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_IMPOSSIBLE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_EXCHANGE_IMPOSSIBLE){//교환불가
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_DEFER."' value='".ORDER_STATUS_EXCHANGE_DEFER."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_DEFER."')\" checked><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_DEFER."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DEFER)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_EXCHANGE_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
			}
	$help_text .= "
			</td>
		</tr>
		<tr id='ht_level0_refund_status' class='ht_level0_refund_status_".ORDER_STATUS_RETURN_COMPLETE."' >
			<td class='input_box_title'> <b>환불처리상태</b></td>
			<td class='input_box_item'> 
				<input type='radio' name='level0_refund_status' id='level0_refund_status_".ORDER_STATUS_REFUND_APPLY."' value='".ORDER_STATUS_REFUND_APPLY."' checked ><label for='level0_refund_status_".ORDER_STATUS_REFUND_APPLY."'>".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</label>
			</td>
		</tr>
		<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_EXCHANGE_DENY." ht_level0_reason_".ORDER_STATUS_EXCHANGE_DEFER." ht_level0_reason_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' >
			<td class='input_box_title'> <b>사유</b></td>
			<td class='input_box_item'>
					<select name='level0_reason_code'  class='level0_select_reason_".ORDER_STATUS_EXCHANGE_DENY."' style='font-size:12px;' >";
						$help_text .= "<option value='' >거부사유</option>";
						foreach($order_select_status_div['A']['EY']['EY'] as $key => $val){
							$help_text .= "<option value='".$key."' >".$val[title]."</option>";
						}
						$help_text .= "
					</select>
					<select name='level0_reason_code'  class='level0_select_reason_".ORDER_STATUS_EXCHANGE_DEFER."' style='font-size:12px;' >";
						$help_text .= "<option value='' >보류사유</option>";
						foreach($order_select_status_div['A']['EF']['EF'] as $key => $val){
							$help_text .= "<option value='".$key."' >".$val[title]."</option>";
						}
						$help_text .= "
					</select>
					<select name='level0_reason_code'  class='level0_select_reason_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' style='font-size:12px;' >";
						$help_text .= "<option value='' >불가사유</option>";
						foreach($order_select_status_div['A']['EM']['EM'] as $key => $val){
							$help_text .= "<option value='".$key."' >".$val[title]."</option>";
						}
						$help_text .= "
					</select>
			</td>
		</tr>";
		//[S] 쉐어드메모리 데이터 로드
		$warehouse_data = sharedControll("warehouse_data");
		//[E] 쉐어드메모리 데이터 로드

		if(!empty($warehouse_data)){
			$help_text .= "
				<script type='text/javascript'>
					$(document).ready(function(){
						loadPlaceData('#regist_company_id', '".trim($warehouse_data["regist_pi_ix"])."');
					});
				</script>
			";
		}
		$help_text .= "
		<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_EXCHANGE_DENY." ht_level0_msg_".ORDER_STATUS_EXCHANGE_DEFER." ht_level0_msg_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE." ht_level0_msg_".ORDER_STATUS_EXCHANGE_ACCEPT."' >
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

	$Contents .= HelpBox($help_title, $help_text,300);
}elseif((($pre_type==ORDER_STATUS_RETURN_APPLY && $type!=ORDER_STATUS_RETURN_COMPLETE )||$pre_type == ORDER_STATUS_RETURN_ING) && $view_type != 'pos_order'){
	$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick=\"ChangeUpdateForm('help_text_level0');\" checked><label for='update_kind_level0'>반품처리상태변경</label>
	</nobr>";

	$help_text = "
	<script type='text/javascript'>
	<!--
		function HelpTextChangeStatus(status){
			$('#ht_level0_reason').hide();

			$('[name=level0_reason_code]').attr('disabled',true);
			$('.level0_select_reason_'+status).attr('disabled',false);
			$('[name=level0_reason_code]').hide();
			$('.level0_select_reason_'+status).show();

			$('.ht_level0_reason_'+status).show();

			$('#ht_level0_msg').hide();
			$('.ht_level0_msg_'+status).show();
			$('#ht_level0_refund_status').hide();
			$('.ht_level0_refund_status_'+status).show();
			$('#ht_level0_return_product_state').hide();
			$('.ht_level0_return_product_state_'+status).show();
			$('#ht_level0_return_inventory').hide();
			$('.ht_level0_return_inventory_'+status).show();

		}

		$(document).ready(function(){";
			
			if($type==ORDER_STATUS_RETURN_APPLY){//반품요청일때
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_ING."');";
			}elseif($type == ORDER_STATUS_RETURN_DENY){//반품거부일때
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_ING."');";
			}elseif($type == ORDER_STATUS_RETURN_ING){//반품승인일때
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_ACCEPT."');";
			}elseif($type == ORDER_STATUS_RETURN_DELIVERY){//반품상품배송중
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_ACCEPT."');";
			}elseif($type == ORDER_STATUS_RETURN_ACCEPT){//반품상품회수
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."');";
			}elseif($type == ORDER_STATUS_RETURN_DEFER){//반품보류
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_IMPOSSIBLE."');";
			}elseif($type == ORDER_STATUS_RETURN_IMPOSSIBLE){//반품불가
				$help_text .= "HelpTextChangeStatus('".ORDER_STATUS_RETURN_DEFER."');";
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
			<td class='input_box_title'> <b>반품처리상태</b></td>
			<td class='input_box_item'>";
			if($type==ORDER_STATUS_RETURN_APPLY){//반품요청일때
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_ING."')\" checked>
				<label for='level0_update_status_".ORDER_STATUS_RETURN_ING."'>".getOrderStatus(ORDER_STATUS_RETURN_ING)."</label>

				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_DENY."' value='".ORDER_STATUS_RETURN_DENY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_DENY."')\" >
				<label for='level0_update_status_".ORDER_STATUS_RETURN_DENY."'  >".getOrderStatus(ORDER_STATUS_RETURN_DENY)."</label>

				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\">
				<label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_RETURN_DENY){//반품거부일때
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_ING."')\" checked><label for='level0_update_status_".ORDER_STATUS_RETURN_ING."'  >".getOrderStatus(ORDER_STATUS_RETURN_ING)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_RETURN_ING){//반품승인일때
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_ACCEPT."' value='".ORDER_STATUS_RETURN_ACCEPT."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_ACCEPT."')\" checked><label for='level0_update_status_".ORDER_STATUS_RETURN_ACCEPT."'>".getOrderStatus(ORDER_STATUS_RETURN_ACCEPT)."</label>

				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_DELIVERY."' value='".ORDER_STATUS_RETURN_DELIVERY."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_DELIVERY."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_DELIVERY."'>".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)."</label>

				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_RETURN_DELIVERY){//반품상품배송중
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_ACCEPT."' value='".ORDER_STATUS_RETURN_ACCEPT."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_ACCEPT."')\" checked><label for='level0_update_status_".ORDER_STATUS_RETURN_ACCEPT."'  >".getOrderStatus(ORDER_STATUS_RETURN_ACCEPT)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_RETURN_ACCEPT){//반품상품회수
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."')\" checked><label for='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_DEFER."' value='".ORDER_STATUS_RETURN_DEFER."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_DEFER."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_DEFER."'  >".getOrderStatus(ORDER_STATUS_RETURN_DEFER)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_IMPOSSIBLE."' value='".ORDER_STATUS_RETURN_IMPOSSIBLE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_IMPOSSIBLE."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_RETURN_IMPOSSIBLE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_DELIVERY_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_RETURN_DEFER){//반품보류
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_IMPOSSIBLE."' value='".ORDER_STATUS_RETURN_IMPOSSIBLE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_IMPOSSIBLE."')\" checked><label for='level0_update_status_".ORDER_STATUS_RETURN_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_RETURN_IMPOSSIBLE)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
			}elseif($type == ORDER_STATUS_RETURN_IMPOSSIBLE){//반품불가
				$help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_DEFER."' value='".ORDER_STATUS_RETURN_DEFER."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_DEFER."')\" checked><label for='level0_update_status_".ORDER_STATUS_RETURN_DEFER."'>".getOrderStatus(ORDER_STATUS_RETURN_DEFER)."</label>
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_RETURN_COMPLETE."')\"><label for='level0_update_status_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label>";
			}
	$help_text .= "
			</td>
		</tr>
		<tr id='ht_level0_refund_status' class='ht_level0_refund_status_".ORDER_STATUS_RETURN_COMPLETE."' >
			<td class='input_box_title'> <b>환불처리상태</b></td>
			<td class='input_box_item'> 
				<input type='radio' name='level0_refund_status' id='level0_refund_status_".ORDER_STATUS_REFUND_APPLY."' value='".ORDER_STATUS_REFUND_APPLY."' checked ><label for='level0_refund_status_".ORDER_STATUS_REFUND_APPLY."'>".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</label>
			</td>
		</tr>
		<tr id='ht_level0_reason' class='ht_level0_reason_".ORDER_STATUS_RETURN_DENY." ht_level0_reason_".ORDER_STATUS_RETURN_DEFER." ht_level0_reason_".ORDER_STATUS_RETURN_IMPOSSIBLE."' >
			<td class='input_box_title'> <b>사유</b></td>
			<td class='input_box_item'> 
				<select name='level0_reason_code'  class='level0_select_reason_".ORDER_STATUS_RETURN_DENY."' style='font-size:12px;' >";
					$help_text .= "<option value='' >거부사유</option>";
					foreach($order_select_status_div['A']['RY']['RY'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<select name='level0_reason_code'  class='level0_select_reason_".ORDER_STATUS_RETURN_DEFER."' style='font-size:12px;' >";
					$help_text .= "<option value='' >보류사유</option>";
					foreach($order_select_status_div['A']['RF']['RF'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
				<select name='level0_reason_code'  class='level0_select_reason_".ORDER_STATUS_RETURN_IMPOSSIBLE."' style='font-size:12px;' >";
					$help_text .= "<option value='' >불가사유</option>";
					foreach($order_select_status_div['A']['RM']['RM'] as $key => $val){
						$help_text .= "<option value='".$key."' >".$val[title]."</option>";
					}
					$help_text .= "
				</select>
			</td>
		</tr>";

		//[S] 쉐어드메모리 데이터 로드
		$warehouse_data = sharedControll("warehouse_data");
		//[E] 쉐어드메모리 데이터 로드

		if(!empty($warehouse_data)){
			$help_text .= "
				<script type='text/javascript'>
					$(document).ready(function(){
						loadPlaceData('#regist_company_id', '".trim($warehouse_data["regist_pi_ix"])."');
					});
				</script>
			";
		}
		$help_text .= "
		<tr id='ht_level0_msg' class='ht_level0_msg_".ORDER_STATUS_RETURN_DENY." ht_level0_msg_".ORDER_STATUS_RETURN_DEFER." ht_level0_msg_".ORDER_STATUS_RETURN_IMPOSSIBLE." ht_level0_msg_".ORDER_STATUS_RETURN_ACCEPT."' >
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

	$Contents .= HelpBox($help_title, $help_text,300);
}elseif($pre_type == 'sos_product'){
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
			$('select[name=level0_reason_code]').hide().attr('disabled',true);
			$('select.ht_level0_reason_'+status).show().attr('disabled',false);

			$('#ht_level0_msg').hide();
			$('.ht_level0_msg_'+status).show();
		}

		$(document).ready(function(){";

        $help_text .= "HelpTextChangeStatus('".ORDER_STATUS_BUY_FINALIZED."');";

    $help_text .= "
		});

	//-->
	</script>
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>SOS 상품 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 결제 처리상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
	<div id='help_text_level0'>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
		<col width=170>
		<col width=*>
		<tr id='ht_level0_status'>
			<td class='input_box_title'> <b>결제상태</b></td>
			<td class='input_box_item'>";

        $help_text .= "
				<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' onclick=\"HelpTextChangeStatus('".ORDER_STATUS_BUY_FINALIZED."')\" checked><label for='level0_update_status_".ORDER_STATUS_BUY_FINALIZED."'  >".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label>";


    $help_text .= "
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
}

$Contents .= "
</form>
";

$Script .= "
<script> 
    function excelDown(frm){
        //if(jQuery('#oet_ix').val().length > 0){location.href='../order/orders_excel2003.php?oet_ix='+jQuery('#oet_ix').val()+'&view_type=$view_type&".$type_param."&".$QUERY_STRING."'}else{alert('엑셀양식을선택해주세요.');}
       
        var oet_ix = $('select[name=oet_ix] :selected').val();        
        if(oet_ix){



							var ig_now = new Date();   //현재시간
							var ig_hour = ig_now.getHours();   //현재 시간 중 시간.



								//	새벽시간(23시~07시), 휴무일(일, 토)
							//if(Number(ig_hour) >= \"23\" || Number(ig_hour) <= \"7\" || Number(ig_now.getDay()) == \"0\" || Number(ig_now.getDay()) == \"6\") {
								var ig_inputString = prompt('사유를 간략하게 입력하세요.\\r\\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

								if(ig_inputString != null && ig_inputString.trim() != '') {
									//	엑셀다운로드 진행

										var str_length = ig_inputString.length;		// 전체길이

										if(str_length > \"20\") {
											alert('사유가 20자 이상 입니다.');
											return false;
										} else {
											var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\\r\\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

												if(ig_inputString_PW != null && ig_inputString_PW.trim() != '') {

													var str_PW_length = ig_inputString_PW.length;		// 전체길이

													if(str_PW_length > \"15\") {
														alert('비밀번호를 15자 이하로 해주세요.');
														return false;
													} else {
														frm.irs.value = ig_inputString;
														frm.ipw.value = ig_inputString_PW;

														frm.action = '../order/orders_excel2003.php';
														frm.act.value = 'excel_down';
														frm.submit();
														frm.action = '../order/orders.goods_list.act.php';
														frm.act.value = 'select_status_update';

													}

												} else {
													alert('비밀번호를 입력해 주세요.');
													return false;
												}
										}


								} else {
									alert('사유를 입력하세요');
									return false;
								}
							/*} else {
								//	일반 업무때 다운로드
								var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\\r\\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

									if(ig_inputString_PW != null && ig_inputString_PW.trim() != '') {

										var str_PW_length = ig_inputString_PW.length;		// 전체길이

										if(str_PW_length > \"15\") {
											alert('비밀번호를 15자 이하로 해주세요.');
											return false;
										} else {
														frm.irs.value = ig_inputString;
														frm.ipw.value = ig_inputString_PW;

														frm.action = '../order/orders_excel2003.php';
														frm.act.value = 'excel_down';
														frm.submit();
														frm.action = '../order/orders.goods_list.act.php';
														frm.act.value = 'select_status_update';

										}

									} else {
										alert('비밀번호를 입력해 주세요.');
										return false;
									}
							}*/








        }else{
            alert('엑셀양식을선택해주세요.')
        }

    }
</script>
";

if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
	$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
	$P->strLeftMenu = order_menu();
	$P->Navigation = "주문관리 > $title_str ";
	$P->title = $title_str;
    $P->NaviTitle = $title_str; 
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	if($view_type == "sellertool"){
		$P->strLeftMenu = sellertool_menu();
	}else if($view_type == "offline_order"){
		$P->strLeftMenu = offline_order_menu();
	}else if($view_type == "pos_order"){
		$P->strLeftMenu = pos_order_menu();
	}else if($view_type == "sc_order"){
		$P->strLeftMenu = sns_menu();
	}else if($view_type  == "buyer_accounts"){
		$P->strLeftMenu = buyer_accounts_menu();
	}else if($view_type  == "inventory"){
		$P->strLeftMenu = inventory_menu();
	}else{
		$P->strLeftMenu = order_menu();
	}
	$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);\n<script language='javascript' src='../include/DateSelect.js'></script>
	$P->addScript = "<script language='javascript' src='../order/orders.goods_list.js'></script>\n<script language='javascript' src='../inventory/placesection.js'></script>\n".$Script;
	$P->Navigation = "주문관리 > $title_str ";
	$P->title = $title_str;
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>