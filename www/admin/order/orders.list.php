<?
/*
상품명에 cut_str 걸려있는거 다 제거함 kbk 13/08/06
*/
include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");

include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../campaign/mail.config.php");
include("../order/orders.lib.php");


$sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '".$_SESSION['admininfo']['mall_ix']."'
			and config_name = 'front_url'";

$db->query($sql);
$db->fetch();
$front_url = $db->dt['front_url'];

if($startDate == ""){
    /*
    $before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));
    $startDate = date("Y-m-d", $before10day);
    $endDate = date("Y-m-d");
    */

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

$sms_design = new SMS;

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

if(isset($product_type) && $product_type == '77'){
    if(isset($gift_type)){
        if($gift_type == 'A') {
            $where .= " AND o.choice_gift_order = '" . $choice_gift . "' 
            AND od.choice_gift_prd = '" . $choice_gift . "'";
        }elseif($gift_type == 'P') {//구매금액별 사은품
            $where .= " AND o.choice_gift_order = '" . $choice_gift . "'";
        }elseif($gift_type == 'G') {//상품별 사은품
            $where .= " AND od.choice_gift_prd = '" . $choice_gift . "'";
        }
    }
}
if(!empty($product_type)&& $product_type != '77'){
    $where .=" and od.product_type = '".$product_type."' ";
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
        if($type[$i]){
            if($type_str == ""){
                $type_str .= "'".$type[$i]."'";
            }else{
                $type_str .= ", '".$type[$i]."' ";
            }
        }
    }
    if($type_str != "") {
        $where .= "and od.status in ($type_str) ";
    }
}else{
    if($type){
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

if($_GET['mode'] == 'search'){
    if(!isset($_GET['mult_search_use'])) {
        $mult_search_use = 0;
    }
}else {
    $mult_search_use = 1;
}

if($search_type && $search_text){


    if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
        //다중검색 시작 2014-04-10 이학봉
        if($search_text != ""){

            if(strpos($search_text,",") !== false){
                $search_array = explode(",",$search_text);
                $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                $where .= "and $search_type in ( ";
                $count_where .= "and $search_type in ( ";

                for($i=0;$i<count($search_array);$i++){
                    if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                        $search_array[$i] = format_phone(trim($search_array[$i]));
                    }else {
                        $search_array[$i] = trim($search_array[$i]);
                    }
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $where .= "'".trim($search_array[$i])."'";
                            $count_where .= "'".trim($search_array[$i])."'";
                        }else{
                            $where .= "'".trim($search_array[$i])."' , ";
                            $count_where .= "'".trim($search_array[$i])."' , ";
                        }
                    }
                }
                $where .= ")";
                $count_where .= ")";
            }else if(strpos($search_text,"\n") !== false){//\n
                $search_array = explode("\n",$search_text);
                $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                $where .= "and $search_type in ( ";
                $count_where .= "and $search_type in ( ";

                for($i=0;$i<count($search_array);$i++){
                    if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                        $search_array[$i] = format_phone(trim($search_array[$i]));
                    }else {
                        $search_array[$i] = trim($search_array[$i]);
                    }
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $where .= "'".trim($search_array[$i])."'";
                            $count_where .= "'".trim($search_array[$i])."'";
                        }else{
                            $where .= "'".trim($search_array[$i])."' , ";
                            $count_where .= "'".trim($search_array[$i])."' , ";
                        }
                    }
                }
                $where .= ")";
                $count_where .= ")";
            }else{
                if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                    $where .= " and ".$search_type." = '".format_phone(trim($search_text))."'";
                    $count_where .= " and ".$search_type." = '".trim($search_text)."'";
                }else {
                    $where .= " and ".$search_type." = '".trim($search_text)."'";
                    $count_where .= " and ".$search_type." = '".trim($search_text)."'";
                }
            }

            if ( substr_count($search_type, 'odd.') > 0) {
                $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
            }

            if(substr_count($left_join,'shop_order_payment op')==0){
                if (substr_count($search_type, 'op.') > 0) {
                    $left_join .= " left join shop_order_payment op on (op.oid=od.oid) ";
                }
            }
        }
    }else {

        // 주문자휴대폰 or 주문번호
        if ($search_type == "combi_mboid") {
            $where .= "and (   REPLACE(bmobile,'-','') LIKE '%" . trim($search_text) . "%' 
                        or REPLACE(odd.rmobile,'-','') LIKE '%" . trim($search_text) . "%' 
                        or bmobile LIKE '%" . trim($search_text) . "%'  
                        or odd.rmobile LIKE '%" . trim($search_text) . "%' 
                        or o.oid LIKE '%" . trim($search_text) . "%'
                        ) ";
        } else if ($search_type == "combi_name") {
            $where .= "and (bname LIKE '%" . trim($search_text) . "%'  or odd.rname LIKE '%" . trim($search_text) . "%') ";
        } else if ($search_type == "combi_cooid") {
            $where .= "and (od.co_oid = '" . trim($search_text) . "'  or od.co_od_ix = '" . trim($search_text) . "') ";
        } else if ($search_type == "combi_email") {
            $where .= "and (bmail LIKE '%" . trim($search_text) . "%'  or odd.rmail LIKE '%" . trim($search_text) . "%') ";
        } else if ($search_type == "combi_tel") {
            $where .= "and (REPLACE(btel,'-','') LIKE '%" . trim($search_text) . "%'  or REPLACE(odd.rtel,'-','') LIKE '%" . trim($search_text) . "%' or btel LIKE '%" . trim($search_text) . "%' or odd.rtel LIKE '%" . trim($search_text) . "%') ";
        } else if ($search_type == "combi_mobile") {
            $where .= "and ( REPLACE(bmobile,'-','') LIKE '%" . trim($search_text) . "%'  or REPLACE(odd.rmobile,'-','') LIKE '%" . trim($search_text) . "%' or bmobile LIKE '%" . trim($search_text) . "%'  or odd.rmobile LIKE '%" . trim($search_text) . "%' ) ";
        } else {
            if ($search_type == "o.bmobile" || $search_type == "odd.rmobile") {
                $where .= "and ( REPLACE(" . $search_type . ",'-','') LIKE '%" . trim($search_text) . "%' or " . $search_type . " LIKE '%" . trim($search_text) . "%' ) ";
            } else {
                $where .= "and $search_type LIKE '%" . trim($search_text) . "%' ";
            }
        }
        if ($search_type == "combi_mboid" || $search_type == "combi_name" || $search_type == "combi_email" || $search_type == "combi_tel" || $search_type == "combi_mobile" || substr_count($search_type, 'odd.') > 0) {
            $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
        }

        if(substr_count($left_join,'shop_order_payment op')==0){
            if (substr_count($search_type, 'op.') > 0) {
                $left_join .= " left join shop_order_payment op on (op.oid=od.oid) ";
            }
        }
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

