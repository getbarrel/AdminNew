<?php
include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");

include("../campaign/mail.config.php");
include("../order/orders.lib.php");




if($startDate == ""){
    $vdate = date("Y-m-d", time());
    $startDate = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
    $endDate = date("Y-m-d");
}

if(empty($type)){
    $type = $fix_type;
}


if($view_type == 'pos_order' || $view_type == 'sc_order'){
    $rows_cnt = '3';			// 포스관리일 경우 교환처리상태가 필요없어서 주석처리 됨을 rowspan 값을 2로 설정해줌 2013-07-07 이학봉
}else{
    $rows_cnt = '4';
}

if(!$title_str){
    $title_str  = "주문리스트";
}

if($max == ""){
    $max = 15; //페이지당 갯수
}

if($page == ''){
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
if($view_type == 'sc_order'){
    $where = "WHERE od.status !='SR' AND od.product_type IN (".implode(',',$sns_product_type).") ";
    $folder_name = "sns";
}else{
    $where = "WHERE od.status !='SR' ";
    $folder_name = "product";
}

if($mode != "search"){
    $orderdate = 1;
}

if(!$date_type){
    $date_type = "o.order_date";
}

if($orderdate){
    //$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
    $where .= "and ".$date_type." between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}

if($mmode == "personalization" && $mem_ix != ""){
    $where .= " and o.user_code = '".$mem_ix."' ";
}

if(is_array($type)){
    for($i = 0; $i < count($type); $i++){
        if($type[$i] && $type[$i] == "IC"){
            if($o_type_str == ""){
                $o_type_str .= "'".$type[$i]."'";
            }else{
                $o_type_str .= ", '".$type[$i]."' ";
            }
        }else if($type[$i] && $type[$i] != "IC"){
            if($type_str == ""){
                $type_str .= "'".$type[$i]."'";
            }else{
                $type_str .= ", '".$type[$i]."' ";
            }
        }
    }

    if($type_str != "" && $o_type_str != ""){
        $where .= "and (o.status in ($o_type_str) or od.status in ($type_str))";
    }else if($type_str != ""){
        $where .= "and od.status in ($type_str) ";
    }else if($o_type_str != ""){
        $where .= "and o.status in ($o_type_str) ";
    }
}else{
    if($type && $type == "IC"){
        $where .= "and o.status = '$type' ";
    }else if($type && $type != "IC"){
        $where .= "and od.status = '$type' ";
    }
}

if(is_array($refund_type)){
    for($i = 0; $i < count($refund_type); $i++){
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

$left_join = "";
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
    }else if($search_type == "combi_cooid"){
        $where .= "and (od.co_oid = '".trim($search_text)."'  or od.co_od_ix = '".trim($search_text)."') ";
    }else if($search_type == "combi_email"){
        $where .= "and (bmail LIKE '%".trim($search_text)."%'  or odd.rmail LIKE '%".trim($search_text)."%') ";
    }else if($search_type == "combi_tel"){
        $where .= "and (REPLACE(btel,'-','') LIKE '%".trim($search_text)."%'  or REPLACE(odd.rtel,'-','') LIKE '%".trim($search_text)."%' or btel LIKE '%".trim($search_text)."%' or odd.rtel LIKE '%".trim($search_text)."%') ";
    }else if($search_type == "combi_mobile"){
        $where .= "and ( REPLACE(bmobile,'-','') LIKE '%".trim($search_text)."%'  or REPLACE(odd.rmobile,'-','') LIKE '%".trim($search_text)."%' or bmobile LIKE '%".trim($search_text)."%'  or odd.rmobile LIKE '%".trim($search_text)."%' ) ";
    }else{
        if($search_type == "o.bmobile"||$search_type == "odd.rmobile"){
            $where .= "and ( REPLACE(".$search_type.",'-','') LIKE '%".trim($search_text)."%' or ".$search_type." LIKE '%".trim($search_text)."%' ) ";
        }else{
            $where .= "and $search_type LIKE '%".trim($search_text)."%' ";
        }
    }
    if($search_type == "combi_name" || $search_type == "combi_email" || $search_type == "combi_tel" || $search_type == "combi_mobile" || substr_count($search_type,'odd.') > 0){
        $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
    }
}

if(is_array($order_from)){
    for($i = 0; $i < count($order_from); $i++){
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
    for($i = 0; $i < count($payment_agent_type); $i++){
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
    for($i = 0; $i < count($delivery_status); $i++){
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

if(is_array($p_admin) && count($p_admin) == 1){
    if($p_admin[0] == "A"){
        $where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
    }else if($p_admin[0] == "S"){
        $where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
    }
}else{
    if($p_admin == "A"){
        $where .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
    }else if($p_admin == "S"){
        $where .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
    }
}

if($stock_use_yn != ""){
    $where .= "and od.stock_use_yn = '".$stock_use_yn."'";
}

if($mall_ix != ""){
    $where .= "and od.mall_ix = '".$mall_ix."'";
}

if($gp_ix != ""){
    $where .= "and o.gp_ix = '".$gp_ix."'";
}



//echo $sql;
//var_dump($order_from_str);


$Contents = "
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "주문관리 > $title_str ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<form name='search_frm' method='get' action=''>
			<input type='hidden' name='mmode' value='$mmode' />
			<input type='hidden' name='mem_ix' value='$mem_ix' />
			<input type='hidden' name='mode' value='search' />
			<table width=100%  border=0>
				<!--tr height=25>
					<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
				</tr-->
				<tr>
					<td align='left' colspan=2  width='100%' valign=top style='padding-top:5px;'>
					<table height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
						<tr>
							<td bgColor=#ffffff style='padding:0 0 3px 0;height:120px;'>
								<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
									<col width=5%>
									<col width=10%>
									<col width=35%>
									<col width=15%>
									<col width=35%>";
if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents .= "
                                    <tr>
                                        <td class='search_box_title' colspan='2'> 프론트 전시 구분</td>
                                        <td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
                                    </tr>";
}

$Contents .= "";

if($admininfo["admin_level"] == 9){
    $Contents .= "
                                    <tr height=30>
                                        <th class='search_box_title' colspan='2'>판매처 선택 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
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
                                                <tr height=25>";

    if($view_type == 'offline_order'){		//영업관리용도
        $Contents .= "                              <td colspan='8'><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." checked><label for='order_from_offline'>통합구매</label></td>";
    }else if($view_type == 'pos_order'){		//포스관리용도
        $Contents .= "                              <td colspan='8'><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." checked><label for='order_from_pos'>POS</label></td>";
    }else{
        $Contents .= "
                                                    <td><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></td>";
        $slave_db->query("select * from sellertool_site_info where disp='1' ");
        $sell_order_from = $slave_db->fetchall();
        if(count($sell_order_from) > 0){
            for($i = 0; $i < count($sell_order_from); $i++){
                if($i == 5 || ($i > 5 && $i%8 == 5)){
                    $Contents .= "              </tr>
                                                <tr>";
                }
                $Contents .= "                  <td><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></td>";
            }
            if(count($sell_order_from) < 5){
                for($j = 0; $j < 5 - count($sell_order_from); $j++){
                    $Contents .= "<td></td>";
                }
            }
        }else{
            $Contents .= "
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>";
        }
    }

    $Contents .= "
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>";
}

if($mmode != "personalization"){
    $Contents .= "
                                    <tr>
                                        <th class='search_box_title' rowspan='2' >결제 </th>
                                        <th class='search_box_title' >결제상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
                                        <td class='search_box_item' colspan='3'>
                                            <table cellpadding=0 cellspacing=0 width='100%' border='0' >
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <tr height=25>
                                                    <td><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_READY."'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></td>
                                                    <td><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></td>";

    if($_SESSION['admininfo']['admin_level'] != '8'){
        $Contents .= "                              <td><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label></td>";
    }

    $Contents .= "                                  <td><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></td>";

    if($view_type == 'pos_order'){
        $Contents .= "
													<td></td>
													<td></td>
													<td></td>
													<td></td>";
    }else{
        if($_SESSION["admininfo"]["admin_level"] == 9){
            $Contents .= "
													<td></td>
													<td></td>
													<td></td>
													<td></td>";
        }else{
            $Contents .= "
													<td></td>
													<td></td>
													<td></td>
													<td></td>";
        }
    }

    $Contents .= "
												</tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class='search_box_title' >환불상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
                                        <td class='search_box_item' colspan='3'>
                                            <table cellpadding=0 cellspacing=0 width='100%' border='0' >
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <col width='12.5%'>
                                                <tr height=25>
                                                    <td><input type='checkbox' name='refund_type[]' id='refund_type_".ORDER_STATUS_REFUND_APPLY."' value='".ORDER_STATUS_REFUND_APPLY."' ".CompareReturnValue(ORDER_STATUS_REFUND_APPLY,$refund_type,' checked')." ><label for='refund_type_".ORDER_STATUS_REFUND_APPLY."'>".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</label></td>
                                                    <td><input type='checkbox' name='refund_type[]'  id='refund_type_".ORDER_STATUS_REFUND_COMPLETE."' value='".ORDER_STATUS_REFUND_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_REFUND_COMPLETE,$refund_type,' checked')." ><label for='refund_type_".ORDER_STATUS_REFUND_COMPLETE."'>".getOrderStatus(ORDER_STATUS_REFUND_COMPLETE)."</label></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class='search_box_title' rowspan='".$rows_cnt."' style='padding-right:10px;' nowrap>처리상태</th>
                                        <th class='search_box_title' style='padding-right:10px;' nowrap>배송처리상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
                                        <td class='search_box_item' colspan=3>
                                        <table cellpadding=0 cellspacing=0 width='100%' border='0' >
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <col width='12.5%'>
                                            <tr height=25>";
    if($view_type == 'pos_order'){
        $Contents .= "
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$type,' checked')." ><label for='type_".ORDER_STATUS_BUY_FINALIZED."'>".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label></TD>
														<TD ></TD>
														<TD ></TD>
														<TD ></TD>
														<TD ></TD>
														<TD></TD>
														<TD></TD>";
    }else{

        $Contents .= "
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_READY."' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_CANCEL_APPLY."' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_".ORDER_STATUS_CANCEL_APPLY."'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_DELAY."' value='".ORDER_STATUS_DELIVERY_DELAY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_DELAY,$type,' checked')."><label for='type_".ORDER_STATUS_DELIVERY_DELAY."'>".getOrderStatus(ORDER_STATUS_DELIVERY_DELAY)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_ING."'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_DELIVERY_COMPLETE."' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_BUY_FINALIZED."' value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$type,' checked')." ><label for='type_".ORDER_STATUS_BUY_FINALIZED."'>".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</label></TD>
														<TD></TD>
														<TD></TD>";
    }
    $Contents .= "
													</TR>
												</TABLE>
												</td>
											</tr>";

    if($view_type != 'pos_order'){
        $Contents .= "
											<tr>
												<th class='search_box_title'>출고처리상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<TR height=25>
														<TD >
														<input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY,$delivery_status,' checked')." >
														<label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY)."</label>
														</TD>
														<TD >
															<input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM,$delivery_status,' checked')." >
															<label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM)."</label>
														</TD>
														<TD >
															<input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING,$delivery_status,' checked')." >
															<label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING)."</label>
														</TD>
														<TD >
															<input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_READY,$delivery_status,' checked')." >
															<label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)."</label>
														</TD>
														<TD >
															<input type='checkbox' name='delivery_status[]' id='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE,$delivery_status,' checked')." >
															<label for='type_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'>".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE)."</label>
														</TD>
														<TD ></TD>
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
    }


    if($view_type != 'pos_order' && $view_type != 'sc_order'){
        $Contents .= "
											<tr>
												<th class='search_box_title' >교환처리상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<TR height=25>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_DENY."' value='".ORDER_STATUS_EXCHANGE_DENY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DENY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DENY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DENY)."</label></TD>
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
														<TD ><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_DELIVERY."' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DELIVERY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_ACCEPT."' value='".ORDER_STATUS_EXCHANGE_ACCEPT."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ACCEPT,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_ACCEPT."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ACCEPT)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_DEFER."' value='".ORDER_STATUS_EXCHANGE_DEFER."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DEFER,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_DEFER."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DEFER)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' value='".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_IMPOSSIBLE,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_IMPOSSIBLE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>
													</TR>
												</TABLE>
												</td>
											</tr>";
    }
    $Contents .= "
											<tr>
												<th class='search_box_title' >반품처리상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
													<col width='12.5%'>
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
														<TD><input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_IMPOSSIBLE."' value='".ORDER_STATUS_RETURN_IMPOSSIBLE."' ".CompareReturnValue(ORDER_STATUS_RETURN_IMPOSSIBLE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_IMPOSSIBLE."'>".getOrderStatus(ORDER_STATUS_RETURN_IMPOSSIBLE)."</label></TD>
														<TD><input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
													</TR>";
    }

    $Contents .= "
												</TABLE>
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>결제방법 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' nowrap>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'>".getMethodStatus(ORDER_METHOD_BANK)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'>".getMethodStatus(ORDER_METHOD_CARD)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'>".getMethodStatus(ORDER_METHOD_VBANK)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'>".getMethodStatus(ORDER_METHOD_ICHE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle' --></label><br/>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'>".getMethodStatus(ORDER_METHOD_PHONE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'>".getMethodStatus(ORDER_METHOD_CASH)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle' --></label>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'>".getMethodStatus(ORDER_METHOD_SAVEPRICE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_RESERVE."' value='".ORDER_METHOD_RESERVE."' ".CompareReturnValue(ORDER_METHOD_RESERVE,$method,' checked')." ><label for='method_".ORDER_METHOD_RESERVE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_RESERVE)."'>".getMethodStatus(ORDER_METHOD_RESERVE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_RESERVE.".gif' align='absmiddle' --></label>
													
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PAYCO."' value='".ORDER_METHOD_PAYCO."' ".CompareReturnValue(ORDER_METHOD_PAYCO,$method,' checked')." ><label for='method_".ORDER_METHOD_PAYCO."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PAYCO)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PAYCO.".gif' align='absmiddle'></label -->
												</td>
												<th class='search_box_title' >결제형태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item'>
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')." ><label for='payment_agent_type_W' class='helpcloud' help_width='90' help_height='15' help_html='PC(웹)결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_w.gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')." ><label for='payment_agent_type_M' class='helpcloud' help_width='80' help_height='15' help_html='모바일결제'><img src='../images/".$admininfo[language]."/s_payment_agent_type_m.gif' align='absmiddle'></label>
												</td>
											</tr>
											";
}

