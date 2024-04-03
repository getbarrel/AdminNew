<?php
include("../class/layout.class");
//include("../order/excel_out_columsinfo.php");
include("../include/phpexcel/Classes/PHPExcel.php");

ini_set('memory_limit','2048M');
set_time_limit(9999999);

//error_reporting(E_ALL);
PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

date_default_timezone_set('Asia/Seoul');

$db = new Database;
$odb = new Database;

if($act == 'excel_down') {
    if ($search_searialize_value) {
        $unserialize_search_value = unserialize(urldecode($search_searialize_value));
        //print_r ($unserialize_search_value);
        //exit;
        extract($unserialize_search_value);
    }
    if($type_param_value){
        $fix_type = unserialize(urldecode($type_param_value));
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

}


if(empty($oet_ix)){
	echo "<script type='text/javascript'>
	<!--
		alert('잘못된 접근입니다.');
		history.back();
	//-->
	</script>";
	exit;
}else{
	$sql = "select * from shop_order_excel_template where oet_ix = '".$oet_ix."'";
	$db->query($sql);
	$db->fetch();

	// 데이터는 정보 없으면 2줄부터 시작
	if($db->dt["oet_line"]){
		$rownum = $db->dt["oet_line"];
	}else{
		$rownum = 2;
	}


	$columsinfo=unserialize(urldecode($db->dt["oet_array"]));
}

// var_dump($columsinfo);exit;


if($view_type=="sc_order") $not_in_product_type=$shop_product_type;
else $not_in_product_type=$sns_product_type;

if($excel_type == 'select'){
    $oid_array_data = explode(',',$oid_array);
    if(is_array($oid_array_data)){
        $where = "where o.oid = od.oid and o.oid in ('".implode("','",$oid_array_data)."')";
        $orderby = " o.order_date DESC ";
    }else{


        echo "<script type='text/javascript'>
        <!--
            alert('선택된 주문이 존재하지 않습니다.');
            history.back();
        //-->
        </script>";
        exit;
    }

}else {

    $where = "WHERE od.status !='SR' AND od.product_type NOT IN (" . implode(',', $not_in_product_type) . ") ";

    if($act == 'excel_down' && $excel_down_type == '2' ){
        if(count($oid) > 0){
            $where .= " and od.oid in ('".implode("','",$oid)."')";
        }else if(count($od_ix) > 0){
            $where .= " and od.od_ix in ('".implode("','",$od_ix)."')";
        }

    }

    if ($view_type == 'offline_order') {        //영업관리 용도 2013-07-05 이학봉
        $where .= " and od.order_from in ('offline') ";
        $ood_where .= " and od.order_from in ('offline') ";
    } elseif ($view_type == 'inventory') {
        $where = "WHERE od.status != 'SR' AND od.stock_use_yn = 'Y' and od.gu_ix !='0' ";
        $folder_name = "product";
    } else if ($view_type == 'pos_order') {        //포스관리 용도 2013-07-05 이학봉
        $where .= " and od.order_from in ('pos') ";
        $ood_where .= " and od.order_from in ('pos') ";
    }

    if (!$date_type) {
        $date_type = "o.order_date";
    }

    if ($orderdate) {
        //$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
        $where .= "and " . $date_type . " between '" . $startDate . " 00:00:00' and '" . $endDate . " 23:59:59' ";
    } else {
        if (!$startDate) {
            //$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".date("Y-m-d", time()-84600*30)."' and '".date("Y-m-d")."' ";
            $where .= "and " . $date_type . " between '" . date("Y-m-d", time() - 84600 * 30) . " 00:00:00' and '" . date("Y-m-d") . " 23:59:59' ";
        }
    }


    if (is_array($type)) {
        for ($i = 0; $i < count($type); $i++) {
            if ($type[$i]) {
                if ($type_str == "") {
                    $type_str .= "'" . $type[$i] . "'";
                } else {
                    $type_str .= ", '" . $type[$i] . "' ";
                }
            }
        }

        if ($type_str != "") {
            $where .= "and od.status in ($type_str) ";
        }
    } else {
        if ($type) {
            $where .= "and od.status = '$type' ";
        }
    }

    /*
    if(is_array($type)){
        for($i=0;$i < count($type);$i++){
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
    */

    if (is_array($refund_type)) {
        for ($i = 0; $i < count($refund_type); $i++) {
            if ($refund_type[$i]) {
                if ($refund_type_str == "") {
                    $refund_type_str .= "'" . $refund_type[$i] . "'";
                } else {
                    $refund_type_str .= ", '" . $refund_type[$i] . "' ";
                }
            }
        }

        if ($refund_type_str != "") {
            $where .= "and od.refund_status in ($refund_type_str) ";
        }
    } else {
        if ($refund_type) {
            $where .= "and od.refund_status = '$refund_type' ";
        }
    }

    if (is_array($order_from)) {
        for ($i = 0; $i < count($order_from); $i++) {
            if ($order_from[$i] != "") {
                if ($order_from_str == "") {
                    $order_from_str .= "'" . $order_from[$i] . "'";
                } else {
                    $order_from_str .= ",'" . $order_from[$i] . "' ";
                }
            }
        }
        if ($order_from_str != "") {
            $where .= "and od.order_from in ($order_from_str) ";
        }
    } else {
        if ($order_from) {
            $where .= "and od.order_from = '$order_from' ";
        }
    }

    if (is_array($payment_agent_type)) {
        for ($i = 0; $i < count($payment_agent_type); $i++) {
            if ($payment_agent_type[$i] != "") {
                if ($pay_agent_str == "") {
                    $pay_agent_str .= "'" . $payment_agent_type[$i] . "'";
                } else {
                    $pay_agent_str .= ",'" . $payment_agent_type[$i] . "' ";
                }
            }
        }

        if ($pay_agent_str != "") {
            $where .= "and o.payment_agent_type in ($pay_agent_str) ";
        }
    } else {
        if ($payment_agent_type) {
            $where .= "and o.payment_agent_type = '$payment_agent_type' ";
        }
    }

    if ($product_type != "") {
        $where .= "and od.product_type = '" . $product_type . "'";
    }

    if ($pre_type != ORDER_STATUS_DELIVERY_READY) {
        if (is_array($delivery_status)) {
            for ($i = 0; $i < count($delivery_status); $i++) {
                if ($delivery_status[$i] != "") {
                    if ($delivery_status_str == "") {
                        $delivery_status_str .= "'" . $delivery_status[$i] . "'";
                    } else {
                        $delivery_status_str .= ", '" . $delivery_status[$i] . "' ";
                    }
                }
            }

            if ($delivery_status_str != "") {
                $where .= "and od.delivery_status in ($delivery_status_str) ";
            }
        } else {
            if ($delivery_status) {
                $where .= "and od.delivery_status = '$delivery_status' ";
            }
        }
    } else {
        if ($_COOKIE[view_wdr_order] == 1) {
            $where .= "and (od.delivery_status not in ('WDA','WDO','WDP','WDC') or od.delivery_status is null or delivery_status ='')";
        } else {
            $where .= "and (od.delivery_status not in ('WDA','WDO','WDP','WDR','WDC') or od.delivery_status is null or delivery_status ='')";
        }
    }

    if ($md_code != "") {
        $where .= "and od.md_code = '" . $md_code . "'";
    }

    if (is_array($refund_method)) {
        for ($i = 0; $i < count($refund_method); $i++) {
            if ($refund_method[$i]) {
                if ($refund_method_str == "") {
                    $refund_method_str .= "'" . $refund_method[$i] . "'";
                } else {
                    $refund_method_str .= ", '" . $refund_method[$i] . "' ";
                }
            }
        }

        if ($refund_method_str != "") {
            $where .= "and o.refund_method in ($refund_method_str) ";
        }
    } else {
        if ($refund_method) {
            $where .= "and o.refund_method = '$refund_method' ";
        }
    }

    $sql = "SELECT company_id FROM common_company_detail where com_type='A' ";
    $db->query($sql);
    $db->fetch();
    $a_company_id = $db->dt[company_id];

    if ($use_reserve == "Y") {
        $where .= "and o.use_reserve_price > 0 ";
    }

    if (is_array($p_admin) && count($p_admin) == 1) {
        if ($p_admin[0] == "A") {
            $where .= "and od.company_id ='" . $a_company_id . "' ";
        } elseif ($p_admin[0] == "S") {
            $where .= "and od.company_id !='" . $a_company_id . "' ";
        }
    } else {
        if ($p_admin == "A") {
            $where .= "and od.company_id ='" . $a_company_id . "' ";
        } elseif ($p_admin == "S") {
            $where .= "and od.company_id !='" . $a_company_id . "' ";
        }
    }

    if ($view_type != 'inventory') {
        if ($invoice_no_bool == "Y") {
            $where .= " and ifnull(od.invoice_no,'') !='' ";
        } elseif ($invoice_no_bool == "N") {
            $where .= " and ifnull(od.invoice_no,'') ='' ";
        }
    }

    if ($view_type == 'inventory') {
        if ($order_cnt == '1') {
            $where .= " and (select count(odd.od_ix) as cnt from shop_order_detail as odd where odd.stock_use_yn = 'Y' and odd.oid = od.oid) = 1";
        } else if ($order_cnt == '2') {
            $where .= " and (select count(odd.od_ix) as cnt from shop_order_detail as odd where odd.stock_use_yn = 'Y' and odd.oid = od.oid) > 1";
        }
    }

    if ($stock_use_yn != "") {
        $where .= " and od.stock_use_yn = '" . $stock_use_yn . "' ";
    }

    if ($mall_ix != "") {
        $where .= "and od.mall_ix = '" . $mall_ix . "'";
    }

    if ($gp_ix != "") {
        $where .= "and o.gp_ix = '" . $gp_ix . "'";
    }

    if (is_array($cid)) {
        for ($i = 0; $i < count($cid); $i++) {
            if ($cid[$i] != "") {
                if ($cid_str == "") {
                    $cid_str .= "'" . $cid[$i] . "'";
                } else {
                    $cid_str .= ", '" . $cid[$i] . "' ";
                }
            }
        }

        if ($cid_str != "") {
            $where .= "and od.cid in ($cid_str) ";
        }
    } else {
        if ($cid) {
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

    if ($is_check_delivery != "") {
        $where .= "and od.is_check_delivery = '" . $is_check_delivery . "'";
    }

    $left_join = "";

    if (is_array($method)) {
        for ($i = 0; $i < count($method); $i++) {
            if ($method[$i] != "") {
                if ($method_str == "") {
                    $method_str .= "'" . $method[$i] . "'";
                } else {
                    $method_str .= ", '" . $method[$i] . "' ";
                }
            }
        }
        if ($method_str != "") {
            $where .= "and op.method in ($method_str) ";
            $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method in ($method_str)) ";
        }
    } else {
        if ($method) {
            $where .= "and op.method = '$method' ";
            $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.method = '$method') ";
        }
    }

    if ($pre_type == ORDER_UNRECEIVED_CLAIM) {
        $left_join .= " right join shop_order_unreceived_claim uc on (uc.od_ix=od.od_ix) and uc.claim_status = '" . ORDER_UNRECEIVED_CLAIM . "' ";
    }


    if ($search_type && $search_text) {


        if ($mult_search_use == '1') {    //다중검색 체크시 (검색어 다중검색)
            //다중검색 시작 2014-04-10 이학봉
            if ($search_text != "") {

                if (strpos($search_text, ",") !== false) {
                    $search_array = explode(",", $search_text);
                    $search_array = array_filter($search_array, create_function('$a', 'return preg_match("#\S#", $a);'));

                    $where .= "and $search_type in ( ";
                    $count_where .= "and $search_type in ( ";

                    for ($i = 0; $i < count($search_array); $i++) {
                        if ($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                            $search_array[$i] = format_phone(trim($search_array[$i]));
                        } else {
                            $search_array[$i] = trim($search_array[$i]);
                        }
                        if ($search_array[$i]) {
                            if ($i == count($search_array) - 1) {
                                $where .= "'" . trim($search_array[$i]) . "'";
                                $count_where .= "'" . trim($search_array[$i]) . "'";
                            } else {
                                $where .= "'" . trim($search_array[$i]) . "' , ";
                                $count_where .= "'" . trim($search_array[$i]) . "' , ";
                            }
                        }
                    }
                    $where .= ")";
                    $count_where .= ")";
                } else if (strpos($search_text, "\n") !== false) {//\n
                    $search_array = explode("\n", $search_text);
                    $search_array = array_filter($search_array, create_function('$a', 'return preg_match("#\S#", $a);'));

                    $where .= "and $search_type in ( ";
                    $count_where .= "and $search_type in ( ";

                    for ($i = 0; $i < count($search_array); $i++) {
                        if ($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                            $search_array[$i] = format_phone(trim($search_array[$i]));
                        } else {
                            $search_array[$i] = trim($search_array[$i]);
                        }
                        if ($search_array[$i]) {
                            if ($i == count($search_array) - 1) {
                                $where .= "'" . trim($search_array[$i]) . "'";
                                $count_where .= "'" . trim($search_array[$i]) . "'";
                            } else {
                                $where .= "'" . trim($search_array[$i]) . "' , ";
                                $count_where .= "'" . trim($search_array[$i]) . "' , ";
                            }
                        }
                    }
                    $where .= ")";
                    $count_where .= ")";
                } else {
                    if ($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                        $where .= " and " . $search_type . " = '" . format_phone(trim($search_text)) . "'";
                        $count_where .= " and " . $search_type . " = '" . trim($search_text) . "'";
                    } else {
                        $where .= " and " . $search_type . " = '" . trim($search_text) . "'";
                        $count_where .= " and " . $search_type . " = '" . trim($search_text) . "'";
                    }
                }

                if (substr_count($search_type, 'odd.') > 0) {
                    $left_join .= " left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix) ";
                }
                if (substr_count($search_type, 'op.') > 0) {
                    if (substr_count($left_join, 'shop_order_payment op') == 0) {
                        $left_join .= " left join shop_order_payment op on (op.oid=od.oid) ";
                    }
                }
            }
        } else {

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

            if (substr_count($search_type, 'op.') > 0) {
                if (substr_count($left_join, 'shop_order_payment op') == 0) {
                    $left_join .= " left join shop_order_payment op on (op.oid=od.oid) ";
                }
            }
        }
    }

    if ($send_type != "") {
        $where .= "and odd.send_type = '$send_type' ";
    }

    if ($bank != "") {
        $where .= "and op.bank = '$bank' ";
        if (substr_count($left_join, 'shop_order_payment op') == 0) {
            $left_join .= " left join shop_order_payment op on (op.oid=od.oid and op.bank = '$bank') ";
        }
    }

    if ($reason_code != "") {
        $where .= "and os.reason_code = '" . $reason_code . "' ";
        $left_join .= " left join shop_order_status os on (os.od_ix=od.od_ix and os.reason_code = '" . $reason_code . "') ";
    }


    if ($admininfo[admin_level] == 9) {
        if ($company_id != "") {
            $where .= " and o.oid = od.oid and od.company_id = '" . $company_id . "'";
        } else {
            $where .= " and o.oid = od.oid ";
        }
    } else if ($admininfo[admin_level] == 8) {
        $where .= " and o.oid = od.oid and od.company_id = '" . $admininfo[company_id] . "'";
    } else {
        $where .= " and o.oid = od.oid ";
    }

    $orderby = " o.order_date DESC ";
    if ($excel_type == "delivery") {
        if ($view_type_sub == "due_date") {
            $where .= " AND od.due_date >= " . date("Ymd");
            $orderby = " od.due_date ASC, od.od_ix ASC";
        }
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

}


$ORDER_FROM["self"]="자체쇼핑몰";
$ORDER_FROM["offline"]="오프라인 영업";
$ORDER_FROM["pos"]="POS";

$db->query("select site_name,site_code from sellertool_site_info ");
if($db->total){
	$sellertool=$db->fetchall("object");
	foreach($sellertool as $st){
		$ORDER_FROM[$st["site_code"]]=$st["site_name"];
	}
}

$sql = "SELECT 
		o.oid as oid,
		od.od_ix as od_ix,
		od.cid as cid,
		od.pid as pid,
		od.gid as gid,
		od.rfid,
		od.pcode as pcode,        
		od.pname as pname_1,
		CONCAT(od.pname,' ',IFNULL(od.set_name,''),' ',IFNULL(od.sub_pname,''),'(',od.option_text,')') AS pname_2,
		CONCAT(od.pname,' ',IFNULL(od.set_name,''),' ',IFNULL(od.sub_pname,''),'(',od.option_text,'/',od.pcnt,'개)') AS pname_3,
		CONCAT(replace(replace(replace(od.option_text,'옵션 : ',''),'옵션선택 : ',''),'옵션:사이즈 : ',''),' (+', FORMAT(IFNULL(od.option_price,0),0) ,'원)') AS optiontext_1,
		replace(replace(replace(od.option_text,'옵션 : ',''),'옵션선택 : ',''),'옵션:사이즈 : ','') as optiontext_2,
		od.pcnt as pcnt,
		od.psprice as psprice,
		(case when od.origin_sellprice > 0 then od.origin_sellprice else od.listprice end)  as sellprice,
		o.order_date as order_date,
		od.company_name as company_name,
		o.bname as bname, 
		o.buserid as buserid,
		o.age as age,
		o.sex as sex,
		o.bmail as bmail, 
		o.mem_group as mem_group,
		o.btel as btel,
		o.bmobile as bmobile,
		odd.rname as rname,
		odd.rtel as rtel,
		odd.rmobile as rmobile,
		(case when LENGTH(REPLACE(odd.zip,'-','')) = 5 then REPLACE(odd.zip,'-','') else '' end) as zip_new,
		(case when LENGTH(REPLACE(odd.zip,'-','')) > 5 then REPLACE(odd.zip,'-','') else '' end) as zip_1,
		concat(CAST(substr(REPLACE(odd.zip,'-',''),1,3) as CHAR),'-',CAST(substr(REPLACE(odd.zip,'-',''),4,3) as CHAR)) as zip_2,
		CONCAT(IFNULL(odd.addr1,''),' ',IFNULL(odd.addr2,'')) as addr,
		odd.addr1 as addr_1,
		odd.addr2 as addr_2,
		od.invoice_no as invoice_no,
		case when IFNULL(od.msgbyproduct,'') !='' then od.msgbyproduct else odd.msg end as msg,
		od.delivery_pay_method as delivery_pay_method,
		od.delivery_method as delivery_method,
		o.total_price as order_total_price,
		od.pt_dcprice as order_payment_price,
		od.coprice as product_coprice,
		(od.ptprice - od.pt_dcprice) as product_dc_price,
		od.pt_dcprice as product_pt_price,
		(case when od.product_type != 77 then od.add_info else '' end)  as add_info,
		(SELECT payment_price FROM shop_order_payment where oid=o.oid and pay_type='G' and method='".ORDER_METHOD_RESERVE."') as use_reserve,		
		
		";
		//2015-12-14 Hong 코웰 요청으로 인한 수정 o.payment_price as order_payment_price,

		//서브쿼리 필요할떄만 들어게가게끔 처리!
		for ($j=0,$col='A'; $j < count($columsinfo); $j++,$col++){
			if($columsinfo[$col]["code"]=="quick"){
				$sql .= "
				(SELECT code_name FROM ".TBL_SHOP_CODE." where code_gubun = '02' and code_ix = od.quick) as quick,";
			}elseif($columsinfo[$col]["code"]=="delivery_price"){
				$sql .= "
				(case when od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix=od.ode_ix) then odv.delivery_dcprice else '0' end) as delivery_price,";
			}elseif($columsinfo[$col]["code"]=="product_dc_info"){
				$sql .= "
				(SELECT GROUP_CONCAT(CONCAT(dc_title,'-',FORMAT(dc_price,0),'원') SEPARATOR ',') FROM shop_order_detail_discount where oid=o.oid and od_ix=od.od_ix) as product_dc_info,";
			}elseif($columsinfo[$col]["code"]=="product_dc_coupon"){
				$sql .= "
				(SELECT dc_price FROM shop_order_detail_discount where oid=o.oid and od_ix=od.od_ix and dc_type='CP' limit 1) as product_dc_coupon,";
			}elseif($columsinfo[$col]["code"]=="product_dc_premium"){
				$sql .= "
				(case when o.gp_ix='2' and od.origin_sellprice > 0 then (od.origin_sellprice - od.psprice) when o.gp_ix='2' then (od.listprice - od.psprice) else 0 end) as product_dc_premium,";
			}elseif($columsinfo[$col]["code"]=="product_dc_app"){
				$sql .= "
				(SELECT dc_price FROM shop_order_detail_discount where oid=o.oid and od_ix=od.od_ix and dc_type='APP') as product_dc_app,";
			}elseif($columsinfo[$col]["code"]=="product_dc_admin"){
				$sql .= "
				(SELECT dc_price FROM shop_order_detail_discount where oid=o.oid and od_ix=od.od_ix and dc_type='MG') as product_dc_admin,";
			}elseif($columsinfo[$col]["code"]=="use_reserve"){
				/* 2015-12-14 무조건 호출이 되어야함
				$sql .= "
				(SELECT payment_price FROM shop_order_payment where oid=o.oid and pay_type='G' and method='".ORDER_METHOD_RESERVE."') as use_reserve,";
				*/
			}elseif($columsinfo[$col]["code"]=="use_saveprice"){
				$sql .= "
				(SELECT payment_price FROM shop_order_payment where oid=o.oid and pay_type='G' and method='".ORDER_METHOD_SAVEPRICE."') as use_saveprice,";
			}elseif($columsinfo[$col]["code"]=="method"){
				$sql .= "
				(SELECT GROUP_CONCAT(method SEPARATOR '|') FROM shop_order_payment where oid=o.oid and pay_type='G') as method,";
			}elseif($columsinfo[$col]["code"]=="charger_ix"){
				$sql .= "
				(SELECT AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name FROM common_member_detail where code=o.charger_ix) as charger_ix,";
			}elseif($columsinfo[$col]["code"]=="md_name"){
				$sql .= "
				(SELECT AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name FROM common_member_detail where code=od.md_code) as md_name,";
			}elseif($columsinfo[$col]["code"]=="namsa_basic_section"){
				$sql .= "
				(SELECT ps.section_name FROM inventory_goods_basic_place bp left join inventory_place_section ps on (bp.ps_ix=ps.ps_ix) where bp.company_id = '362ed8ee1cba4cc34f80aa5529d2fbcd' and bp.pi_ix = '1' and bp.gid=od.gid and bp.gu_ix=od.gu_ix ) as namsa_basic_section,";
			}elseif($columsinfo[$col]["code"]=="claim_apply_user" || $columsinfo[$col]["code"]=="claim_apply_msg" || $columsinfo[$col]["code"]=="claim_data_channel"){
				$sql .= "
				(SELECT CONCAT((CASE WHEN c_type='B' THEN '구매자' WHEN c_type='S' THEN '판매자' WHEN c_type='M' THEN 'MD' ELSE c_type END),'||',status_message,'||',(CASE WHEN data_channel='1' THEN '프론트' WHEN data_channel='2' THEN '백오피스' ELSE '' END)) FROM shop_order_status os where os.oid=od.oid and os.od_ix=od.od_ix and status in ('CA','CC','RA','RI','EA','EI') and reason_code!='' order by os.regdate desc limit 0,1) as claim_apply_info,";
			}elseif($columsinfo[$col]["code"]=="product_expect_ac_price" || $columsinfo[$col]["code"]=="product_ac_price"){
				$sql .= "
				(case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end) end) as product_expect_ac_price,";
			}elseif($columsinfo[$col]["code"]=="product_expect_dc_allotment_price" || $columsinfo[$col]["code"]=="product_ac_price"){
				$sql .= "
				IFNULL((case when od.account_type='3' or od.refund_status='FC' then '0' else (select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) end),'0') as product_expect_dc_allotment_price,";
			}elseif($columsinfo[$col]["code"]=="product_expect_fee_price" || $columsinfo[$col]["code"]=="product_ac_price"){
				$sql .= "
				IFNULL((case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then ((od.ptprice - (select IFNULL(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) end),'0') as product_expect_fee_price,";
			}else if($columsinfo[$col]["code"]=="tid"){
                $sql .= "(select tid from shop_order_payment where method='".ORDER_METHOD_NPAY."' and oid = o.oid and pay_type = 'G' limit 1) as tid, ";

            }else if($columsinfo[$col]["code"]=="gift_type"){
                $sql .= " od.gift_type, ";

            }else if($columsinfo[$col]["code"]=="choice_gift_order"){
                $sql .= " o.choice_gift_order, ";

            }else if($columsinfo[$col]["code"]=="choice_gift_prd"){
                $sql .= " od.choice_gift_prd, ";

            }
		}
		$sql .= "
		o.status as order_status,
		od.status as status,
		od.delivery_status as delivery_status,
		od.refund_status as refund_status,
		od.accounts_status as accounts_status,
		od.ic_date as ic_date,
		od.di_date as di_date,
		od.dc_date as dc_date,
		od.bf_date as bf_date,
		o.user_ip,
		od.order_from,
		od.co_oid as co_oid,
		od.co_od_ix as co_od_ix,
		od.co_delivery_no as co_delivery_no,
		
		SUBSTRING_INDEX(AES_DECRYPT(UNHEX(o.refund_bank),'".$db->ase_encrypt_key."'),'|',1) as refund_bank, 
		SUBSTRING_INDEX(AES_DECRYPT(UNHEX(o.refund_bank),'".$db->ase_encrypt_key."'),'|',-1) as refund_bank_account, 
		AES_DECRYPT(UNHEX(o.refund_bank_name),'".$db->ase_encrypt_key."') as refund_bank_owner,
		
		sodd.sbmall_pr_cd,
        sodd.sbmall_od_cd,
        sodd.sbmall_be_dt,
        sodd.exmall_vat_fg,
        sodd.exmall_umat_fg,
        sodd.exmall_tr_co,
        sodd.exmall_so_fg,
        sodd.exmall_shipreq_dt,
        sodd.exmall_mgmt_cd,
        sodd.exmall_exch_cd,
        sodd.exmall_due_dt,
        
        odd.country,
        odd.city,
        odd.state
	FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
	left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
	left join shop_order_delivery odv on (odv.oid=od.oid and odv.ode_ix = od.ode_ix)
	left join shop_product sp on (od.pid = sp.id)
	left join shop_order_excel_ex_info sodd on sodd.sodd_code = od.od_ix
	".$left_join."
	".$where."
	ORDER BY ".$orderby." ";


$db->query($sql);

if($_SESSION['admininfo']['admin_id'] == 'forbiz'){
   //  echo $sql;exit;
}

$ordersXL = new PHPExcel();

// 속성 정의

$ordersXL->getProperties()->setCreator("포비즈 코리아")
						 ->setLastModifiedBy("Mallstory.com")
						 ->setTitle("orders List")
						 ->setSubject("orders List")
						 ->setDescription("generated by forbiz korea")
						 ->setKeywords("mallstory")
						 ->setCategory("orders List");

if($db->total){
	
	$orders=$db->fetchall("object");
	$orders_total = count($orders);

	// 헤더 타이틀
	for ($i=0,$col='A'; $i < count($columsinfo); $i++,$col++){
		
		if($columsinfo[$col]["code"]=="DEFAULT")			$value_str = "공백(기본값)";
		else												$value_str = $columsinfo[$col]["text"];

		$ordersXL->getActiveSheet()->setCellValue($col . "1", $value_str);
	}
	
	$use_reserve_oid = array();
	$use_reserve_check = array();

	for ($j=0,$col='A'; $j < $orders_total; $j++,$rownum++,$col='A'){

		$orderDate=$orders[$j];
		$cate_text_array="";
		$ca_info_array="";

		for ($i=0; $i < count($columsinfo); $i++,$col++){
	
			if($columsinfo[$col]["code"]=="DEFAULT")			$value_str = $columsinfo[$col]["text"];
			else												$value_str = get_order_excel_colum_val($columsinfo[$col]["code"]);
			
			$value_str = str_replace("=","",$value_str);

			switch($columsinfo[$col]["code"])
			{
				case "pid":
				case "co_oid":
				case "co_od_ix":
				case "bmobile":
				case "rmobile":
				case "btel":
				case "rtel":
				case "product_seller_sale_type_code":
				case "product_seller_sale_type_margin":
				case "part_no":
				case "part_no_div":
				case "store_code":
				case "invoice_no":
					$ordersXL->getActiveSheet()->setCellValueExplicit($col . ($rownum), $value_str, PHPExcel_Cell_DataType::TYPE_STRING);
					break;
				case "rfid":
					$ordersXL->getActiveSheet()->setCellValueExplicit($col . ($rownum), str_replace(array("전체 > ","검색엔진 > "),"",getRefererCategoryPath2($value_str, 4)), PHPExcel_Cell_DataType::TYPE_STRING);
					break;
                case "refund_bank":
                    $ordersXL->getActiveSheet()->setCellValue($col . ($rownum), $arr_banks_name[$value_str]);
                    break;
                case "refund_bank_account":
                    $ordersXL->getActiveSheet()->setCellValue($col . ($rownum), $value_str, PHPExcel_Cell_DataType::TYPE_STRING);
                    break;
				default:

					if($columsinfo[$col]["code"]=="use_reserve"){
						if($use_reserve_oid[$orderDate['oid']]===true){
							$value_str = 0;
						}

						$use_reserve_oid[$orderDate['oid']] = true;
					}
					
					if($columsinfo[$col]["code"]=="order_payment_price"){
						if($use_reserve_check[$orderDate['oid']]!=true){
							$value_str -= $orderDate['use_reserve'];

							$use_reserve_check[$orderDate['oid']] = true;
						}
					}

                    if($columsinfo[$col]["code"]=="choice_gift_order" || $columsinfo[$col]["code"]=="choice_gift_prd"){
                        if($value_str == 'Y'){
                            $value_str = '선택';
                        }elseif($value_str == 'N'){
                            $value_str = '선택안함';
                        }else{
                            $value_str = '-';
                        }
                    }elseif($columsinfo[$col]["code"]=="gift_type"){
                        if($value_str == 'P'){
                            $value_str = '구매금액별 사은품';
                        }elseif($value_str == 'G'){
                            $value_str = '사은품';
                        }else{
                            $value_str = '일반상품';
                        }
                    }

                    $value_str = htmlspecialchars_decode($value_str, ENT_QUOTES);
					$ordersXL->getActiveSheet()->setCellValue($col . ($rownum), $value_str);
					break;
			}
		}
		
		//2014-10-28 Hong 메모리 최적화를 위해!
		unset($orders[$j]);
	}
}


$ordersXL->getActiveSheet()->setTitle('매출진행관리');
$ordersXL->setActiveSheetIndex(0);

if($excel_type == "delivery"){
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="배송정보_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');
}else{
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="주문목록_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');
}

$objWriter = PHPExcel_IOFactory::createWriter($ordersXL, 'Excel5');
//$objWriter = PHPExcel_IOFactory::createWriter($ordersXL, 'CSV');
//$objWriter->setUseBOM(true);
$objWriter->save('php://output');


function get_order_excel_colum_val($value){
	global $orderDate,$cate_text_array,$ca_info_array,$ORDER_FROM;
	
	if(substr_count($value,"cid_") > 0){
		//cid_0~cid_3 여러번 디비 호출을 막기 위해서!!
		if(empty($cate_text_array)){
			$cate_text_array = explode(">",getCategoryPathByAdmin($orderDate["cid"],'4'));
			$value_str = trim($cate_text_array[str_replace("cid_","",$value)]);
		}
		$value_str = trim($cate_text_array[str_replace("cid_","",$value)]);
	}elseif(substr_count($value,"pname_") > 0 || substr_count($value,"optiontext_") > 0 ){
		$value_str = strip_tags(str_replace(array("?","\n\r","\n","="),"",$orderDate[$value]));
	}elseif($value == "delivery_method"){
		$value_str = getDeliveryMethod($orderDate[$value]);
	}elseif($value == "method"){
		$value_str = getMethodStatus($orderDate[$value]);
	}elseif(in_array($value,array("order_status","status","delivery_status","refund_status","accounts_status"))){
		$value_str = strip_tags(getOrderStatus($orderDate[$value]));
	}elseif($value == "delivery_pay_method"){
		$value_str = getDeliveryPayType($orderDate[$value]);
	}elseif($value == "order_from"){
		$value_str = $ORDER_FROM[$orderDate[$value]];
	}elseif($value == "sex"){
		if($orderDate[$value]=="M"){
			$value_str = "남자";
		}elseif($orderDate[$value]=="W"){
			$value_str = "여자";
		}else{
			$value_str = "-";
		}
	}elseif($value=="claim_apply_user" || $value=="claim_apply_msg" || $value=="claim_data_channel"){
		if(empty($ca_info_array)){
			list($ca_info_array['claim_apply_user'],$ca_info_array['claim_apply_msg'],$ca_info_array['claim_data_channel']) = explode("||",$orderDate["claim_apply_info"]);
		}
		$value_str = trim($ca_info_array[$value]);
	}elseif($value == "product_ac_price"){
		$value_str = $orderDate['product_expect_ac_price'] - $orderDate['product_expect_dc_allotment_price'] - $orderDate['product_expect_fee_price'];
	}elseif($value == "bname2" || $value == "rname2"){
		$tmp_value = substr($value,0,-1);
		$f_value = mb_substr($orderDate[$tmp_value],0,1,'utf-8');
		$s_value = str_repeat ( '*', mb_strlen ( mb_substr($orderDate[$tmp_value],1,-1,'utf-8') , 'utf-8' ) );
		$t_value = mb_substr($orderDate[$tmp_value],-1,1,'utf-8');
		$value_str = $f_value . $s_value . $t_value;
	}else{
		$value_str = $orderDate[$value];
	}

	return $value_str;
}