if(is_array($cid)){
    for($i = 0; $i < count($cid); $i++){
        if($cid[$i] != ""){
            if($cid_str == ""){
                $cid_str .= "'".$cid[$i]."'";
            }else{
                $cid_str .= ", '".$cid[$i]."' ";
            }
        }
    }

    if($cid_str != ""){
        $where .= "and od.cid in ($cid_str) ";
    }
}else{
    if($cid){
        $where .= "and od.cid = '$cid' ";
    }
}

if(!empty($payment_sprice) && !empty($payment_eprice)){
    $where .= " and o.payment_price between '".$payment_sprice."' and '".$payment_eprice."' ";
}

if(!empty($user_type)){
    if($user_type == 'Y'){
        $where .= " and o.user_code != '' ";
    }else{
        $where .= " and o.user_code = '' ";
    }
}

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
                                    <tr>
                                        <td class='input_box_title' colspan='2'>
                                            <div style='float:left;padding-top:5px;'><b>카테고리선택</b></div>
                                            <div style='float:left;padding-left:20px;'>
                                                <img src='../images/icon/search_icon.gif' value='검색' onclick=\"PoPWindow('../product/search_category.php?group_code=',800,600,'add_brand_category')\" style='cursor:pointer;'>
                                            </div>
                                        </td>
                                        <td class='input_box_item' colspan=3 >
                                            <div id='selected_category_6' style='padding:10px;overflow-y:scroll;max-height:100px;'>
                                            <table width='98%' cellpadding='0' cellspacing='0' id='objMd'>
                                            <colgroup>
                                                <col width='*'>
                                                <col width='600'>
                                            </colgroup>
                                            <tbody>";
                if(count($cid) > 0){

                    for($k=0;$k<count($cid);$k++){

                        $re_cid = $cid[$k];
                        $sql = "select * from shop_category_info where cid = '".$re_cid."'";
                        $slave_db->query($sql);
                        $slave_db->fetch();
                        $depth = $slave_db->dt[depth];

                        for($i=0;$i<=$depth;$i++){
                            $this_cid = substr(substr($re_cid, 0,($i*3+3)).'000000000000',0,15);
                            $sql = "select * from shop_category_info where cid = '".$this_cid."'";
                            $slave_db->query($sql);
                            $slave_db->fetch();
                            $cname = $slave_db->dt[cname];
                            $relation_cname[$k] .= $cname." > ";
                        }

                        $Contents .= "	<tr style='height:26px;' id='row_".$re_cid."'>
                                                            <td>
                                                            <input type='hidden' name='cid[]' id='cid_".$re_cid."' value='".$re_cid."'>".$relation_cname[$k]."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_cid."')\"><img src='../images/korean/btc_del.gif' border='0'></a>
                                                            </td>
                                                        </tr>";
                    }
                }
                $Contents .= "
                                                </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
    
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
        /*
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
        */
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
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ASCROW."' value='".ORDER_METHOD_ASCROW."' ".CompareReturnValue(ORDER_METHOD_ASCROW,$method,' checked')." ><label for='method_".ORDER_METHOD_ASCROW."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ASCROW)."'>".getMethodStatus(ORDER_METHOD_ASCROW)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ASCROW.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'>".getMethodStatus(ORDER_METHOD_ICHE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle' --></label><br/>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'>".getMethodStatus(ORDER_METHOD_PHONE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'>".getMethodStatus(ORDER_METHOD_CASH)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle' --></label>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'>".getMethodStatus(ORDER_METHOD_SAVEPRICE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle' --></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_RESERVE."' value='".ORDER_METHOD_RESERVE."' ".CompareReturnValue(ORDER_METHOD_RESERVE,$method,' checked')." ><label for='method_".ORDER_METHOD_RESERVE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_RESERVE)."'>".getMethodStatus(ORDER_METHOD_RESERVE)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_RESERVE.".gif' align='absmiddle' --></label>
													
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PAYCO."' value='".ORDER_METHOD_PAYCO."' ".CompareReturnValue(ORDER_METHOD_PAYCO,$method,' checked')." ><label for='method_".ORDER_METHOD_PAYCO."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PAYCO)."'>".getMethodStatus(ORDER_METHOD_PAYCO)."<!--img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PAYCO.".gif' align='absmiddle'--></label>
													
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_NPAY."' value='".ORDER_METHOD_NPAY."' ".CompareReturnValue(ORDER_METHOD_NPAY,$method,' checked')." ><label for='method_".ORDER_METHOD_NPAY."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_NPAY)."'>".getMethodStatus(ORDER_METHOD_NPAY)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_NPAY.".gif' align='absmiddle' --></label>
													
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_TOSS."' value='".ORDER_METHOD_TOSS."' ".CompareReturnValue(ORDER_METHOD_TOSS,$method,' checked')." ><label for='method_".ORDER_METHOD_TOSS."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_TOSS)."'>".getMethodStatus(ORDER_METHOD_TOSS)."<!-- img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_TOSS.".gif' align='absmiddle' --></label>
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
											<tr height=30 style='display:none;'>
											    <!--
												<th class='search_box_title' colspan='2'>셀러명 </th>
												<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
												-->
												<th class='search_box_title' colspan='2'>담당MD </th>
												<td class='search_box_item' colspan='3'>".MDSelect($md_code)."</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' colspan='2'>상품관리구분 </th>
												<td class='search_box_item'>";

    if($view_type != 'inventory'){
        /*
        $Contents .= "
													<input type='checkbox' name='p_admin[]' id='p_admin_a' value='A' ".CompareReturnValue("A",$p_admin,' checked')." ><label for='p_admin_a'>본사상품</label>&nbsp;
													<input type='checkbox' name='p_admin[]' id='p_admin_s' value='S' ".CompareReturnValue("S",$p_admin,' checked')." ><label for='p_admin_s'>셀러상품</label>&nbsp;
													";*/
    }

    $Contents .= "
													<input type='checkbox' name='stock_use_yn' id='stock_use_y' value='Y' ".CompareReturnValue("Y",$stock_use_yn,' checked')." ><label for='stock_use_y'>WMS상품</label>&nbsp;
                                                    <input type='checkbox' name='product_type' id='product_type' value='77' ".CompareReturnValue("77",$product_type,' checked')." ><label for='product_type'>사은품</label>&nbsp;
                                                    <select name='gift_type' id='gift_type' ".($product_type == '77' ? '' : 'disabled').">
                                                        <option value='A' ".CompareReturnValue('A',$gift_type,' selected').">전체</option>
                                                        <option value='P' ".CompareReturnValue('P',$gift_type,' selected').">구매금액별 사은품</option>
                                                        <option value='G' ".CompareReturnValue('G',$gift_type,' selected').">상품별 사은품</option>
                                                    </select>&nbsp;
                                                    <select name='choice_gift' id='choice_gift' ".($product_type == '77' ? '' : 'disabled').">
                                                        <option value='Y' ".CompareReturnValue('Y',$choice_gift,' selected').">선택</option>
                                                        <option value='N' ".CompareReturnValue('N',$choice_gift,' selected').">선택안함</option>
                                                    </select>&nbsp;
                                                    ";

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
														<option value='od.di_date' ".CompareReturnValue('od.di_date',$date_type,' selected').">배송중일자</option>
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
												<th class='search_box_title' colspan='2'>조건검색 
												<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								                <label for='mult_search_use'>(다중검색 체크)</label>
												</th>
												<td class='search_box_item' style='text-align:left;'>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<tr>
														<td  >
														<select name='search_type' style='font-size:12px;'>
														    <option value='' ".CompareReturnValue('',$search_type,' selected').">선택해주세요</option>
														    <option value='combi_mboid' ".CompareReturnValue('combi_mboid',$search_type,' selected').">주문자휴대폰 & 주문번호</option>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자 & 수취인-명</option>
															<option value='combi_email' ".CompareReturnValue('combi_email',$search_type,' selected').">주문자 & 수취인-Email</option>
															<option value='combi_tel' ".CompareReturnValue('combi_tel',$search_type,' selected').">주문자 & 수취인전화번호</option>
															<option value='combi_mobile' ".CompareReturnValue('combi_mobile',$search_type,' selected').">주문자 & 수취인휴대폰</option>
															<option value='o.bname' ".CompareReturnValue('o.bname',$search_type,' selected').">주문자명</option>
															<option value='o.buserid' ".CompareReturnValue('o.buserid',$search_type,' selected').">주문자ID</option>
															<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
															<option value='combi_cooid' ".CompareReturnValue('combi_cooid',$search_type,' selected').">제휴사주문번호</option>
															<option value='odd.rname' ".CompareReturnValue('odd.rname',$search_type,' selected').">수취인명</option>
															<option value='o.bmobile' ".CompareReturnValue('o.bmobile',$search_type,' selected').">주문자 휴대폰</option>
															<option value='odd.rmobile' ".CompareReturnValue('odd.rmobile',$search_type,' selected').">수취인 휴대폰</option>
															<option value='od.invoice_no' ".CompareReturnValue('od.invoice_no',$search_type,' selected').">송장번호</option>
															<option value='od.pname' ".CompareReturnValue('od.pname',$search_type,' selected').">상품명</option>
															<option value='od.option_text' ".CompareReturnValue('od.option_text',$search_type,' selected').">옵션명</option>
															<option value='od.gid' ".CompareReturnValue('od.gid',$search_type,' selected').">품목코드</option>
															<option value='od.gu_ix' ".CompareReturnValue('od.gu_ix',$search_type,' selected').">품목시스템코드</option>
															<option value='od.pid' ".CompareReturnValue('od.pid',$search_type,' selected').">상품시스템코드</option>
															<option value='op.tid' ".CompareReturnValue('op.tid',$search_type,' selected').">네이버주문번호(승인번호)</option>
														</select>
														</td>
														<td  >
														    <div id='search_text_input_div'>
														        <input type='text' class=textbox name='search_text' size='30' value='$search_text' style='' >
														    </div>
														    <div id='search_text_area_div' style='display:none;'>
                                                                <textarea name='search_text' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' disabled>".$search_text."</textarea>
                                                            </div>
														</td>
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
											<tr height=30 >
											   
												<th class='search_box_title' colspan='2'>결제 금액대별 검색</th>
												<td class='search_box_item' >
												    <input type='text' class='textbox' name='payment_sprice' value='".$payment_sprice."' /> ~
												    <input type='text' class='textbox' name='payment_eprice' value='".$payment_eprice."' /> 
                                                </td>
												<th class='search_box_title'>회원타입 </th>
												<td class='search_box_item'>
												    <input type='radio' name='user_type' id='user_type' value='' ".CompareReturnValue('',$user_type,'checked')." ><label for='user_type'>전체</label>&nbsp;
													<input type='radio' name='user_type' id='user_type_y' value='Y' ".CompareReturnValue('Y',$user_type,'checked')." ><label for='user_type_y'>회원</label>&nbsp;
                                                    <input type='radio' name='user_type' id='user_type_n' value='N' ".CompareReturnValue('N',$user_type,'checked')." ><label for='user_type_n'>비회원</label>
                                                    
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