if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
    $Contents .= "
											<tr height=30>
												<th class='search_box_title' colspan='2'>셀러명 </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												<th class='search_box_title'>담당MD </th>
												<td class='search_box_item'>".MDSelect($md_code)."</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>상품관리구분 </th>
												<td class='search_box_item'>";

    if($view_type != 'inventory'){
        $Contents .= "
													<input type='checkbox' name='p_admin[]' id='p_admin_a' value='A' ".CompareReturnValue("A",$p_admin,' checked')." ><label for='p_admin_a'>본사상품</label>&nbsp;
													<input type='checkbox' name='p_admin[]' id='p_admin_s' value='S' ".CompareReturnValue("S",$p_admin,' checked')." ><label for='p_admin_s'>셀러상품</label>&nbsp;
													";
    }

    $Contents .= "
													<input type='checkbox' name='stock_use_yn' id='stock_use_y' value='Y' ".CompareReturnValue("Y",$stock_use_yn,' checked')." ><label for='stock_use_y'>WMS상품</label>&nbsp;";

    $Contents .= "
												</td>
												<td class='search_box_title' >회원그룹 </td>
                                          <td class='search_box_item' >
                                            ".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
                                          </td>
											</tr>";
}

$Contents .= "
											<tr height=33>
												<th class='search_box_title' colspan='2' style='padding-left:5px;'>
													<select name='date_type' style='width:80px;'>
														<option value='o.order_date' ".CompareReturnValue('o.order_date',$date_type,' selected').">주문일자</option>
														<option value='od.ic_date' ".CompareReturnValue('od.ic_date',$date_type,' selected').">입금일자</option>
														<option value='od.dr_date' ".CompareReturnValue('od.dr_date',$date_type,' selected').">배송준비중일자</option>
														<option value='od.dc_date' ".CompareReturnValue('od.dc_date',$date_type,' selected').">배송완료일자</option>
														<option value='od.bf_date' ".CompareReturnValue('od.bf_date',$date_type,' selected').">거래완료일</option>
													</select>
													<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked').">
												</th>
												<td class='search_box_item'  colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>조건검색 </th>
												<td class='search_box_item' style='text-align:left;'>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<tr>
														<td  >
														<select name='search_type' style='font-size:12px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자&수취인-명</option>
															<option value='combi_email' ".CompareReturnValue('combi_email',$search_type,' selected').">주문자&수취인-Email</option>
															<option value='combi_tel' ".CompareReturnValue('combi_tel',$search_type,' selected').">주문자&수취인-전화번호</option>
															<option value='combi_mobile' ".CompareReturnValue('combi_mobile',$search_type,' selected').">주문자&수취인-핸드폰번호</option>
															<option value='o.bname' ".CompareReturnValue('o.bname',$search_type,' selected').">주문자명</option>
															<option value='o.buserid' ".CompareReturnValue('o.buserid',$search_type,' selected').">주문자ID</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='combi_cooid' ".CompareReturnValue('combi_cooid',$search_type,' selected').">제휴사주문번호</option>
															<option value='odd.rname' ".CompareReturnValue('odd.rname',$search_type,' selected').">수취인명</option>
															<option value='o.bmobile' ".CompareReturnValue('o.bmobile',$search_type,' selected').">주문자핸드폰</option>
															<option value='odd.rmobile' ".CompareReturnValue('odd.rmobile',$search_type,' selected').">수취인핸드폰</option>
															<option value='od.invoice_no' ".CompareReturnValue('od.invoice_no',$search_type,' selected').">송장번호</option>
															<option value='od.pname' ".CompareReturnValue('od.pname',$search_type,' selected').">상품명</option>
															<option value='od.option_text' ".CompareReturnValue('od.option_text',$search_type,' selected').">옵션명</option>
															<option value='od.gid' ".CompareReturnValue('od.gid',$search_type,' selected').">품목코드</option>
															<option value='od.gu_ix' ".CompareReturnValue('od.gu_ix',$search_type,' selected').">품목시스템코드</option>
															<option value='od.pid' ".CompareReturnValue('od.pid',$search_type,' selected').">상품시스템코드</option>
														</select>
														</td>
														<td  ><input type='text' class=textbox name='search_text' size='30' value='$search_text' style='' ></td>
													</tr>
													</table>
												</td>
												<th class='search_box_title'>목록수 </th>
												<td class='search_box_item'>
													<select name='max' style='font-size:12px;'>
														<option value='5' ".CompareReturnValue('5',$max,' selected').">5</option>
														<option value='10' ".CompareReturnValue('10',$max,' selected').">10</option>
														<option value='15' ".CompareReturnValue('15',$max,' selected').">15</option>
														<option value='20' ".CompareReturnValue('20',$max,' selected').">20</option>
														<option value='50' ".CompareReturnValue('50',$max,' selected').">50</option>
														<option value='100' ".CompareReturnValue('100',$max,' selected').">100</option>
													</select> 한페이지에 보여질 갯수를 선택해주세요
												</td>
											</tr>
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
		<td colspan=2 align=center style='padding:10px 0px;position:relative;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
