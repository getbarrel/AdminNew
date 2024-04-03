<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2020-02-12
 * Time: 오후 3:30
 */
@set_time_limit(0);
include $_SERVER["DOCUMENT_ROOT"]."/class/database.class";

$db = new database;

$today = date('Y-m-d');

$sql = "select od_ix from shop_order_detail where status = 'BF' and bf_date BETWEEN  '".$today." 00:00:00' and '".$today." 23:59:59' ";
$db->query($sql);
$order_data = $db->fetchall();

if(is_array($order_data) && count($order_data) > 0){
    foreach($order_data as $key=>$val){
        //구매 확정 시 크리마 연동 처리
        cremaSend($val['od_ix']);
    }
}





function cremaSend($od_ix){
    $ch = curl_init(); // 리소스 초기화

    $url = "http://stg.barrelmade.co.kr/shop/crema/orderReSend?od_ix=".$od_ix;

    // 옵션 설정
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);

    $output = curl_exec($ch); // 데이터 요청 후 수신

    //echo $output;

    curl_close($ch);  // 리소스 해제
    //
}