$total = 0;
if($mode == "search"){
    $sql = "SELECT distinct o.oid
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				$left_join
				$where ";
//echo $sql;exit;
    $slave_db->query($sql);
    $total = $slave_db->total;


    $sql = "select mall_ix from ".TBL_SHOP_SHOPINFO." where mall_div = 'B' and currency_unit = 'KRW' ";
    $db->query($sql);
    $db->fetch();
    $local_mall_ix = $db->dt['mall_ix'];

    $sql = "select mall_ix from ".TBL_SHOP_SHOPINFO." where mall_div = 'B' and currency_unit = 'USD' ";
    $db->query($sql);
    $db->fetch();
    $global_mall_ix = $db->dt['mall_ix'];

    $sql = "SELECT sum(pt_dcprice) as product_price
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		$left_join
		$where and od.mall_ix = '".$local_mall_ix."' ";
    $slave_db->query($sql);
    $prder_price = $slave_db->fetch();

	$sql = "SELECT sum(pt_dcprice) as product_price
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		$left_join
		$where and od.status = 'BF' and od.mall_ix = '".$local_mall_ix."' ";
    $slave_db->query($sql);
    $confirmed_price = $slave_db->fetch();

    $sql = "SELECT sum(pt_dcprice) as product_price
		FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
		$left_join
		$where and od.mall_ix = '".$global_mall_ix."'";
    $slave_db->query($sql);
    $global_prder_price = $slave_db->fetch();


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

$Contents .= "<td colspan=3 align=left><b class=blk>전체 주문수 : $total 건</b> ".($_SESSION["admininfo"]["admin_level"] > 8 ? "<!--총주문금액 : ".number_format($prder_price[total_price])." 원--> 구매확정 : ".number_format($confirmed_price[product_price])." 원 ,국내상품금액 : ".number_format($prder_price[product_price])." 원 ,  해외상품금액 :  $".number_format($global_prder_price[product_price])."  <!--배송금액 : ".number_format($prder_price[delivery_price])." 원-->" : "")."</td><td colspan=10 align=right>";

if($admininfo[admin_level] != 9){
    $Contents .= "<span style='color:red'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
}

/*
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
	<a href='../order/excel_out.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
}else{
    $Contents .= "
	<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}
*/




$Contents .= orderExcelTemplateSelect("O");

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    /*
    $Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다..'>
    <a href='../order/orders_excel2003.php?view_type=$view_type&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></span>";
    */

	/*
    $Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다.'>
	<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' 
	onclick=\"if(jQuery('#oet_ix').val().length > 0){location.href='../order/orders_excel2003.php?oet_ix='+jQuery('#oet_ix').val()+'&view_type=$view_type&".$QUERY_STRING."'}else{alert('엑셀양식을선택해주세요.');}\" >
	</span>";
	*/


    $Contents .= "<span class='helpcloud' help_height='30' help_html='주문정보를 엑셀로 다운로드 하실 수 있습니다.'>
	<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' 
	onclick=\"if(jQuery('#oet_ix').val().length > 0){ ig_excel_dn_chk('../order/orders_excel2003.php?oet_ix='+jQuery('#oet_ix').val()+'&view_type=$view_type&".$QUERY_STRING."'); }else{alert('엑셀양식을선택해주세요.');}\" >
	</span>";


}else{
    $Contents .= "
	<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a>";
}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents .="<input type='button' id='selectExcelDownLoad' value='선택주문 엑셀다운로드' />";
}else{
    $Contents .= "
	<a href=\"".$auth_excel_msg."\"><input type='button'  value='선택주문 엑셀다운로드' /></a>";
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
	<col width='8%'/>
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
		<td align='center' class='m_td helpcloud' help_width='150' help_height='80' help_html='O:사은품 선택 <br>X:사은품 선택안함 <br>N:사은품 존재하지 않음' style='background-color:#fff7da;'  nowrap>
		    <font color='#000000' class=small><b>금액/카테고리/상품포함</br>사은품 선택여부</b></font>
		</td>
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
            if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
                //다중검색 시작 2014-04-10 이학봉
                if($search_text != ""){
                    if(strpos($search_text,",") !== false){
                        $search_array = explode(",",$search_text);
                        $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
                        $addWhere .= "and $search_type in ( ";
                        for($i=0;$i<count($search_array);$i++){
                            $search_array[$i] = trim($search_array[$i]);
                            if($search_array[$i]){
                                if($i == count($search_array) - 1){
                                    $addWhere .= "'".trim($search_array[$i])."'";
                                }else{
                                    $addWhere .= "'".trim($search_array[$i])."' , ";
                                }
                            }
                        }
                        $addWhere .= ")";
                    }else if(strpos($search_text,"\n") !== false){//\n
                        $search_array = explode("\n",$search_text);
                        $search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
                        $addWhere .= "and $search_type in ( ";

                        for($i=0;$i<count($search_array);$i++){
                            $search_array[$i] = trim($search_array[$i]);
                            if($search_array[$i]){
                                if($i == count($search_array) - 1){
                                    $addWhere .= "'".trim($search_array[$i])."'";
                                }else{
                                    $addWhere .= "'".trim($search_array[$i])."' , ";
                                }
                            }
                        }
                        $addWhere .= ")";
                    }else{
                        $addWhere .= " and ".$search_type." = '".trim($search_text)."'";
                    }

                }

            }else{
                $addWhere .= "and $search_type LIKE '%".trim($search_text)."%' ";
            }
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
		o.oid, 
		o.delivery_box_no, o.payment_price, 
		o.payment_agent_type, o.user_code as user_id,
		o.buserid, o.bmobile, o.bname, o.gp_ix,
		o.mem_group, o.status as ostatus, 
		o.order_date as regdate, 
		o.total_price,od.ode_ix,
		o.choice_gift_order,
		o.choice_gift_order_c,
		o.choice_gift_order_p,
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
        od.mall_ix,
		od.delivery_type,
		od.delivery_policy,
		od.delivery_package,
		od.delivery_method,
		od.delivery_pay_method,
		od.ori_company_id,
		od.delivery_addr_use,
		od.factory_info_addr_ix,
		od.erp_link_date,
        od.gid,
		od.choice_gift_prd,
		od.gift_type,
        od.add_info,
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
        ,(select tid from shop_order_payment where oid = o.oid and pay_type = 'G' and method = '".ORDER_METHOD_NPAY."'  ) as npay_oid
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

    $origin_currency_unit = $admin_config["currency_unit"];
    for ($i = 0; $i < count($order_list); $i++){

        $admin_config["currency_unit"] = check_currency_unit($order_list[$i]['mall_ix']);
        if($admin_config["currency_unit"] == 'USD'){
            $decimals_value = 2;
        }else{
            $decimals_value = 0;
        }

        //주문삭제 버튼
        //if($_SESSION["admininfo"]["charger_id"]=="forbiz"){
//        if($order_list[$i][status] == ORDER_STATUS_DELIVERY_COMPLETE)		{
//            $delete = "<a href=\"javascript:alert(language_data['orders.list.php']['A'][language]);\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";//[처리완료] 기록은 삭제할 수 없습니다.
//        }else if($order_list[$i][status] != ORDER_STATUS_CANCEL_COMPLETE && $order_list[$i][method] == "1"){
//            $delete = "<a href=\"javascript:order_delete('delete','".$order_list[$i][oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";
//        }else{
//            $delete = "<a href=\"javascript:act('delete','".$order_list[$i][oid]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:2px;'></a>";
//        }
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

        $discount_info = $currency_display[$admin_config["currency_unit"]]["front"].number_format($order_list[$i][ptprice]-$order_list[$i][pt_dcprice],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"];

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

            $b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$order_list[$i][buserid]."/".wel_masking_seLen($order_list[$i][bname], 1, 1)."<br/>핸드폰 : ".$order_list[$i][bmobile]." <br/>회원그룹 : ".$order_list[$i][mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($order_list[$i][user_id],/*($order_list[$i][gp_ix]=='2' ? "<b class='red'>VIP</b>" : "").*/wel_masking_seLen($order_list[$i][bname], 1, 1).( $order_list[$i][buserid] ? "(<span class='small'>".$order_list[$i][buserid]."</span>)" : "(<span class='small'>비회원</span>)"))."</b> <br/> ".($_SESSION["admininfo"]["admin_level"] > 8 && $order_list[$i][user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$order_list[$i][user_id]."&mmode=pop',1280,800,'member_view')\"  style='cursor:pointer;'>" : "");


            $recipient_info=getOrderRecipientInfo($order_list[$i]);
            $recipient_=$recipient_info["recipient"];
            $recipient_str=$recipient_info["recipient_str"];
            $recipient_width=$recipient_info["recipient_width"];
            $recipient_height=$recipient_info["recipient_height"];

            $r_mem_info= "<b style='cursor:pointer' class='helpcloud' help_width='".$recipient_width."' help_height='".$recipient_height."' help_html='".$recipient_str."' />".wel_masking_seLen($recipient_, 1, 1)."</b>";

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
												".GetDisplayDivision($order_list[$i]['mall_ix'], "text")."
												<br>
												<font color='#000000' class=small><b>".($order_list[$i][rfid]?str_replace(array("전체 > ","검색엔진 > "),"",getRefererCategoryPath2($order_list[$i][rfid], 4)):'')."</b></font>
												
												</td>";
            }

            $Contents .= "
											<td  align='center'  style='background-color:#fff7da;'><font color='orange' class=small><b>".$order_list[$i][regdate]."</b></font> <font color='red' class=small><b>".(!empty($order_list[$i][erp_link_date])?" - ERP연동":"")."</b></font></td>
											<td  align='center'  style='background-color:#fff7da;'>
												<font color='blue' class=small><b>
													<span style='color:#007DB7;font-weight:bold;' class='small'>".$order_list[$i][oid]."</span></b>
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
                if($admin_config["currency_unit"] == 'USD'){
                    $decimals = 2;
                }else{
                    $decimals = 0;
                }
                $exchange_rate_payment_price = getOrderExchangeRatePaymentPrice($order_list[$i]);
                $Contents .= "<td  align='center'  style='background-color:#fff7da;'nowrap><font color='red' class=small><b>
                    ".$currency_display[$admin_config["currency_unit"]]["front"]." 
                    ".number_format($order_list[$i][total_price],$decimals)." 
                    ".$currency_display[$admin_config["currency_unit"]]["back"]." 
                    
                    ".($exchange_rate_payment_price > 0 ? "<br/>(".number_format($exchange_rate_payment_price,$decimals_value).")" : "")."</b></font></td>";
            }

            if($order_list[$i]['choice_gift_order'] == 'Y'){
                $choice_gift_order = 'O';
            }elseif($order_list[$i]['choice_gift_order'] == 'N'){
                $choice_gift_order = 'X';
            }else{
                $choice_gift_order = 'N';
            }

            if($order_list[$i]['choice_gift_order_c'] == 'Y'){
                $choice_gift_order .= '/O';
            }elseif($order_list[$i]['choice_gift_order_c'] == 'N'){
                $choice_gift_order .= '/X';
            }else{
                $choice_gift_order .= '/N';
            }

            if($order_list[$i]['choice_gift_order_p'] == 'Y'){
                $choice_gift_order .= '/O';
            }elseif($order_list[$i]['choice_gift_order_p'] == 'N'){
                $choice_gift_order .= '/X';
            }else{
                $choice_gift_order .= '/N';
            }
            $Contents .= "
                                            <td  align='center'  style='background-color:#fff7da;'nowrap>".$choice_gift_order."</td>
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
											<!-- a  href='".$front_url."/shop/goodsView/".$order_list[$i][pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $order_list[$i][pid], 'm',$order_list[$i])."'  width=50 style='margin:5px;'></a -->
											<a  href='".$front_url."/shop/goodsView/".$order_list[$i][pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/addimgNew", $order_list[$i][pid], 'list',$order_list[$i])."'  target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/addimgNew", $order_list[$i][pid], 'slist',$order_list[$i])."'  width=50 style='margin:5px;'></a><br/>";

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

        if($order_list[$i]['product_type'] == '77') {
            $freeProductDesc = '';
            if($order_list[$i]['gift_type'] == 'P'){
                $freeProductDesc = '[구매금액별 사은품]';
            }elseif($order_list[$i]['gift_type'] == 'G'){
                $freeProductDesc = '[사은품]';
            }

        }else{
            $freeProductDesc = '';
        }

        if($order_list[$i][order_from] == auction){
            $Contents .= "<span style='color:#007DB7;font-weight:bold;' class='small'>".$freeProductDesc.$order_list[$i][od_ix]." ".($order_list[$i][co_od_ix] ? "(".$order_list[$i][co_od_ix].")" : "")."</span><br/>";
        }else{
            $Contents .= "<span style='color:#007DB7;font-weight:bold;' class='small'>".$freeProductDesc.$order_list[$i][od_ix]." ".($order_list[$i][co_oid] ? "(".$order_list[$i][co_oid].")" : "")."</span><br/>";
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
            //$Contents .= "<b class='".($order_list[$i][product_type]=='99' ? "red" : "blue")."' >[".$order_list[$i][brand_name]."] ".$order_list[$i][pname]."</b><br/><strong>".$order_list[$i][set_name]."<br/></strong>".$order_list[$i][sub_pname];
        }else{
            //$Contents .= "[".$order_list[$i][company_name]."] ".$order_list[$i][pname];
        }
        
        $Contents .= "[".$order_list[$i][company_name]."] ".$order_list[$i][pname]; //셀러명으로 고정

        $Contents .= "</a>";

        $Contents .= "<br/> ▶ 상품코드:".$order_list[$i][gid];

        if(!empty($order_list[$i][add_info])) {
            $Contents .= "<br/> ▶ 색상:".$order_list[$i][add_info];
        }

        if(strip_tags($order_list[$i][option_text])){
            $Contents .= "<br/> ▶ ".strip_tags($order_list[$i][option_text]);
        }
        if(!empty($order_list[$i][npay_oid])){
            $Contents .= "<br/> ▶ 네이버페이주문번호:".$order_list[$i][npay_oid];
        }

        if($order_list[$i]['choice_gift_prd'] == 'N'){
            $Contents .= "<br/> ▶ 사은품 선택 안함";
        }

        $Contents .="
										</TD>
									</TR>
								</TABLE>
							</td>
							<td class='' align=center>
								".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][listprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."<br/>
								/".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][psprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."
							</td>
							<td class='' align=center>".$discount_info."</td>
							<td class='' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][pt_dcprice],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]." <br/> ".number_format($order_list[$i][reserve],$decimals_value)."P (".number_format($order_list[$i][pcnt])."개)</td>";


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
                        ".$currency_display[$admin_config["currency_unit"]]["front"].number_format($order_list[$i][delivery_totalprice],$decimals_value).$currency_display[$admin_config["currency_unit"]]["back"]."
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
							<td class='' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($order_list[$i][expect_ac_price],$decimals_value)."".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
						</tr>";

        $b_oid = $order_list[$i][oid];
        $bcompany_id = $order_list[$i][company_id];
        $bproduct_id = $order_list[$i][pid];
        $bset_group = $order_list[$i][set_group];
        $bori_ode_ix = $order_list[$i][ode_ix];


    }
    $admin_config["currency_unit"] = $origin_currency_unit;
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
    $Contents .= "<a href='JavaScript:SelectDelete(document.forms[\"listform\"]);'><img src='../images/korean/bt_all_del.gif' border='0' align='absmiddle'></a>";
}