</table>
</form>
<!--a name='list_top'></a-->
<form name=listform method=post action='../order/orders.batch.act.php' onsubmit='return BatchSubmit(this)' target='act'>
<input type='hidden' name='mmode' value='$mmode' />
<input type='hidden' name='mem_ix' value='$mem_ix' />
<input type='hidden' name='page' value='$page' />
<input type='hidden' id='oid' value='' />
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."' />
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
}else{
    $where .= " and o.oid = od.oid ";
}

if($view_type == 'offline_order'){		//영업관리 용도 2013-07-05 이학봉
    $where .= " and od.order_from in ('offline') ";
}else if($view_type == 'pos_order'){		//포스관리 용도 2013-07-05 이학봉
    $where .= " and od.order_from in ('pos') ";
}

if($mode == "search"){
    $sql = "SELECT distinct o.oid
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				$left_join
				$where ";

    $slave_db->query($sql);
    $total = $slave_db->total;

    $sql = "SELECT sum(pt_dcprice) as product_price
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		$left_join
		$where ";
    $slave_db->query($sql);
    $prder_price = $slave_db->fetch();


    $sql = "SELECT distinct o.oid
			FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
			$left_join
			$where
			ORDER BY o.order_date DESC LIMIT $start, $max";
    $slave_db->query($sql);
    $oid_array = $slave_db->fetchall("object");

    if(is_array($oid_array)){
        foreach($oid_array as $info){
            $o_array[]=$info[oid];
        }
    }
}

