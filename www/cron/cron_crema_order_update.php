<?php

include_once("../class/layout.class");
include_once("./crema_lib.php");


/**
 * use exmple
$crema = new CremaHandler();
$data = $crema->getBrands();
print_r($data);
 *
 */

$crema = new CremaHandler();

$sdate = date('Y-m-d 00:00:00', strtotime('-1 day'));
$edate = date('Y-m-d 23:59:59', strtotime('-1 day'));

$db->query(
    "SELECT `o`.`oid`, `o`.`order_date`, `o`.`payment_price`, `o`.`buserid`, `o`.`bname`, `o`.`bmobile`, `o`.`bmail`, `o`.`gp_ix`, `o`.`payment_agent_type`
    FROM `shop_order` as `o`
    JOIN `shop_order_detail` as `od` on (o.oid = od.oid)
    WHERE od.bf_date BETWEEN '".$sdate."' AND '".$edate."' AND o.buserid IS NOT NULL  
    GROUP BY o.oid                 
    ORDER BY `bf_date` ASC    
    ");

$rows = $db->fetchall();


if($rows) {

    $oid_in = array_column($rows, 'oid');
    $oid_in = implode("','", $oid_in);

    // BF  구매확정 ,
    $db->query(
        "SELECT `oid`, `od_ix`, `pid`, `pt_dcprice`
    FROM `shop_order_detail`
    WHERE `oid` IN( '" . $oid_in . "')    
    ORDER BY `oid` DESC");
    $rows2 = $db->fetchall();


    foreach ($rows2 as $orderDetail) {
        foreach ($rows as $key => $value) {
            if ($orderDetail['oid'] == $value['oid']) {
                $rows[$key]['orderDetail'][] = $orderDetail;
            }
        }
    }

    //crema api
    $crema = new CremaHandler();

    foreach ($rows as $key => $val) {

        if ($val['payment_agent_type'] == "W") {
            $device = 'pc';
        } else {
            $device = 'mobile';
        }

        //구매자 아이디가 필수
        if ($val['buserid']) {
            $param = array('code' => $val['oid']
            , 'created_at' => date('Y-m-d\TH:i:sO', strtotime($val['order_date']))
            , 'total_price' => $val['payment_price'] //주문 실결제금액(쿠폰, 적립금을 제외한 결제 금액)
            , 'user_code' => $val['buserid']
            , 'user_name' => $val['bname']
            , 'user_phone' => $val['bmobile']
            , 'user_email' => $val['bmail']
            , 'user_grade_id' => $val['gp_ix']
            , 'store_name' => null //오프라인 매장명
            , 'order_device' => $device
            );

            $data = $crema->putOrder($param);


            if (isset($val['orderDetail']) && isset($data['id'])) {
                foreach ($val['orderDetail'] as $od) {
                    $sub_param = array('order_id' => $data['id'] //crema insert id
                    , 'code' => $od['od_ix']
                    , 'product_code' => (int)$od['pid']
                    , 'price' => $od['pt_dcprice']
                    , 'status' => 'delivery_finished'
                    );
                    $crema = new CremaHandler();
                    $sub_data = $crema->postSubOrder($sub_param);
                }
            }
        }
    }
}



function array_column($array, $columnKey, $indexKey = null)
{
    $result = array();
    foreach ($array as $subArray) {
        if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
            $result[] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
        } elseif (array_key_exists($indexKey, $subArray)) {
            if (is_null($columnKey)) {
                $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                $result[$index] = $subArray;
            } elseif (array_key_exists($columnKey, $subArray)) {
                $index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
                $result[$index] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
            }
        }
    }
    return $result;
}

?>