$Contents .= "
	</td>
	<td colspan='13' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
</tr>
</table>
";


$select = "
	<nobr>
		<select name='update_type' onChange='view_order_num(this,\"$total\")'>
			<option value='1'>검색한주문 전체에게</option>
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_sms' value='sms'  checked onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS 일괄발송</label>
		<!--<input type='radio' name='update_kind' id='update_kind_sendemail' value='sendemail' onclick=\"ChangeUpdateForm('batch_update_sendemail');\"><label for='update_kind_sendemail'>이메일 일괄발송</label>-->
	</nobr>";

	$help_text="
	<div id='batch_update_sms' >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>sms 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F' )."</span></div>
	<table cellpadding=0 cellspacing=0>
		<col width='140xp;'>
		<col width='200px;'>
		<col width='140px;'>
		<col width='140px;'>
		<col width='*'>
		<tr>
			<td style='vertical-align:top;'>
				<table class='box_shadow' style='width:139px;height:120px;' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top style='padding:5px 7px 5px 7px'>
							<table cellpadding=0 cellspacing=0><!--CheckSpecialChar(this);-->
							<tr><td align=left>mallstory sms </td></tr>
							<tr><td><textarea style='width:106px;height:100px;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' name='sms_text' onkeyup=\"fc_chk_byte(this,2000, this.form.sms_text_count);\" ></textarea></td></tr>
							<tr><td height=20 align=right><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> / 2,000 byte </td></tr>
							</table>
						</td>
						<th class='box_06'></th>
					</tr>
					<tr>
						<th class='box_07'></th>
						<td class='box_08'></td>
						<th class='box_09'></th>
					</tr>
				</table>
			</td>";

	$cominfo = getcominfo();

	$help_text .= "
			<td valign=top style='padding:0 0 0 10px'>
				<table cellpadding=0 cellspacing=0 ><input type=hidden name='sms_send_page' value='1'>
				    <input type='hidden' name='search_total' value='".$total."' />
					<tr height=26>
						<td align=left width=90 class=small>보내는사람 : </td>
						<td><input type=text name='send_phone' class=textbox style='display:inline;' size=12 value='".$cominfo[com_phone]."'></td>
					</tr>
					<!--<tr height=22><td align=left class=small>SMS 잔여건수 : </td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>-->
					<tr height=22><td align=left class=small nowrap>발송수/발송대상 : </td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</b> 명</td></tr>
					<tr height=22>
						<td align=left class=small>발송수량(1회) : </td>
						<td>
						<select name=sms_max>
							<option value='5' >5</option>
							<option value='10'  >10</option>
							<option value='20' >20</option>
							<option value='50' >50</option>
							<option value='100' selected>100</option>
							<option value='200' >200</option>
							<option value='300' >300</option>
							<option value='400' >400</option>
							<option value='500' >500</option>
							<option value='1000' >1000</option>
						</select>
						</td>
					</tr>
					<!--
					<tr height=22>
						<td align=left class=small>발송대상 : </td>
						<td>
						<select name='send_target'>
							<option value='r' selected>수취인</option>
							<option value='b'>주문자</option>
						</select>
						</td>
					</tr>
					-->
					<tr height=22><td align=left class=small>일시정지 : </td><td><input type='checkbox' name='stop' ></td></tr>
					<tr height=50>
                        <td align=center colspan=2>";
                            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                                $help_text .= "
                                <input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0>";
                            }else{
                                $help_text .= "
                                <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0></a>";
                            }
                            $help_text .= "
                        </td>
                    </tr>
				</table>
			</td>
			<td style='vertical-align:top; display:none;'>
				<table class='box_shadow' style='width:139px;height:120px;' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top style='padding:5px 7px 5px 7px'>
							<table cellpadding=0 cellspacing=0>
							<tr><td align=left>※ 예시</td></tr>
							<tr><td><textarea style='width:106px;height:100px;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' readonly>{name}님 {site}에 주문하신 {totalPrice}원을 {bank}로 송금 해주셔야 발송됩니다.</textarea></td></tr>
							</table>
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
			<td style='vertical-align:top;display:none;'>
				<table class='box_shadow' style='width:139px;height:120px;' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'>=></th>
						<td class='box_05'  valign=top style='padding:5px 7px 5px 7px'>
							<table cellpadding=0 cellspacing=0>
							<tr><td align=left>&nbsp;</td></tr>
							<tr><td><textarea style='width:106px;height:100px;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' readonly>홍길동님 몰스토리에 주문하신 10000원을 나래은행 000-000-0000 로 송금 해주셔야 발송됩니다.</textarea></td></tr>
							</table>
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
			<td valign=top style='padding:0 0 0 10px'>
				<table cellpadding=0 cellspacing=0 >
					<tr height=22><td align=left colspan='2' nowrap>※ 사용할수 있는 치환문자열</td></tr>
					<tr height=22><td align=left class=small nowrap>{name} : </td><td nowrap> 고객명(주문자) </td></tr>
					<tr height=22><td align=left class=small nowrap>{site} : </td><td nowrap> 사이트명</td></tr>
					<tr height=22><td align=left class=small nowrap>{orderDate} : </td nowrap><td> 주문일</td></tr>
					<tr height=22><td align=left class=small nowrap>{totalPrice} : </td nowrap><td> 총주문금액</td></tr>
					<!--<tr height=22><td align=left class=small nowrap>{bank} : </td><td nowrap> 입금계좌정보<br/>(가상계좌정보포함)</td></tr>-->
				</table>
			</td>
		</tr>
	</table>
	</div>
	<div id='batch_update_sendemail' style='display:none' >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif' align=absmiddle> <b>email 일괄발송</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 email 을 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J' )."</span></div>
	<table cellpadding=3 cellspacing=0 width=100% class='input_table_box'>
		<col width=15%>
		<col width=35%>
		<col width=15%>
		<col width=35%>
		<tr>
			<td class='input_box_title'> <b>이메일 제목</b></td>
			<td class='input_box_item' colspan=3>
				<table cellpadding=0>
					<tr>
						<td><input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px;height:21px;padding:0px;margin:0px;' ></td>

						<td>
						<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
						<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label>
						</td>
					</tr>
					<tr>
						<td colspan=2 id='email_select_area' style='display:none;'>
						".getMailList("","","display:inline;width:250px;")."
						</td>
					</tr>
				</table>
				<!--
				<input type=text name='email_subject' id='email_subject_text'   class=textbox value=''  style='width:250px' >  <span class='small blu'></span>
				<select name='email_subject_select' id='email_subject_select' style='display:none;width:250px;'>
					<option value=''>이메일을 선택해주세요</option>
				</select>
				<input type='radio' name='email_type' id='email_type_new' value='new' ".($email_type == "new" || $email_type == "" ? "checked":"")." onclick=\"LoadEmail('new');\"><label for='email_type_new'>새로작성</label>
				<input type='radio' name='email_type' id='email_type_box' value='box' ".CompareReturnValue("box",$update_kind,"checked")." onclick=\"LoadEmail('box');\"><label for='email_type_box'>기존이메일선택</label>
				-->
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>참조</b></td>
			<td class='input_box_item' colspan=3>
				<input type=text name='mail_cc'  class=textbox value='' style='width:350px' > <span class='small blu'>콤마(,) 구분으로 이메일을 입력해주세요.</span>
			</td>
		</tr>
		<tr height=22><input type=hidden name='email_send_page' value='1'>
			<td class='input_box_title'> <b>발송수/발송대상 </b> </td>
			<td class='input_box_item'><b id='sended_email_cnt' class=blu>0</b> 건 / <b id='remainder_email_cnt'>$total</b> 명</td>
			<td class='input_box_title'> <b>발송수량(1회) </b> </td>
			<td class='input_box_item'>
				<select name=email_max>
					<option value='5' >5</option>
					<option value='10'  >10</option>
					<option value='20' >20</option>
					<option value='50' >50</option>
					<option value='100' selected>100</option>
					<option value='200' >200</option>
					<option value='300' >300</option>
					<option value='400' >400</option>
					<option value='500' >500</option>
					<option value='1000' >1000</option>
				</select>
			</td>
		</tr>
		<tr height=22>
			<td class='input_box_title'> <b>일시정지 </b> </td>
			<td class='input_box_item' colspan=3><input type='checkbox' name='email_stop' id='email_stop'><label for='email_stop'>정지</label></td>
		</tr>
		<tr>
			<td class='input_box_item' style='padding:0px;' colspan=4><textarea name='mail_content' id='mail_content' style='display:none' ></textarea></td>
		</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>
                <input type=checkbox name='save_mail' id='save_mail' value='1' align=absmiddle>
                <label for='save_mail'>메일함에 저장하기</label>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                    $help_text .= "
                    <input type=image src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle >";
                }else{
                    $help_text .= "
                    <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_send.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle ></a>";
                }
                $help_text .= "
            </td>
        </tr>
	</table>
	</div>";