$Contents .= "<td colspan=3 align=left><b class=blk>전체 주문수 : $total 건</b> ".($_SESSION["admininfo"]["admin_level"] > 8 ? "<!--총주문금액 : ".number_format($prder_price[total_price])." 원--> 상품금액 : ".number_format($prder_price[product_price])." 원 <!--배송금액 : ".number_format($prder_price[delivery_price])." 원-->" : "")."</td><td colspan=10 align=right>";

if($admininfo[admin_level] != 9){
    $Contents .= "<span style='color:red'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
}

$Contents .= orderExcelTemplateSelect("O");

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다..'>
	<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"if(jQuery('#oet_ix').val().length > 0){location.href='../order/orders_excel2003.php?oet_ix='+jQuery('#oet_ix').val()+'&view_type=$view_type&".$QUERY_STRING."'}else{alert('엑셀양식을선택해주세요.');}\" ></span>";

}else{
    $Contents .= "
	<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
}

$Contents .= "
		</td>
	</tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='border-bottem:0px;'>
	<col width='30px'/>
	".($admininfo[admin_level]==9 ? "<col width='8%'/>" : "" )."
	<col width='13%'/>
	<col width='*'/>
	<col width='11%'/>
	<col width='11%'/>
	<col width='11%'/>
	<col width='8%'/>
	".($admininfo[admin_level]==9 ? "<col width='11%'/>" : "" )."
	<col width='6%'/>
		<tr height='35' >
		<td align='center' class='s_td' style='background-color:#fff7da;' ><input type=checkbox  name='all_fix' onclick='fixAll2(document.listform);input_check_num();'></td>
		".($admininfo[admin_level]==9 ? "<td align='center' class='m_td' style='background-color:#fff7da;' ><font color='#000000' class=small><b>판매처<br>기여사이트</b></font></td>" : "" )."
		<td align='center'  class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>주문일</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>주문번호</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap><font color='#000000' class=small><b>주문자/수취인</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>결제방법/구분</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>입금상태(입금일)</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>영수증</b></font></td>
		".($admininfo[admin_level]==9 ? "<td align='center' class='m_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>총 결제금액</b></font></td>" : "" )."
		<td align='center' class='e_td' style='background-color:#fff7da;'  nowrap><font color='#000000' class=small><b>관리</b></font> <img src='../images/icon/sub_title_dot.gif' onclick=\"$('.order_list_tr').toggle();\" align='absmiddle' style='cursor:pointer;padding-left:5px;' /></td>
	</tr>
	</table>
	 <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='*%' align='center' class='m_td' ><font color='#000000' class=small ><b>주문상세번호/상품명/옵션</b></font></td>
			<td width='8%' align='center' class='m_td'  ><font color='#000000' class=small ><b>정가<br/>/판매가(할인가)</b></font></td>
			<td width='8%' align='center' class='m_td'  ><font color='#000000' class=small ><b>할인금액</b></font></td>
			<td width='8%' align='center' class='m_td'  ><font color='#000000' class=small ><b>결제금액<br/>/적립금(수량)</b></font></td>
			<td width='10%' align='center' class='m_td'  ><font color='#000000' class=small ><b>배송방법<br/>/배송비</b></font></td>
			<td width='7%' align='center' class='m_td'  ><font color='#000000' class=small ><b>재고/진행/부족</b></font></td>
			<td width='7%' align='center' class='m_td'  ><font color='#000000' class=small ><b>처리상태</b></font></td>
			".($admininfo[admin_level]==9 ? "<td width='7%' align='center' class='m_td'  ><font color='#000000' class=small ><b>출고처리상태</b></font></td>" : "" )."
			<td width='8%' align='center' class='m_td'  ><font color='#000000' class=small ><b>정산예정금액</b></font></td>
		</tr>";


