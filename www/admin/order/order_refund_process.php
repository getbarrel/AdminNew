<?php
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
include("../reseller/reseller.lib.php");
include("../inventory/inventory.lib.php");
include("../../include/cash_manage.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/receipt.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

include_once("../logstory/class/sharedmemory.class");

$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));

$db = new Database;
$mdb = new Database;

$and_company_id = '';
$od_ix_str = '';
$pre_type = ORDER_STATUS_INCOM_COMPLETE;

$sql = "SELECT * FROM shop_order_cancel WHERE process_yn = 'N'";
$db->query($sql);
$datas = $db->fetchall();
$total = $db->total;

if($total > 0) {

    for ($j = 0; $j < $total; $j++) {
        $od_ix = $datas[$j];
        if ($j == 0) $od_ix_str .= "'" . $od_ix['od_ix'] . "'";
        else                    $od_ix_str .= ",'" . $od_ix['od_ix'] . "'";
    }

    $sql = "select od.*,o.status as ostatus,o.user_code from " . TBL_SHOP_ORDER . " o ," . TBL_SHOP_ORDER_DETAIL . " od where o.oid=od.oid and od.od_ix in (" . $od_ix_str . ")  $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();
    $update_str = "";
    if ($pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_READY) {
        //$update_str .= " , delivery_status = '', quick = '', invoice_no = '' ";
    }

    for ($i = 0; $i < count($order_details); $i++) {

        if($order_details[$i][status] == 'CC' || $order_details[$i][status] == 'ED') {
            continue;
        }

        if ($order_details[$i][ostatus] == ORDER_STATUS_DEFERRED_PAYMENT) {//외상일때 환불 신청 X
            $update_str2 = "";
        } else {
            $update_str2 = " , refund_status='" . ORDER_STATUS_REFUND_APPLY . "'  , fa_date=NOW() ";
        }

        if ($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == ORDER_STATUS_DELIVERY_READY || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type == ORDER_STATUS_DEFERRED_PAYMENT || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_READY) {
            $STATUS_MESSAGE = "[" . fetch_order_status_div('IR', 'CA', "title", $reason_code) . "]" . $msg;
            $update_str2 .= " , claim_fault_type = '" . fetch_order_status_div('IR', 'CA', "type", $reason_code) . "' ";

            if ($pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_READY) {
                //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                $results = inventory_warehouse_move($order_details[$i]["gu_ix"], $order_details[$i]["pcnt"], $order_details[$i]["delivery_basic_ps_ix"], $order_details[$i]["delivery_ps_ix"], $_SESSION["admininfo"]["charger_ix"], $_SESSION["admininfo"]["charger"], "return", $order_details[$i]["oid"]);

                if ($results != "Y") {
                    echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                    exit;
                }
            }

        } else {
            $STATUS_MESSAGE = $msg;
        }

        $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set status = '" . ORDER_STATUS_CANCEL_COMPLETE . "'  , cc_date = NOW(), update_date = NOW() $update_str $update_str2 $am_update_str where   od_ix='" . $order_details[$i][od_ix] . "'   $and_company_id";
        $db->query($sql);


        set_order_status($order_details[$i][oid], $status, $STATUS_MESSAGE, $ADMIN_MESSAGE, $_SESSION["admininfo"]["company_id"], $order_details[$i][od_ix], $order_details[$i][pid], $reason_code);
        //적립한 마일리지 취소
        InsertReserveInfo($order_details[$i][user_code], $order_details[$i][oid], $order_details[$i][od_ix], $id, $reserve, '9', '2', $etc, 'mileage', $_SESSION["admininfo"]);
        //inventory.lib.php에

        if (($reason_code != "" && fetch_order_status_div('IR', 'CA', "type", $reason_code) == "S") || (empty($reason_code) && $order_details[$i]["claim_fault_type"] == "S")) {
            //S:판매자 책임 B:구매자 책임
            //입금후 취소시 셀러 판매신용점수 차감 (판매자귀책)
            //셀러판매신용점수 추가 시작 2014-06-15 이학봉
            InsertPenaltyInfo('2', '4', $order_details[$i][oid], $order_details[$i][od_ix], $penalty, $order_details[$i]["company_id"], '입금후취소 판매신용점수 차감', $_SESSION["admininfo"], 'cc');
            //셀러판매신용점수 추가 끝 2014-06-15 이학봉

            /*
            define("POINT_USE_STATE_IC","1"); // 입금완료
            define("POINT_USE_STATE_DC","2"); // 배송완료
            define("POINT_USE_STATE_BF","3"); // 구매확정
            define("POINT_USE_STATE_CC","4"); // 입금후 취소
            define("POINT_USE_STATE_EC","5"); // 교환확정
            define("POINT_USE_STATE_RC","6"); // 반품확정
            define("POINT_USE_STATE_DD","7"); // 입금완료후 발송지연
            define("POINT_USE_STATE_DDA","8"); // 입금완료후 추가 발송지연
            define("POINT_USE_STATE_ETC","9"); // 기타
            */
            insertProductPoint('2', POINT_USE_STATE_CC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금후취소 상품점수 차감', $_SESSION["admininfo"], 'cc');
        }

        UpdateSellingCnt($order_details[$i]);

        //후불 외상시 미수금 처리
        if ($order_details[$i][ostatus] == ORDER_STATUS_DEFERRED_PAYMENT) {
            $noaccept_data = "";
            $noaccept_data[oid] = $order_details[$i][oid];
            $noaccept_data[msg] = "<br/>-" . date('Ymd') . " " . $order_details[$i][pname] . " 취소";
            $noaccept_data[order_cancel_price] = $order_details[$i][ptprice] - $order_details[$i][member_sale_price] - $order_details[$i][use_coupon];
            setNoacceptOrderCancel($noaccept_data);
        }

        //쿠폰 돌려주기!!!
        if ($coupon_data[restore_cc2] == "Y") {
            $UseCoupon["oid"] = $order_details[$i][oid];
            $UseCoupon["od_ix"] = $order_details[$i][od_ix];
            $returnCoupon = orderUseCouponReturn($UseCoupon);
        }

        //제휴사 주문 상태 연동
        if (function_exists('sellerToolUpdateOrderStatus')) {
            sellerToolUpdateOrderStatus(ORDER_STATUS_CANCEL_COMPLETE, $order_details[$i][od_ix]);
        }

        $sql = "update shop_order_cancel set process_yn = 'Y' where od_ix = '".$order_details[$i][od_ix]."';";
        $db->query($sql);
    }

    $sql = "select *,pid as id from " . TBL_SHOP_ORDER_DETAIL . " where od_ix in (" . $od_ix_str . ") $and_company_id group by oid";
    $db->query($sql);
    $order_infos = $db->fetchall("object");

    for ($i = 0; $i < count($order_infos); $i++) {
        //2012-10-09 홍진영
        $mdb->query("select * from " . TBL_SHOP_ORDER . " WHERE oid='" . $order_infos[$i][oid] . "'");
        $order = "";
        $order = $mdb->fetch();

        $mdb->query("select *, pid as id from " . TBL_SHOP_ORDER_DETAIL . " WHERE oid='" . $order_infos[$i][oid] . "' and od_ix in (" . $od_ix_str . ") $and_company_id");
        $order_details = "";
        $order_details = $mdb->fetchall("object");

        if ($pre_type != ORDER_STATUS_CANCEL_APPLY) {
            $product_info = array();
            $product_com_info = array();
            foreach ($order_details as $key => $detail) {

                $product_com_info[$detail[company_id]] = $detail[delivery_type];

                $product_info[$key] = $detail;
                $product_info[$key][claim_type] = "C";
                $product_info[$key][claim_group] = "99";//클래임그룹임시로 99로 동일!
                $product_info[$key][claim_fault_type] = fetch_order_status_div('IR', 'CA', "type", $reason_code);//클래임책임자
                $product_info[$key][claim_apply_yn] = "Y";//요청상품
                $product_info[$key][claim_apply_cnt] = $detail[pcnt];//요청상품수량
            }

            $resulte = clameChangePriceCalculate($product_info);

            foreach ($product_com_info as $company_id => $delivery_type) {

                $sql = "select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='" . $order_infos[$i][oid] . "' ";
                $db->query($sql);
                $db->fetch();
                $claim_group = $db->dt["claim_group"];

                $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set claim_group = '" . $claim_group . "' where oid='" . $order_infos[$i][oid] . "' and od_ix in (" . $od_ix_str . ") and company_id='" . $company_id . "' $and_company_id ";
                //echo $sql."<br/>";
                $db->query($sql);

                $sql = "insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','" . $order_infos[$i][oid] . "','$company_id','$delivery_type','$claim_group','" . $resulte[delivery][$company_id][delivery_price] . "',NOW())";
                //echo $sql."<br/>";
                $db->query($sql);
            }
        } else {
            //ERP를 통해 취소요청이 들어올시 클레임 배송비 처리를 여기서함.
            $product_info = array();
            $product_com_info = array();
            foreach ($order_details as $key => $detail) {
                if ($detail['need_claim_yn'] == 'Y') {
                    $product_com_info[$detail[company_id]] = $detail[delivery_type];

                    $product_info[$key] = $detail;
                    $product_info[$key][claim_type] = "C";
                    $product_info[$key][claim_group] = "99";//클래임그룹임시로 99로 동일!
                    $product_info[$key][claim_fault_type] = fetch_order_status_div('IR', 'CA', "type", "DD");//클래임책임자
                    $product_info[$key][claim_apply_yn] = "Y";//요청상품
                    $product_info[$key][claim_apply_cnt] = $detail[pcnt];//요청상품수량
                }
            }

            if (count($product_info) > 0) {
                $resulte = clameChangePriceCalculate($product_info);

                foreach ($product_com_info as $company_id => $delivery_type) {

                    $sql = "select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='" . $order_infos[$i][oid] . "' ";
                    $db->query($sql);
                    $db->fetch();
                    $claim_group = $db->dt["claim_group"];

                    $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set claim_group = '" . $claim_group . "' where oid='" . $order_infos[$i][oid] . "' and od_ix in (" . $od_ix_str . ") and company_id='" . $company_id . "' $and_company_id ";
                    //echo $sql."<br/>";
                    $db->query($sql);

                    $sql = "insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','" . $order_infos[$i][oid] . "','$company_id','$delivery_type','$claim_group','" . $resulte[delivery][$company_id][delivery_price] . "',NOW())";
                    //echo $sql."<br/>";
                    $db->query($sql);
                }
            }
        }

    }
}