$Contents .= "".HelpBox($select, $help_text, 400)."</form>";


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
		/* 2014-07-06 SMS 이메일 발송 주석처리 해서 임시 주석처리! Hong*/
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
	    $('#product_type').click(function() {
	        if($(this).is(':checked')){
	            $('#gift_type').attr('disabled',false);
	            $('#choice_gift').attr('disabled',false);
	        }else{
	            $('#gift_type').attr('disabled',true);
	            $('#choice_gift').attr('disabled',true);
	        }
	    });
	    
	    $('#selectExcelDownLoad').on('click',function(){	       
	        var oet_ix = $('select[name=oet_ix] :selected').val();
	        var oidCheckBool = false;
	        var oid_array = [];
	        $('input:checkbox[name^=oid]').each(function(){	           
	           if($(this).is(':checked') == true){
	               oidCheckBool = true;
	               oid_array.push($(this).val());
	           } 
	        });
	        if(oet_ix){
	            if(oidCheckBool){


							var ig_now = new Date();   //현재시간
							var ig_hour = ig_now.getHours();   //현재 시간 중 시간.



								//	새벽시간(23시~07시), 휴무일(일, 토)
							if(Number(ig_hour) >= \"23\" || Number(ig_hour) <= \"7\" || Number(ig_now.getDay()) == \"0\" || Number(ig_now.getDay()) == \"6\") {
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
														$('#select_excel').find('input[name=oet_ix]').val(oet_ix);
														$('#select_excel').find('input[name=oid_array]').val(oid_array);
														$('#select_excel').find('input[name=irs]').val(ig_inputString);
														$('#select_excel').find('input[name=ipw]').val(ig_inputString_PW);
														$('#select_excel').submit();
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
							} else {
								//	일반 업무때 다운로드
								var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\\r\\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

									if(ig_inputString_PW != null && ig_inputString_PW.trim() != '') {

										var str_PW_length = ig_inputString_PW.length;		// 전체길이

										if(str_PW_length > \"15\") {
											alert('비밀번호를 15자 이하로 해주세요.');
											return false;
										} else {
											$('#select_excel').find('input[name=oet_ix]').val(oet_ix);
											$('#select_excel').find('input[name=oid_array]').val(oid_array);
											$('#select_excel').find('input[name=irs]').val(ig_inputString);
											$('#select_excel').find('input[name=ipw]').val(ig_inputString_PW);
											$('#select_excel').submit();
										}

									} else {
										alert('비밀번호를 입력해 주세요.');
										return false;
									}
							}




	            }else{
	                alert('하나 이상의 주문을 선택 해 주세요');    
	            }
	        }else{
	            alert('엑셀양식을선택해주세요');
	        }
	    });
	    
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