if(count($o_array)>0){

    $addWhere=" AND od.status !='SR' ";

    if($admininfo[admin_level] == 9){
        if($admininfo[mem_type] == "MD"){
            $addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
        }
    }else{
        $addWhere .= " and od.company_id ='".$admininfo[company_id]."' ";
    }

    if($search_type && $search_text){
        if($search_type == "od.pname" || $search_type == "od.invoice_no" || $search_type == "od.option_text"){
            $addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
        }
    }

    if(is_array($p_admin) && count($p_admin) == 1){
        if($p_admin[0]=="A"){
            $addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
        }else if($p_admin[0]=="S"){
            $addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
        }
    }else{
        if($p_admin=="A"){
            $addWhere .= "and od.company_id ='".$HEAD_OFFICE_CODE."' ";
        }else if($p_admin=="S"){
            $addWhere .= "and od.company_id !='".$HEAD_OFFICE_CODE."' ";
        }
    }

    if($stock_use_yn != ""){
        $addWhere .= "and od.stock_use_yn = '".$stock_use_yn."'";
    }

    $sql = "SELECT 
		o.oid, o.delivery_box_no, o.payment_price, 
		o.payment_agent_type, o.user_code as user_id,
		o.buserid, o.bmobile, o.bname, o.gp_ix,
		o.mem_group, o.status as ostatus, 
		o.order_date as regdate, 
		o.total_price,od.ode_ix,
		AES_DECRYPT(UNHEX(refund_bank),'".$db->ase_encrypt_key."') as refund_bank1, 
		AES_DECRYPT(UNHEX(refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_name1,
		od.od_ix, od.product_type, od.pid, od.brand_name, od.pname, 
		od.set_name, od.sub_pname, od.option_text, od.coprice,
		od.listprice,od.psprice, od.pcnt, od.ptprice, od.pt_dcprice,
		od.delivery_status, od.refund_status, od.stock_use_yn, od.order_from, 
		od.pcode, od.admin_message, od.status,od.option_kind,
		od.company_name, od.company_id, od.reserve, od.co_pid,od.co_oid,od.co_od_ix,
		date_format(od.ic_date,'%Y-%m-%d') as incom_date,
		od.real_lack_stock,od.rfid, 

		od.delivery_type,od.delivery_policy,od.delivery_package,od.delivery_method,od.delivery_pay_method,od.ori_company_id,od.delivery_addr_use,od.factory_info_addr_ix,

		(select IFNULL(delivery_dcprice,'0') as delivery_dcprice 
			from 
				shop_order_delivery 
			where
				ode_ix=od.ode_ix
		) as delivery_totalprice,
		
		(select regdate from shop_order_status where oid=od.oid and (case when od.status='IC' then status='IC' else (od_ix=od.od_ix and status=od.status) end) order by regdate desc limit 1) as status_regdate,
		(select regdate from shop_order_status where oid=od.oid and od_ix=od.od_ix and status=od.refund_status order by regdate desc limit 1) as refund_status_regdate,
		
		(case when od.stock_use_yn ='Y' then
			(select sum(stock) from inventory_product_stockinfo ps where ps.gid=gu.gid and ps.unit=gu.unit)
		else
			(case when (od.stock_use_yn ='Y' and (od.option_kind = 'x2' or od.option_kind = 'b' or od.option_kind = 'x' or od.option_kind = 's2' or od.option_kind = 'c')) or od.order_from != 'self' then pod.option_stock else p.stock end)
		end) as stock,
		
		(case when od.stock_use_yn ='Y' then
			gu.sell_ing_cnt
		else
			(case when (od.stock_use_yn ='Y' and (od.option_kind = 'x2' or od.option_kind = 'b' or od.option_kind = 'x' or od.option_kind = 's2' or od.option_kind = 'c')) or od.order_from != 'self' then pod.option_sell_ing_cnt else p.sell_ing_cnt end)
		end) as sell_ing_cnt,

		(case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then (od.ptprice -(od.ptprice*(od.commission)/100)) else ((od.coprice*od.pcnt)-(od.coprice*od.pcnt*(od.commission)/100)) end) end) as expect_ac_price

	FROM 
		".TBL_SHOP_ORDER." o,
		".TBL_SHOP_ORDER_DETAIL." od 
		left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod on (od.option_id=pod.id)
		left join ".TBL_SHOP_PRODUCT." p on (p.id=od.pid)
		left join inventory_goods_unit gu on (gu.gu_ix=od.gu_ix)
	where
		o.oid = od.oid 
		and o.oid in ('".implode("','",$o_array)."')
		$addWhere
		ORDER BY o.oid desc, od.ode_ix ASC, od.pid ASC, od.set_group asc";

    $slave_db->query($sql);
    $order_list=$slave_db->fetchall("object");

    for ($i = 0; $i < count($order_list); $i++){

        //주문삭제 버튼
        //if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
        if($order_list[$i][status] == ORDER_STATUS_DELIVERY_COMPLETE)		{
            $delete = "<a href=\"javascript:alert(language_data['orders.list.php']['A'][language]);\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";//[처리완료] 기록은 삭제할 수 없습니다.
        }else if($order_list[$i][status] != ORDER_STATUS_CANCEL_COMPLETE && $order_list[$i][method] == "1"){
            $delete = "<a href=\"javascript:order_delete('delete','".$order_list[$i][oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";
        }else{
            $delete = "<a href=\"javascript:act('delete','".$order_list[$i][oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:2px;'></a>";
        }
        //}

        $bcompany_id = '';

        $delivery_pay_type = getDeliveryPayType($order_list[$i][delivery_pay_method]);		//배송비 결제수단 텍스트 리턴
        $delivery_method = getDeliveryMethod($order_list[$i][delivery_method]);			//배송방법 텍스트 리턴

        $one_status = getOrderStatus($order_list[$i][status]).($order_list[$i][admin_message]!="" ? "<br><b class='grn'>".$order_list[$i][admin_message]."</b>":"")."<br>".str_replace(' ','<br/>',$order_list[$i][status_regdate]).($order_list[$i][refund_status] ? "<br/>".getOrderStatus($order_list[$i][refund_status])."<br>".str_replace(' ','<br/>',$order_list[$i][refund_status_regdate]) : "");

        $sql="select * from shop_order_detail_discount where od_ix='".$order_list[$i][od_ix]."' ";
        $slave_db->query($sql);
        if($slave_db->total){
            $dc_info = $slave_db->fetchall("object");
        }else{
            $dc_info = "";
        }

        $dc_coupon_info = getOrderDetailCouponDcInfo($dc_info);
        $dc_coupon_str=$dc_coupon_info["coupon_str"];
        $dc_coupon_width=$dc_coupon_info["coupon_width"];
        $dc_coupon_height=$dc_coupon_info["coupon_height"];

        $dc_etc_info = getOrderDetailEtcDcInfo($dc_info);
        $dc_etc_str=$dc_etc_info["etc_str"];
        $dc_etc_width=$dc_etc_info["etc_width"];
        $dc_etc_height=$dc_etc_info["etc_height"];

        $discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($order_list[$i][ptprice]-$order_list[$i][pt_dcprice]).$currency_display[$admin_config["currency_unit"]]["back"];

        if($dc_etc_str!=""){
            $discount_info.=" <label class='helpcloud' help_width='".$dc_etc_width."' help_height='".$dc_etc_height."' help_html='".$dc_etc_str."'><img src='../images/icon/q_icon.png' align=''></label>";
        }

        if($dc_coupon_str!=""){
            $discount_info.=" <label class='helpcloud' help_width='".$dc_coupon_width."' help_height='".$dc_coupon_height."' help_html='".$dc_coupon_str."'><img src='../images/".$admininfo[language]."/s_use_coupon.gif' align=''></label>";
        }

        if($order_list[$i][oid] != $b_oid){

            $method_info = getOrderMethodInfo($order_list[$i]);
            $method_=$method_info["method"];
            $method_str=$method_info["method_str"];
            $method_width=$method_info["method_width"];
            $method_height=$method_info["method_height"];
            $receipt_type_str=$method_info["receipt"];

            $method = "<label class='helpcloud' help_width='".$method_width."' help_height='".$method_height."' help_html='".$method_str."'>".getMethodStatus($method_,"img")."</label>";

            $u_etc_info=get_order_user_info($order_list[$i][user_id]);

            $b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$order_list[$i][buserid]."/".$order_list[$i][bname]."<br/>핸드폰 : ".$order_list[$i][bmobile]." <br/>회원그룹 : ".$order_list[$i][mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($order_list[$i][user_id],($order_list[$i][gp_ix]=='2' ? "<b class='red'>VIP</b>" : "").$order_list[$i][bname].( $order_list[$i][buserid] ? "(<span class='small'>".$order_list[$i][buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $order_list[$i][user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$order_list[$i][user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");


            $recipient_info=getOrderRecipientInfo($order_list[$i]);
            $recipient_=$recipient_info["recipient"];
            $recipient_str=$recipient_info["recipient_str"];
            $recipient_width=$recipient_info["recipient_width"];
            $recipient_height=$recipient_info["recipient_height"];

            $r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".$recipient_."</b>";

            $Contents .= "<tr>
								<td class='' style='background-color:#fff7da;height:30px;font-weight:bold;' class=blue colspan='".($admininfo[admin_level]==9 ? "10" : "9")."' >
									<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
										<col width='30px'/>
										".($admininfo[admin_level]==9 ? "<col width='9%'/>" : "" )."
										<col width='13%'/>
										<col width='*'/>
										<col width='11%'/>
										<col width='11%'/>
										<col width='11%'/>
										<col width='8%'/>
										".($admininfo[admin_level]==9 ? "<col width='11%'/>" : "" )."
										<col width='6%'/>
										<tr>
											<td align='center' style='background-color:#fff7da;'><input type=checkbox name='oid[]' id='oid' value='".$order_list[$i][oid]."' onclick='input_check_num()'><input type=hidden name='bstatus[".$order_list[$i][oid]."]' value='".$order_list[$i][ostatus]."'><input type='hidden' id='od_status_".str_replace("-","",$order_list[$i][oid])."'></td>";

            if($admininfo[admin_level]==9){
                $Contents .= "
												<td  align='center' style='background-color:#fff7da;line-height:150%;'>
												<font color='#000000' class=small><b>".getOrderFromName($order_list[$i][order_from])."</b></font><br>
												<font color='#000000' class=small><b>".($order_list[$i][rfid]?str_replace(array("전체 > ","검색엔진 > "),"",getRefererCategoryPath2($order_list[$i][rfid], 4)):'')."</b></font>
												
												</td>";
            }

            $Contents .= "
											<td  align='center'  style='background-color:#fff7da;'><font color='orange' class=small><b>".$order_list[$i][regdate]."</b></font></td>
											<td  align='center'  style='background-color:#fff7da;'>
												<font color='blue' class=small><b>
													<span style='color:#007DB7;font-weight:bold;' class='small'>".$order_list[$i][oid]."</span></b>".($order_list[$i][delivery_box_no] ? "<b style='color:red;'>-".$order_list[$i][delivery_box_no]."</b>":"")."
												</font> 
												<span class='helpcloud' help_width='55' help_height='15' help_html='주문서'>
													<img src='../images/icon/paper.gif' style='cursor:pointer' align='absmiddle' onclick=\"PopSWindow('../order/orders.read.php?oid=".$order_list[$i][oid]."&pid=".$order_list[$i][pid]."&mmode=pop',960,600)\"/>
												</span>
											</td>
											<td  align='center'  style='background-color:#fff7da;'>
												<font color='#000000' class=small>".$b_mem_info." / ".$r_mem_info."</font>
											</td>
											<td  align='center'  style='background-color:#fff7da;'nowrap>
												<font color='#000000' class=small><b>".$method." / ".getPaymentAgentType($order_list[$i][payment_agent_type],'img')."</b></font>
											</td>
											<td  align='center' style='background-color:#fff7da;'>
												<font color='' class=small><b>".getOrderStatus($order_list[$i][ostatus]).(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") && $order_list[$i][ostatus] =='IR' && $admininfo[admin_level]==9 && false ? " <img src='../images/".$admininfo["language"]."/btn_incom_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$order_list[$i][oid]."','', 'IR', '".ORDER_STATUS_INCOM_COMPLETE."')\" style='cursor:pointer;' >":"").($order_list[$i][ostatus] =='IC' && $order_list[$i][incom_date] ? "<br/>(<span style='color:black'>".$order_list[$i][incom_date]."</span>)" : "")."</b></font>
											</td>
											<td  align='center'  style='background-color:#fff7da;'nowrap>
												<font color='#000000' class=small><b>".$receipt_type_str."</b></font>
											</td>";

            if($admininfo[admin_level]==9){
                $exchange_rate_payment_price = getOrderExchangeRatePaymentPrice($order_list[$i]);
                $Contents .= "<td  align='center'  style='background-color:#fff7da;'nowrap><font color='red' class=small><b>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($order_list[$i][total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." ".($exchange_rate_payment_price > 0 ? "<br/>(".number_format($exchange_rate_payment_price,2).")" : "")."</b></font></td>";
            }

            $Contents .= "
											<td  align='center'  style='background-color:#fff7da;'nowrap>";

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

                if($mmode == "personalization"){
                    $Contents .= "<a href=\"javascript:PopSWindow('../order/orders.edit.php?oid=".$order_list[$i][oid]."&pid=".$order_list[$i][pid]."&mmode=".$mmode."&mem_ix=".$mem_ix."',960,800);\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a> ";
                }else{
                    $Contents .= "<a href=\"../order/orders.edit.php?oid=".$order_list[$i][oid]."&pid=".$order_list[$i][pid]."&mmode=".$mmode."&mem_ix=".$mem_ix."\" ><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a> ";
                }

            }else{
                $Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a> ";
            }

            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $Contents .= $delete;
            }
            $Contents .= "					
												<img src='../images/icon/sub_title_dot.gif' onclick=\"$('.order_list_tr_".$order_list[$i][oid]."').toggle();\" align='absmiddle' style='cursor:pointer;' />
											</td>
										</tr>
									</table>
								</td>
							</tr>";
        }

        $Contents .= "
						<tr class='order_list_tr_".$order_list[$i][oid]." order_list_tr' style='display:none;'>
							<td >
								<TABLE>
									<TR>
										<TD align='center'>
											<a  href='/shop/goods_view.php?id=".$order_list[$i][pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."'  width=50 style='margin:5px;'></a><br/>";

        if($order_list[$i][product_type]=='21'||$order_list[$i][product_type]=='31'){
            $Contents .= "<label class='helpcloud' help_width='190' help_height='15' help_html='".($order_list[$i][product_type]=='21' ? "서브스크립션 커머스(배송상품)" : "로컬딜리버리 커머스(배송상품)")."'><img src='../images/".$admininfo[language]."/s_product_type_".$order_list[$i][product_type].".gif' align='absmiddle' ></label> ";
        }
        if($order_list[$i][company_id]==$HEAD_OFFICE_CODE){
            $Contents .= "<label class='helpcloud' help_width='70' help_height='15' help_html='본사상품'><img src='../images/".$admininfo[language]."/s_admin_product.gif' align='absmiddle' ></label> ";
        }
        if($order_list[$i][stock_use_yn]=='Y'){
            $Contents .= "<label class='helpcloud' help_width='140' help_height='15' help_html='(WMS)재고관리 상품'><img src='../images/".$admininfo[language]."/s_inventory_use.gif' align='absmiddle' ></label>";
        }

        $Contents .= "</TD>
										<td width='5'></td>
										<TD class=small style='line-height:140%'>";
        if($order_list[$i][order_from] == auction){
            $Contents .= "<span style='color:#007DB7;font-weight:bold;' class='small'>".$order_list[$i][od_ix]." ".($order_list[$i][co_od_ix] ? "(".$order_list[$i][co_od_ix].")" : "")."</span><br/>";
        }else{
            $Contents .= "<span style='color:#007DB7;font-weight:bold;' class='small'>".$order_list[$i][od_ix]." ".($order_list[$i][co_oid] ? "(".$order_list[$i][co_oid].")" : "")."</span><br/>";
        }
        if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){

            if($bcompany_id != $order_list[$i][company_id]){
                $seller_info_str= GET_SELLER_INFO($order_list[$i][company_id]);
            }

            $Contents .= "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='80' help_html='".$seller_info_str."'>".($order_list[$i][company_name] ? $order_list[$i][company_name]:"-")."</b> <img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../seller/seller_company.php?company_id=".$order_list[$i][company_id]."&mmode=pop',960,600,'brand');\"  style='cursor:pointer;'><br>";
        }
        if($order_list[$i][co_pid] != "" && $order_list[$i][co_pid] != "0000000000"){
            $Contents .= "<img src='../images/".$admininfo["language"]."/ico_wholesale.gif' border=0 align=absmiddle  title='도매주문'>  ";
        }

        $Contents .= "<a  href='../".$folder_name."/goods_input.php?id=".$order_list[$i][pid]."' target=_blank />";

        if($order_list[$i][product_type]=='99'||$order_list[$i][product_type]=='21'||$order_list[$i][product_type]=='31'){
            $Contents .= "<b class='".($order_list[$i][product_type]=='99' ? "red" : "blue")."' >[".$order_list[$i][brand_name]."] ".$order_list[$i][pname]."</b><br/><strong>".$order_list[$i][set_name]."<br/></strong>".$order_list[$i][sub_pname];
        }else{
            $Contents .= "[".$order_list[$i][brand_name]."] ".$order_list[$i][pname];
        }

        $Contents .= "</a>";

        if(strip_tags($order_list[$i][option_text])){
            $Contents .= "<br/> ▶ ".strip_tags($order_list[$i][option_text]);
        }

        $Contents .="
										</TD>
									</TR>
								</TABLE>
							</td>
							<td class='' align=center>
								".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][listprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>
								/".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][psprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."
							</td>
							<td class='' align=center>".$discount_info."</td>
							<td class='' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][pt_dcprice])."".$currency_display[$admin_config["currency_unit"]]["back"]." <br/> ".number_format($order_list[$i][reserve])."P (".number_format($order_list[$i][pcnt])."개)</td>";


        //배송비 분리 시작 2014-05-21 이학봉
        if($bori_ode_ix != $order_list[$i][ode_ix]){
            $sql = "SELECT
                            COUNT(DISTINCT(od.od_ix)) AS com_cnt
                        FROM 
                            ".TBL_SHOP_ORDER." o,
                            ".TBL_SHOP_ORDER_DETAIL." od
                        where 
                            o.oid = od.oid 
                            and o.oid = '".$order_list[$i][oid]."' 
                            and od.ode_ix = '".$order_list[$i][ode_ix]."'
                            $addWhere 
                            ";

            $slave_db->query($sql);//$slave_db는 상단에서 선언
            $slave_db->fetch();
            $com_cnt=$slave_db->dt["com_cnt"];

            $Contents .="<td class='' align=center style='line-height:140%;' rowspan='".$com_cnt."'>
                        ".number_format($order_list[$i][delivery_totalprice])."원
                    </td>";
        }   
        //배송비 분리 끝 2014-05-21 이학봉

        $Contents .="
							<td class='' align=center>
								".number_format($order_list[$i][stock])."/-".number_format($order_list[$i][sell_ing_cnt])."/".($order_list[$i][stock]-$order_list[$i][sell_ing_cnt] < 0 ? "<b class='red'>".number_format($order_list[$i][stock]-$order_list[$i][sell_ing_cnt])."</b>" : "-0")."";
        if($order_list[$i][stock_use_yn]=='Y'){
            $Contents .="<br/>";

            if($order_list[$i][real_lack_stock] < 0){
                $Contents .="<b class='red'>".$order_list[$i][real_lack_stock]."</b> <img src='../images/icon/alarm_danger.gif' align='absmiddle'>";
            }else{
                $Contents .="<b class='grn'>".$order_list[$i][real_lack_stock]."</b>";
            }
        }
        $Contents .="
							</td>
							<td class='point' align='center'>".$one_status."</td>";

        if($admininfo[admin_level]==9){
            $Contents .= "<td class='point' align='center'>".getOrderStatus($order_list[$i][delivery_status])."</td>";
        }

        $Contents .= "
							<td class='' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][expect_ac_price])."".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
						</tr>";

        $b_oid = $order_list[$i][oid];
        $bcompany_id = $order_list[$i][company_id];
        $bproduct_id = $order_list[$i][pid];
        $bset_group = $order_list[$i][set_group];
        $bori_ode_ix = $order_list[$i][ode_ix];


    }
}else{
    if($mode=="search"){
        $Contents .= "<tr height=50><td colspan='".($_SESSION["admininfo"]["admin_level"]==9 ? "9" : "8")."' align=center>조회된 결과가 없습니다.</td></tr>";
    }else{
        $Contents .= "<tr height=50><td colspan='".($_SESSION["admininfo"]["admin_level"]==9 ? "9" : "8")."' align=center>조회하시고자 하는 검색조건을 선택후 검색해주세요.</td></tr>";
    }
}

if($QUERY_STRING == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</table>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
<tr height=40>
	<td>";

if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
    $Contents .= "<a href='JavaScript:SelectDelete(document.forms[\"listform\"]);'><img src='../images/korea/bt_all_del.gif' border='0' align='absmiddle'></a>";
}

$Contents .= "
	</td>
	<td colspan='13' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
</tr>
</table>";


$Script="
<script type='text/javascript'>
<!--

	function BatchSubmit(frm){

		if(frm.update_type.value == 1 && '".$_GET[mode]."' != 'search'){
			alert('적용대상중 [검색주문전체]는 검색후 사용 가능합니다. 확인후 다시 시도해주세요');
			return false;
		}

		if($('#update_kind_sms').attr('checked')){
			if(frm.sms_text.value.length < 1){
				alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
				frm.sms_text.focus();
				return false;
			}
		}else if($('#update_kind_sendemail').attr('checked')){

			if(frm.email_subject.value.length < 1){
				alert(language_data['member_batch.php']['F'][language]);//'이메일 제목을 입력해주세요'
				frm.email_subject.focus();
				return false;
			}

			/*
			if(frm.mail_content.value.length < 1 || frm.mail_content.value == '<P>&nbsp;</P>'){
				alert(language_data['member_batch.php']['G'][language]);//'이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요'
				//frm.mail_content.focus();
				return false;
			}
			*/
		}

		if(frm.update_type.value == 1){
			if($('#update_kind_sms').attr('checked')){
				if(!confirm('검색주문 전체에게 SMS 발송을 하시겠습니까?')){return false;}
			}else if($('#update_kind_sendemail').attr('checked')){
				if(!confirm('검색주문 전체에게 이메일 발송을 하시겠습니까?')){return false;}//'검색회원 전체에게 이메일발송을 하시겠습니까?'
			}
			//alert(frm.update_kind.value);
		}else if(frm.update_type.value == 2){
			var oid_checked_bool = false;
			for(i=0;i < frm.oid.length;i++){
				if(frm.oid[i].checked){
					oid_checked_bool = true;
				}
			}
			if(!oid_checked_bool){
				alert('선택하신 주문이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요.');
				return false;
			}
		}
	}

	function input_check_num() {
		/* 2014-07-06 SMS 이메일 발송 주석처리 해서 임시 주석처리! Hong
		var sms_cnt=document.getElementById('remainder_sms_cnt');
		var email_cnt=document.getElementById('remainder_email_cnt');
		var frm=document.listform;
		if(frm.update_type.value==2) {
			var oid_checked_num = 0;
			for(i=1;i < frm.oid.length;i++){
				if(frm.oid[i].checked){
					oid_checked_num++;
				}
			}
			sms_cnt.innerHTML=oid_checked_num;
			email_cnt.innerHTML=oid_checked_num;
		}
		*/
	}

	function view_order_num(sel,num) {
		var sms_cnt=document.getElementById('remainder_sms_cnt');
		var email_cnt=document.getElementById('remainder_email_cnt');
		var frm=document.listform;
		if(sel.value==1) {
			sms_cnt.innerHTML=num;
			email_cnt.innerHTML=num;
		} else {
			var oid_checked_num = 0;
			for(i=1;i < frm.oid.length;i++){
				if(frm.oid[i].checked){
					oid_checked_num++;
				}
			}
			sms_cnt.innerHTML=oid_checked_num;
			email_cnt.innerHTML=oid_checked_num;
		}
	}

	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_sms','batch_update_sendemail');

		for(var i=0; i<area.length; ++i){
			if(area[i]==selected_id){
				document.getElementById(selected_id).style.display = 'block';
			}else{
				document.getElementById(area[i]).style.display = 'none';
			}
		}
	}

	function LoadEmail(email_type){
		if(email_type == 'new'){
			//$('#email_subject_text').css('display','inline');
			$('#email_select_area').css('display','none');
		}else if(email_type == 'box'){
			//$('#email_subject_text').css('display','none');
			$('#email_select_area').css('display','inline');
		}
	}

	$(document).ready(function() {
		/*
		CKEDITOR.replace('mail_content',{
			 startupFocus : false,height:500
		});

		$('select#email_subject_select').change(function(){
			if($(this).val() != ''){
				$.ajax({
					type: 'GET',
					data: {'act': 'mail_info', 'mail_ix': $(this).val()},
					url: '../campaign/mail.act.php',
					dataType: 'json',
					async: true,
					beforeSend: function(){
					},
					success: function(mail_info){
						CKEDITOR.instances['mail_content'].setData(mail_info.mail_text);
						$('#email_subject_text').val(mail_info.mail_title);
					}
				});
			}
		});
		*/
	});
//-->
</script>";


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value='' />
</form>";
if($mmode == "personalization"){
    $P = new ManagePopLayOut();
    $P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
    $P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
    $P->strLeftMenu = order_menu();
    $P->Navigation = "주문관리 > 주문리스트";
    $P->title = "주문리스트";
    $P->NaviTitle = "주문리스트";
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
    }else if($view_type == "inventory"){
        $P->strLeftMenu = inventory_menu();
    }else{
        $P->strLeftMenu = order_menu();
    }
    $P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
    $P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
    $P->Navigation = "주문관리 > 주문리스트";
    $P->title = "주문리스트";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}

?>