$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value='' />
</form>

<form name='select_excel' id='select_excel' method='post' action='../order/orders_excel2003.php'> 
    <input type='hidden' name='excel_type' value='select' >
    <input type='hidden' name='oet_ix' value='' >
    <input type='hidden' name='view_type' value='$view_type' >
    <input type='hidden' name='oid_array' value='' >


	<input type='hidden' name='ipw' id='ipw_id' value='' >
	<input type='hidden' name='irs' id='irs_id' value='' >



</form>
";
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


//	웰숲 우클릭 방지
include_once("./wel_drag.php");
?>



<script type="text/javascript">
	//	wel_ 새벽시간(23시~07시)이나 휴무일 등 업무시간 외 다운로드시 검수 member_excel2003
	function ig_excel_dn_chk(s_val_Data) {
		//console.log(s_val_Data);
		var ig_now = new Date();   //현재시간
		var ig_hour = ig_now.getHours();   //현재 시간 중 시간.



			//	새벽시간(23시~07시), 휴무일(일, 토)
		//if(Number(ig_hour) >= "23" || Number(ig_hour) <= "7" || Number(ig_now.getDay()) == "0" || Number(ig_now.getDay()) == "6") {
			var ig_inputString = prompt('사유를 간략하게 입력하세요.\r\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

			if(ig_inputString != null && ig_inputString.trim() != "") {
				//	엑셀다운로드 진행

					var str_length = ig_inputString.length;		// 전체길이

					if(str_length > "20") {
						alert("사유가 20자 이상 입니다.");
						return false;
					} else {
						var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

							if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

								var str_PW_length = ig_inputString_PW.length;		// 전체길이

								if(str_PW_length > "15") {
									alert("비밀번호를 15자 이하로 해주세요.");
									return false;
								} else {
									location.href = s_val_Data+"&irs="+ig_inputString+"&ipw="+ig_inputString_PW;
								}

							} else {
								alert("비밀번호를 입력해 주세요.");
								return false;
							}
					}


			} else {
				alert("사유를 입력하세요");
				return false;
			}
		/*} else {
			//	일반 업무때 다운로드
			var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

				if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

					var str_PW_length = ig_inputString_PW.length;		// 전체길이

					if(str_PW_length > "15") {
						alert("비밀번호를 15자 이하로 해주세요.");
						return false;
					} else {
						location.href = s_val_Data+"&ipw="+ig_inputString_PW;
					}

				} else {
					alert("비밀번호를 입력해 주세요.");
					return false;
				}
		}*/



	}
</script>